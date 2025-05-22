<?php

namespace App\Http\Controllers;

use App\Exports\KompetensiExport;
use App\Imports\KompetensiImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Kompetensi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class KompetensiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $jenis = $request->query('jenis_kompetensi');
        $kompetensi = Kompetensi::when($search, function ($query, $search) {
            return $query->where('nama_kompetensi', 'like', "%$search%")
                ->orWhere('keterangan', 'like', "%$search%");
        })
            ->when($jenis, function ($query, $jenis) {
                return $query->where('jenis_kompetensi', $jenis);
            })
            ->orderBy('nama_kompetensi')
            ->paginate(10)
            ->withQueryString(); // agar ?search=... tetap terbawa saat paging

        return view('adminsdm.data-master.kompetensi.index', [
            'type_menu' => 'kompetensi',
            'kompetensi' => $kompetensi,
            'search' => $search,
            'jenis_kompetensi' => $jenis,
        ]);
    }
    public function create()
    {
        return view('adminsdm.data-master.kompetensi.create', [
            'type_menu' => 'tambah-kompetensi',
        ]);
    }
    public function store(Request $request)
    {
        // Cek apakah user menggunakan form input manual
        if ($request->filled('input_manual')) {
            // Validasi untuk input manual
            $request->validate([
                'nama_kompetensi' => 'required|string',
                'jenis_kompetensi' => 'required|in:Hard Kompetensi,Soft Kompetensi',
                'keterangan' => 'required|string',
            ], [
                'nama_kompetensi.required' => 'Nama Kompetensi harus diisi',
                'jenis_kompetensi.required' => 'Jenis Kompetensi harus dipilih',
                'jenis_kompetensi.in' => 'Jenis Kompetensi tidak valid',
                'keterangan.required' => 'Keterangan harus diisi',
            ]);

            Kompetensi::create([
                'nama_kompetensi' => $request->nama_kompetensi,
                'jenis_kompetensi' => $request->jenis_kompetensi,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('adminsdm.data-master.kompetensi.index')
                ->with('msg-success', 'Berhasil menambahkan data ' . $request->nama_kompetensi);
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
                Excel::import(new KompetensiImport, $request->file('file_import'));

                return redirect()->route('adminsdm.data-master.kompetensi.index')
                    ->with('msg-success', 'Berhasil mengimpor data kompetensi dari file.');
            } catch (\Exception $e) {
                // Jika ada error saat mengimpor, tangani dan tampilkan pesan error
                return redirect()->back()->with('msg-error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
            }
        }

        // Kalau tidak ada data input manual atau upload file
        return redirect()->back()->with('msg-error', 'Tidak ada data yang dikirim.');
    }


    public function show($id)
    {
        $kompetensi = Kompetensi::findOrFail($id);

        return view('adminsdm.data-master.kompetensi.detail', [
            'kompetensi'    => $kompetensi,
            'type_menu' => 'kompetensi',
        ]);
    }
    public function edit($id)
    {
        $kompetensi = Kompetensi::findOrFail($id);
        return view('adminsdm.data-master.kompetensi.edit', [
            'kompetensi'    => $kompetensi,
            'type_menu' => 'kompetensi',
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kompetensi' => 'required|string',
            'jenis_kompetensi' => 'required|in:Hard Kompetensi,Soft Kompetensi',
            'keterangan' => 'required|string',
        ], [
            'nama_kompetensi.required' => 'Nama semester harus diisi',
        ]);

        $kompetensi = Kompetensi::findOrFail($id);

        $kompetensi->update($request->all());

        return redirect()->route('adminsdm.data-master.kompetensi.index')
            ->with('msg-success', 'Berhasil mengubah data  ' . $kompetensi->nama_kompetensi);
    }


    public function destroy(Kompetensi $kompetensi)
    {
        $kompetensi->delete();
        return redirect()->route('adminsdm.data-master.kompetensi.index')->with('msg-success', 'Berhasil menghapus data  ' . $kompetensi->nama_kompetensi);
    }
    public function printPdf()
    {
        $kompetensi = Kompetensi::all(); // Atau filter sesuai kebutuhan
        $pdf = Pdf::loadView('adminsdm.data-master.kompetensi.kompetensi_pdf', [
            'kompetensi' => $kompetensi,
            'type_menu' => 'kompetensi',
        ]);
        return $pdf->stream('kompetensi.pdf'); // atau ->download('learning-group.pdf')
    }
    public function exportExcel()
    {
        return Excel::download(new KompetensiExport, 'data-kompetensi.xlsx');
    }

    public function exportCSV()
    {
        return Excel::download(new KompetensiExport, 'data-kompetensi.csv');
    }
    public function exportDocx()
    {
        $kompetensi = Kompetensi::all();
        $hardKompetensi = $kompetensi->where('jenis_kompetensi', 'Hard Kompetensi');
        $softKompetensi = $kompetensi->where('jenis_kompetensi', 'Soft Kompetensi');

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Tambahkan logo
        $section->addImage(public_path('./img/logo-perhutani.png'), [
            'width' => 100,
            'height' => 100,
            'wrappingStyle' => 'square',
            'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT,
        ]);

        $section->addTextBreak(1);

        // Judul
        $section->addText('Data Kompetensi Individual Development Plan Perum Perhutani', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addTextBreak(1);

        // ==== Hard Competency ====
        $section->addText('Hard Kompetensi', ['bold' => true, 'size' => 14]);
        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80,
        ]);
        $table->addRow();
        $table->addCell(1000)->addText("No", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(3000)->addText("Nama Kompetensi", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(3000)->addText("Jenis Kompetensi", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(5000)->addText("Keterangan", ['bold' => true], ['alignment' => 'center']);

        foreach ($hardKompetensi->values() as $i => $item) {
            $table->addRow();
            $table->addCell(1000)->addText($i + 1); // Akan selalu mulai dari 1
            $table->addCell(3000)->addText($item->nama_kompetensi);
            $table->addCell(3000)->addText($item->jenis_kompetensi);
            $table->addCell(5000)->addText($item->keterangan);
        }

        $section->addTextBreak(1);

        // ==== Soft Competency ====
        $section->addText('Soft Kompetensi', ['bold' => true, 'size' => 14]);
        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80,
        ]);
        $table->addRow();
        $table->addCell(1000)->addText("No", ['bold' => true]);
        $table->addCell(3000)->addText("Nama Kompetensi", ['bold' => true]);
        $table->addCell(3000)->addText("Jenis Kompetensi", ['bold' => true]);
        $table->addCell(5000)->addText("Keterangan", ['bold' => true]);

        foreach ($softKompetensi->values() as $i => $item) {
            $table->addRow();
            $table->addCell(1000)->addText($i + 1); // Akan mulai dari 1 juga
            $table->addCell(3000)->addText($item->nama_kompetensi);
            $table->addCell(3000)->addText($item->jenis_kompetensi);
            $table->addCell(5000)->addText($item->keterangan);
        }

        $fileName = 'kompetensi.docx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
