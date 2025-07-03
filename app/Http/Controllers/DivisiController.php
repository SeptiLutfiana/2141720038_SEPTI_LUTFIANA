<?php

namespace App\Http\Controllers;

use App\Exports\DivisiExport;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DivisiImport;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Carbon;

class DivisiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $divisi = Divisi::when($search, function ($query, $search) {
            return $query->where('nama_divisi', 'like', "%$search%")
                ->orWhere('keterangan', 'like', "%$search%");
        })
            ->orderBy('nama_divisi')
            ->paginate(5)
            ->withQueryString(); // agar ?search=... tetap terbawa saat paging

        return view('adminsdm.data-master.karyawan.divisi.index', [
            'divisi' => $divisi,
            'search' => $search,
            'type_menu' => 'data-master',
        ]);
    }
    public function create()
    {
        return view('adminsdm.data-master.karyawan.divisi.create', [
            'type_menu' => 'data-master',
        ]);
    }
    public function store(Request $request)
    {
        // Cek apakah user menggunakan form input manual
        if ($request->filled('input_manual')) {
            // Validasi untuk input manual
            $request->validate([
                'nama_divisi' => 'required|string',
                'keterangan' => 'required|string',
            ], [
                'nama_divisi.required' => 'Nama Divisi harus diisi',
                'keterangan.required' => 'Keterangan harus diisi',
            ]);

            // Simpan data divisi ke dalam database
            Divisi::create([
                'nama_divisi' => $request->nama_divisi,
                'keterangan' => $request->keterangan,
            ]);

            // Redirect ke halaman Data Divisi dengan pesan sukses
            return redirect()->route('adminsdm.data-master.karyawan.divisi.index')
                ->with('msg-success', 'Berhasil menambahkan data Divisi ' . $request->nama_divisi);
        }

        // Jika user memilih upload file
        // if ($request->hasFile('file_import')) {
            // Validasi file upload (CSV atau XLSX dengan ukuran maksimal 10MB)
            $request->validate([
                'file_import' => 'required|mimes:xlsx,csv|max:10240', // Maksimal 10MB
            ], [
                'file_import.required' => 'File harus diupload.',
                'file_import.mimes' => 'File harus berformat .xlsx atau .csv.',
                'file_import.max' => 'Ukuran file maksimal 10MB.',
            ]);

            try {
                // Proses impor data dari file (gunakan paket Laravel Excel)
                Excel::import(new DivisiImport, $request->file('file_import'));

                // Redirect ke halaman Data Divisi dengan pesan sukses
                return redirect()->route('adminsdm.data-master.karyawan.divisi.index')
                    ->with('msg-success', 'Berhasil mengimpor data divisi dari file.');
            } catch (\Exception $e) {
                // Jika ada error saat mengimpor, tangani dan tampilkan pesan error
                return redirect()->back()->with('msg-error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
            }
        // }

        // Kalau tidak ada data input manual atau upload file
        return redirect()->back()->with('msg-error', 'Tidak ada data yang dikirim.');
    }


    public function show($id)
    {
        // Mengambil data Divisi berdasarkan ID
        $divisi = Divisi::findOrFail($id);

        return view('adminsdm.data-master.karyawan.divisi.detail', [
            'divisi'    => $divisi,
            'type_menu' => 'data-master',
        ]);
    }
    public function edit($id)
    {
        $divisi = Divisi::findOrFail($id);
        return view('adminsdm.data-master.karyawan.divisi.edit', [
            'divisi'    => $divisi,
            'type_menu' => 'data-master',
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_divisi' => 'required|string',
            'keterangan' => 'required|string',
        ], [
            'nama_divisi.required' => 'Nama Divisi harus diisi',
        ]);

        // Mengambil data Divisi berdasarkan ID
        $divisi = Divisi::findOrFail($id);

        // Melakukan update data divisi
        $divisi->update($request->all());

        return redirect()->route('adminsdm.data-master.karyawan.divisi.index')
            ->with('msg-success', 'Berhasil mengubah data Divisi ' . $divisi->nama_divisi);
    }


    public function destroy(Divisi $divisi)
    {
        $divisi->delete();
        return redirect()->route('adminsdm.data-master.karyawan.divisi.index')->with('msg-success', 'Berhasil menghapus data divisi ' . $divisi->nama_divisi);
    }
    public function printPdf()
    {
        $divisi = Divisi::all(); // Atau filter sesuai kebutuhan
        $waktuCetak = Carbon::now()->translatedFormat('d F Y H:i');

        $pdf = Pdf::loadView('adminsdm.data-master.karyawan.divisi.divisi_pdf', [
            'divisi' => $divisi,
            'type_menu' => 'data-master',
            'waktuCetak' => $waktuCetak,

        ]);
        return $pdf->stream('divisi.pdf'); // atau ->download('learning-group.pdf')
    }
    public function exportExcel()
    {
        return Excel::download(new DivisiExport, 'data-divisi.xlsx');
    }

    public function exportCSV()
    {
        return Excel::download(new DivisiExport, 'data-divisi.csv');
    }
    public function exportDocx()
    {
        $divisi = Divisi::all();

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Tambahkan logo di kiri atas
        $section->addImage(public_path('./img/logo-perhutani.png'), [
            'width' => 100,
            'height' => 100,
            'wrappingStyle' => 'square',
            'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT,
        ]);

        // Spasi antar logo dan teks
        $section->addTextBreak(1);

        // Judul
        $section->addText('Data Divisi Perum Perhutani', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addTextBreak(1);

        // Tabel data
        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80,
        ]);
        $table->addRow();
        $table->addCell(1000)->addText("No", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(3000)->addText("Nama Divisi", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(5000)->addText("Keterangan", ['bold' => true], ['alignment' => 'center']);

        foreach ($divisi as $i => $item) {
            $table->addRow();
            $table->addCell(1000)->addText($i + 1);
            $table->addCell(3000)->addText($item->nama_divisi);
            $table->addCell(5000)->addText($item->keterangan);
        }

        $fileName = 'divisi.docx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
