<?php

namespace App\Http\Controllers;

use App\Exports\SemesterExport;
use App\Imports\SemesterImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $semester = Semester::when($search, function ($query, $search) {
            return $query->where('nama_semester', 'like', "%$search%")
                ->orWhere('keterangan', 'like', "%$search%");
        })
            ->orderBy('nama_semester')
            ->paginate(10)
            ->withQueryString(); // agar ?search=... tetap terbawa saat paging

        return view('adminsdm.data-master.data-idp.semester.index', [
            'type_menu' => 'data-master',
            'semester' => $semester,
            'search' => $search,
        ]);
    }
    public function create()
    {
        return view('adminsdm.data-master.data-idp.semester.create', [
            'type_menu' => 'data-master',
        ]);
    }
    public function store(Request $request)
    {
        if ($request->filled('input_manual')) {
            // Duplikat manual
            if (Semester::where('nama_semester', $request->nama_semester)->exists()) {
                return redirect()->back()->with('msg-error', 'Semester dengan nama tersebut sudah ada.');
            }

            $request->validate([
                'nama_semester' => 'required|string',
                'keterangan' => 'required|string',
            ], [
                'nama_semester.required' => 'Nama Semester harus diisi',
                'keterangan.required' => 'Keterangan harus diisi',
            ]);

            Semester::create([
                'nama_semester' => $request->nama_semester,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('adminsdm.data-master.data-idp.semester.index')
                ->with('msg-success', 'Berhasil menambahkan data ' . $request->nama_semester);
        }

        // Validasi file upload
        $request->validate([
            'file_import' => 'required|mimes:xlsx,csv|max:512',
        ], [
            'file_import.required' => 'File harus diupload.',
            'file_import.mimes' => 'File harus .xlsx atau .csv.',
            'file_import.max' => 'Ukuran maksimal 0.5MB.',
        ]);

        try {
            $import = new \App\Imports\SemesterImport;
            Excel::import($import, $request->file('file_import'));

            if (!$import->headerValid) {
                return redirect()->back()->with('msg-error', 'Format header tidak sesuai. Kolom wajib: nama_semester, keterangan.');
            }

            if ($import->barisBerhasil === 0) {
                return redirect()->back()
                    ->with('msg-error', 'Tidak ada data yang berhasil diimpor.')
                    ->with('duplikat', $import->duplikat);
            }

            return redirect()->route('adminsdm.data-master.data-idp.semester.index')
                ->with('msg-success', "Berhasil mengimpor {$import->barisBerhasil} data semester.")
                ->with('duplikat', $import->duplikat);
        } catch (\Exception $e) {
            return redirect()->back()->with('msg-error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
        }
    }



    public function show($id)
    {
        $semester = Semester::findOrFail($id);

        return view('adminsdm.data-master.data-idp.semester.detail', [
            'semester'    => $semester,
            'type_menu' => 'data-master',
        ]);
    }
    public function edit($id)
    {
        $semester = Semester::findOrFail($id);
        return view('adminsdm.data-master.data-idp.semester.edit', [
            'semester'    => $semester,
            'type_menu' => 'data-master',
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_semester' => 'required|string',
            'keterangan' => 'required|string',
        ], [
            'nama_semester.required' => 'Nama semester harus diisi',
        ]);

        $semester = Semester::findOrFail($id);

        $semester->update($request->all());

        return redirect()->route('adminsdm.data-master.data-idp.semester.index')
            ->with('msg-success', 'Berhasil mengubah data  ' . $semester->nama_semester);
    }


    public function destroy(Semester $semester)
    {
        $semester->delete();
        return redirect()->route('adminsdm.data-master.data-idp.semester.index')->with('msg-success', 'Berhasil menghapus data  ' . $semester->nama_semester);
    }
    public function printPdf()
    {
        $semester = Semester::all(); // Atau filter sesuai kebutuhan
        $pdf = Pdf::loadView('adminsdm.data-master.data-idp.semester.semester_pdf', [
            'semester' => $semester,
            'type_menu' => 'data-master',
        ]);
        return $pdf->stream('semester.pdf'); // atau ->download('learning-group.pdf')
    }
    public function exportExcel()
    {
        return Excel::download(new SemesterExport, 'data-semester.xlsx');
    }

    public function exportCSV()
    {
        return Excel::download(new SemesterExport, 'data-semester.csv');
    }
    public function exportDocx()
    {
        $semester = Semester::all();

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
        $section->addText('Data Semester Perum Perhutani', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addTextBreak(1);

        // Tabel data
        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80,
        ]);
        $table->addRow();
        $table->addCell(1000)->addText("No", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(3000)->addText("Nama Semester", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(5000)->addText("Keterangan", ['bold' => true], ['alignment' => 'center']);

        foreach ($semester as $i => $item) {
            $table->addRow();
            $table->addCell(1000)->addText($i + 1);
            $table->addCell(3000)->addText($item->nama_semester);
            $table->addCell(5000)->addText($item->keterangan);
        }

        $fileName = 'data-semester.docx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
