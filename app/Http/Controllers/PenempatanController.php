<?php

namespace App\Http\Controllers;

use App\Exports\PenempatanExport;
use App\Imports\PenempatanImport;
use App\Models\Penempatan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class PenempatanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $penempatan = Penempatan::when($search, function ($query, $search) {
            return $query->where('nama_penempatan', 'like', "%$search%")
                ->orWhere('keterangan', 'like', "%$search%");
        })
            ->orderBy('nama_penempatan')
            ->paginate(10)
            ->withQueryString(); // agar ?search=... tetap terbawa saat paging

        return view('adminsdm.data-master.karyawan.penempatan.index', [
            'type_menu' => 'penempatan',
            'penempatan' => $penempatan,
            'search' => $search,
        ]);
    }
    public function create()
    {
        return view('adminsdm.data-master.karyawan.penempatan.create', [
            'type_menu' => 'tambah-penempatan',
        ]);
    }
    public function store(Request $request)
    {
        // Cek apakah user menggunakan form input manual
        if ($request->filled('input_manual')) {
            // Validasi untuk input manual
            $request->validate([
                'nama_penempatan' => 'required|string',
                'keterangan' => 'required|string',
            ], [
                'nama_penempatan.required' => 'Nama Penempatan harus diisi',
                'keterangan.required' => 'Keterangan harus diisi',
            ]);

            Penempatan::create([
                'nama_penempatan' => $request->nama_penempatan,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('adminsdm.data-master.karyawan.penempatan.index')
                ->with('msg-success', 'Berhasil menambahkan data penempatan ' . $request->nama_penempatan);
        }

        // Jika user memilih upload file
        if ($request->hasFile('file_import')) {
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
                Excel::import(new PenempatanImport, $request->file('file_import'));

                // Redirect ke halaman Data dengan pesan sukses
                return redirect()->route('adminsdm.data-master.karyawan.penempatan.index')
                    ->with('msg-success', 'Berhasil mengimpor data penempatan dari file.');
            } catch (\Exception $e) {
                // Jika ada error saat mengimpor, tangani dan tampilkan pesan error
                return redirect()->back()->with('msg-error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
            }
        }

        // Kalau tidak dua-duanya
        return redirect()->back()->with('msg-error', 'Tidak ada data yang dikirim.');
    }

    public function show($id)
    {
        // Mengambil data penempatan berdasarkan ID
        $penempatan = Penempatan::findOrFail($id);

        return view('adminsdm.data-master.karyawan.penempatan.detail', [
            'penempatan'    => $penempatan,
            'type_menu' => 'penempatan',
        ]);
    }
    public function edit($id)
    {
        $penempatan = Penempatan::findOrFail($id);
        return view('adminsdm.data-master.karyawan.penempatan.edit', [
            'penempatan'    => $penempatan,
            'type_menu' => 'penempatan',
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_penempatan' => 'required|string',
            'keterangan' => 'required|string',
        ], [
            'nama_penempatan.required' => 'Nama penempatan harus diisi',
        ]);

        // Mengambil data penempatan berdasarkan ID
        $penempatan = Penempatan::findOrFail($id);

        // Melakukan update data jabatan
        $penempatan->update($request->all());

        return redirect()->route('adminsdm.data-master.karyawan.penempatan.index')
            ->with('msg-success', 'Berhasil mengubah data penempatan ' . $penempatan->nama_penempatan);
    }


    public function destroy(Penempatan $penempatan)
    {
        $penempatan->delete();
        return redirect()->route('adminsdm.data-master.karyawan.penempatan.index')->with('msg-success', 'Berhasil menghapus data jabatan ' . $penempatan->nama_penempatan);
    }
    public function printPdf()
    {
        $penempatan = Penempatan::all(); // Atau filter sesuai kebutuhan
        $pdf = Pdf::loadView('adminsdm.data-master.karyawan.penempatan.penempatan_pdf', [
            'penempatan' => $penempatan,
            'type_menu' => 'penempatan',
        ]);
        return $pdf->stream('penempatan.pdf'); // atau ->download('learning-group.pdf')
    }
    public function exportExcel()
    {
        return Excel::download(new PenempatanExport, 'data-penempatan.xlsx');
    }

    public function exportCSV()
    {
        return Excel::download(new PenempatanExport, 'data-penempatan.csv');
    }
    public function exportDocx()
    {
        $penempatan = Penempatan::all();

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
        $section->addText('Data Penempatan Perum Perhutani', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addTextBreak(1);

        // Tabel data
        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80,
        ]);
        $table->addRow();
        $table->addCell(1000)->addText("No", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(3000)->addText("Nama Penempatan", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(5000)->addText("Keterangan", ['bold' => true], ['alignment' => 'center']);

        foreach ($penempatan as $i => $item) {
            $table->addRow();
            $table->addCell(1000)->addText($i + 1);
            $table->addCell(3000)->addText($item->nama_penempatan);
            $table->addCell(5000)->addText($item->keterangan);
        }

        $fileName = 'penempatan.docx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
