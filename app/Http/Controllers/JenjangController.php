<?php

namespace App\Http\Controllers;

use App\Exports\JenjangExport;
use App\Imports\JenjangImport;
use App\Models\Jenjang;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class JenjangController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $jenjang = Jenjang::when($search, function ($query, $search) {
            return $query->where('nama_jenjang', 'like', "%$search%")
                ->orWhere('keterangan', 'like', "%$search%");
        })
            ->orderBy('nama_jenjang')
            ->paginate(10)
            ->withQueryString(); // agar ?search=... tetap terbawa saat paging

        return view('adminsdm.data-master.karyawan.jenjang.index', [
            'type_menu' => 'data-master',
            'jenjang' => $jenjang,
            'search' => $search,
        ]);
    }
    public function create()
    {
        return view('adminsdm.data-master.karyawan.jenjang.create', [
            'type_menu' => 'data-master',
        ]);
    }
    public function store(Request $request)
    {
        if ($request->filled('input_manual')) {
            // Cek duplikat manual
            if (Jenjang::where('nama_jenjang', $request->nama_jenjang)->exists()) {
                return redirect()->back()->with('msg-error', 'Gagal menambahkan. Jenjang sudah ada di database.');
            }

            $request->validate([
                'nama_jenjang' => 'required|string',
                'keterangan' => 'required|string',
            ], [
                'nama_jenjang.required' => 'Nama jenjang harus diisi',
                'keterangan.required' => 'Keterangan harus diisi',
            ]);

            Jenjang::create([
                'nama_jenjang' => $request->nama_jenjang,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('adminsdm.data-master.karyawan.jenjang.index')
                ->with('msg-success', 'Berhasil menambahkan data jenjang ' . $request->nama_jenjang);
        }

        // Validasi file import
        $request->validate([
            'file_import' => 'required|mimes:xlsx,csv|max:512',
        ], [
            'file_import.required' => 'File harus diupload.',
            'file_import.mimes' => 'Format harus .xlsx atau .csv.',
            'file_import.max' => 'Ukuran maksimal 0.5MB.',
        ]);

        try {
            $import = new JenjangImport;
            Excel::import($import, $request->file('file_import'));

            if (!$import->headerValid) {
                return redirect()->back()->with('msg-error', 'Gagal impor. Format header tidak sesuai. Kolom wajib: nama_jenjang, keterangan.');
            }

            if ($import->barisBerhasil === 0) {
                return redirect()->back()->with('msg-error', 'Tidak ada data yang berhasil diimpor.')->with('duplikat', $import->duplikat);
            }

            return redirect()->route('adminsdm.data-master.karyawan.jenjang.index')
                ->with('msg-success', 'Berhasil mengimpor ' . $import->barisBerhasil . ' data jenjang.')
                ->with('duplikat', $import->duplikat);
        } catch (\Exception $e) {
            return redirect()->back()->with('msg-error', 'Terjadi kesalahan saat mengimpor: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $jenjang = Jenjang::findOrFail($id);

        return view('adminsdm.data-master.karyawan.jenjang.detail', [
            'jenjang'    => $jenjang,
            'type_menu' => 'data-master',
        ]);
    }
    public function edit($id)
    {
        $jenjang = Jenjang::findOrFail($id);
        return view('adminsdm.data-master.karyawan.jenjang.edit', [
            'jenjang'    => $jenjang,
            'type_menu' => 'data-master',
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_jenjang' => 'required|string',
            'keterangan' => 'required|string',
        ], [
            'nama_jenjang.required' => 'Nama Jenjang harus diisi',
        ]);

        $jenjang = Jenjang::findOrFail($id);

        $jenjang->update($request->all());

        return redirect()->route('adminsdm.data-master.karyawan.jenjang.index')
            ->with('msg-success', 'Berhasil mengubah data Jenjang ' . $jenjang->nama_jenjang);
    }


    public function destroy(Jenjang $jenjang)
    {
        $jenjang->delete();
        return redirect()->route('adminsdm.data-master.karyawan.jenjang.index')->with('msg-success', 'Berhasil menghapus data jenjang ' . $jenjang->nama_jenjang);
    }
    public function printPdf()
    {
        $jenjang = Jenjang::all(); // Atau filter sesuai kebutuhan
        $pdf = Pdf::loadView('adminsdm.data-master.karyawan.jenjang.jenjang_pdf', [
            'jenjang' => $jenjang,
            'type_menu' => 'data-master',
        ]);
        return $pdf->stream('jenjang.pdf'); // atau ->download('learning-group.pdf')
    }
    public function exportExcel()
    {
        return Excel::download(new JenjangExport, 'data-jenjang.xlsx');
    }

    public function exportCSV()
    {
        return Excel::download(new JenjangExport, 'data-jenjang.csv');
    }
    public function exportDocx()
    {
        $jenjang = Jenjang::all();

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
        $section->addText('Data Jenjang Perum Perhutani', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addTextBreak(1);

        // Tabel data
        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80,
        ]);
        $table->addRow();
        $table->addCell(1000)->addText("No", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(3000)->addText("Nama Jenjang", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(5000)->addText("Keterangan", ['bold' => true], ['alignment' => 'center']);

        foreach ($jenjang as $i => $item) {
            $table->addRow();
            $table->addCell(1000)->addText($i + 1);
            $table->addCell(3000)->addText($item->nama_jenjang);
            $table->addCell(5000)->addText($item->keterangan);
        }

        $fileName = 'jenjang.docx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
