<?php

namespace App\Http\Controllers;

use App\Exports\LearningGroupExport;
use App\Imports\LearningGroupImport;
use App\Models\LearingGroup;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class LearninggroupController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $LG = LearingGroup::when($search, function ($query, $search) {
            return $query->where('nama_LG', 'like', "%$search%")
                ->orWhere('keterangan', 'like', "%$search%");
        })
            ->orderBy('nama_LG')
            ->paginate(10)
            ->withQueryString(); // agar ?search=... tetap terbawa saat paging

        return view('adminsdm.data-master.karyawan.learning-group.index', [
            'type_menu' => 'data-master',
            'LG' => $LG,
            'search' => $search,
        ]);
    }
    public function create()
    {
        return view('adminsdm.data-master.karyawan.learning-group.create', [
            'type_menu' => 'data-master',
        ]);
    }
    public function store(Request $request)
    {
        if ($request->filled('input_manual')) {
            // Cek duplikat manual
            if (\App\Models\LearingGroup::where('nama_LG', $request->nama_LG)->exists()) {
                return redirect()->back()->with('msg-error', 'Direktorat dengan nama tersebut sudah ada.');
            }

            $request->validate([
                'nama_LG' => 'required|string',
                'keterangan' => 'required|string',
            ], [
                'nama_LG.required' => 'Nama Direktorat harus diisi',
                'keterangan.required' => 'Keterangan harus diisi',
            ]);

            \App\Models\LearingGroup::create([
                'nama_LG' => $request->nama_LG,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('adminsdm.data-master.karyawan.learning-group.index')
                ->with('msg-success', 'Berhasil menambahkan data Direktorat ' . $request->nama_LG);
        }

        // Validasi file upload
        $request->validate([
            'file_import' => 'required|mimes:xlsx,csv|max:512',
        ], [
            'file_import.required' => 'File harus diupload.',
            'file_import.mimes' => 'Format harus .xlsx atau .csv.',
            'file_import.max' => 'Ukuran maksimal 0.5MB.',
        ]);

        try {
            $import = new \App\Imports\LearningGroupImport;
            Excel::import($import, $request->file('file_import'));

            if (!$import->headerValid) {
                return redirect()->back()->with('msg-error', 'Format header tidak sesuai. Kolom wajib: nama_lg, keterangan.');
            }

            if ($import->barisBerhasil === 0) {
                return redirect()->back()
                    ->with('msg-error', 'Tidak ada data yang berhasil diimpor.')
                    ->with('duplikat', $import->duplikat);
            }

            return redirect()->route('adminsdm.data-master.karyawan.learning-group.index')
                ->with('msg-success', "Berhasil mengimpor {$import->barisBerhasil} data Direktorat.")
                ->with('duplikat', $import->duplikat);
        } catch (\Exception $e) {
            return redirect()->back()->with('msg-error', 'Terjadi kesalahan saat impor: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $LG = LearingGroup::findOrFail($id);

        return view('adminsdm.data-master.karyawan.learning-group.detail', [
            'LG'    => $LG,
            'type_menu' => 'data-master',
        ]);
    }
    public function edit($id)
    {
        $LG = LearingGroup::findOrFail($id);
        return view('adminsdm.data-master.karyawan.learning-group.edit', [
            'LG'    => $LG,
            'type_menu' => 'data-master',
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_LG' => 'required|string',
            'keterangan' => 'required|string',
        ], [
            'nama_LG.required' => 'Nama learning group harus diisi',
        ]);

        $LG = LearingGroup::findOrFail($id);

        // Melakukan update data jabatan
        $LG->update($request->all());

        return redirect()->route('adminsdm.data-master.karyawan.learning-group.index')
            ->with('msg-success', 'Berhasil mengubah data learning group ' . $LG->nama_LG);
    }

    public function destroy(LearingGroup $LG)
    {
        $LG->delete();
        return redirect()->route('adminsdm.data-master.karyawan.learning-group.index')->with('msg-success', 'Berhasil menghapus data jabatan ' . $LG->nama_LG);
    }
    public function printPdf()
    {
        $LG = LearingGroup::all(); // Atau filter sesuai kebutuhan
        $pdf = Pdf::loadView('adminsdm.data-master.karyawan.learning-group.learninggroup_pdf', [
            'LG' => $LG,
            'type_menu' => 'data-master',
        ]);
        return $pdf->stream('jenjang.pdf'); // atau ->download('learning-group.pdf')
    }
    public function exportExcel()
    {
        return Excel::download(new LearningGroupExport, 'data-LG.xlsx');
    }

    public function exportCSV()
    {
        return Excel::download(new LearningGroupExport, 'data-LG.csv');
    }
    public function exportDocx()
    {
        $LG = LearingGroup::all();

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
        $section->addText('Data Learning Group Perum Perhutani', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addTextBreak(1);

        // Tabel data
        $table = $section->addTable();

        $table->addRow();
        $table->addCell(1000)->addText("No", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(3000)->addText("Nama Learning Group", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(5000)->addText("Keterangan", ['bold' => true], ['alignment' => 'center']);

        foreach ($LG as $i => $item) {
            $table->addRow();
            $table->addCell(1000)->addText($i + 1);
            $table->addCell(3000)->addText($item->nama_LG);
            $table->addCell(5000)->addText($item->keterangan);
        }

        $fileName = 'data-LG.docx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
