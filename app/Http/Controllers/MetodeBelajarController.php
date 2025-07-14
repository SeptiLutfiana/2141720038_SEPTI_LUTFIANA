<?php

namespace App\Http\Controllers;

use App\Exports\MetodeBelajarExport;
use App\Imports\MetodeBelajarImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MetodeBelajar;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class MetodeBelajarController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $metodebelajar = MetodeBelajar::when($search, function ($query, $search) {
            return $query->where('nama_metodeBelajar', 'like', "%$search%")
                ->orWhere('keterangan', 'like', "%$search%");
        })
            ->orderBy('nama_metodeBelajar')
            ->paginate(10)
            ->withQueryString(); // agar ?search=... tetap terbawa saat paging

        return view('adminsdm.data-master.data-idp.metode-belajar.index', [
            'type_menu' => 'metodebelajar',
            'metodebelajar' => $metodebelajar,
            'search' => $search,
        ]);
    }
    public function create()
    {
        return view('adminsdm.data-master.data-idp.metode-belajar.create', [
            'type_menu' => 'metodebelajar',
        ]);
    }
    public function store(Request $request)
    {
        if ($request->filled('input_manual')) {
            $request->validate([
                'nama_metodeBelajar' => 'required|string',
                'keterangan' => 'required|string',
            ]);

            // Cek duplikat manual
            $exists = MetodeBelajar::where('nama_metodeBelajar', $request->nama_metodeBelajar)->exists();
            if ($exists) {
                return redirect()->back()
                    ->with('msg-error', 'Data gagal ditambahkan. Metode belajar "' . $request->nama_metodeBelajar . '" sudah ada.');
            }

            MetodeBelajar::create([
                'nama_metodeBelajar' => $request->nama_metodeBelajar,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('adminsdm.data-master.data-idp.metode-belajar.index')
                ->with('msg-success', 'Berhasil menambahkan data ' . $request->nama_metodeBelajar);
        }

        $request->validate([
            'file_import' => 'required|mimes:xlsx,csv|max:512', // Maks 0.5MB
        ], [
            'file_import.required' => 'File harus diupload.',
            'file_import.mimes' => 'File harus berformat .xlsx atau .csv.',
            'file_import.max' => 'Ukuran file maksimal 0.5MB.',
        ]);

        try {
            $import = new MetodeBelajarImport;
            Excel::import($import, $request->file('file_import'));

            if (!$import->headerValid) {
                return redirect()->back()
                    ->with('msg-error', 'Header file tidak sesuai.')
                    ->with('failures', $import->duplikat);
            }

            if ($import->barisBerhasil === 0) {
                return redirect()->back()
                    ->with('msg-error', 'Tidak ada data yang berhasil diimpor.')
                    ->with('failures', $import->duplikat);
            }

            if (count($import->duplikat)) {
                return redirect()->back()
                    ->with('msg-success', $import->barisBerhasil . ' baris berhasil diimpor.')
                    ->with('failures', $import->duplikat);
            }

            return redirect()->route('adminsdm.data-master.data-idp.metode-belajar.index')
                ->with('msg-success', 'Berhasil mengimpor ' . $import->barisBerhasil . ' data metode belajar.');
        } catch (\Exception $e) {
            return redirect()->back()->with('msg-error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $metodebelajar
            = MetodeBelajar::findOrFail($id);

        return view('adminsdm.data-master.data-idp.metode-belajar.detail', [
            'metodebelajar'    => $metodebelajar,
            'type_menu' => 'metodebelajar',
        ]);
    }
    public function edit($id)
    {
        $metodebelajar = MetodeBelajar::findOrFail($id);
        return view('adminsdm.data-master.data-idp.metode-belajar.edit', [
            'metodebelajar'    => $metodebelajar,
            'type_menu' => 'metodebelajar',
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_metodeBelajar' => 'required|string',
            'keterangan' => 'required|string',
        ], [
            'nama_metodeBelajar.required' => 'Nama metode belajar harus diisi',
        ]);

        $metodebelajar = MetodeBelajar::findOrFail($id);

        $metodebelajar->update($request->all());

        return redirect()->route('adminsdm.data-master.data-idp.metode-belajar.index')
            ->with('msg-success', 'Berhasil mengubah data  ' . $metodebelajar->nama_metodeBelajar);
    }


    public function destroy(MetodeBelajar $metodebelajar)
    {
        $metodebelajar->delete();
        return redirect()->route('adminsdm.data-master.data-idp.metode-belajar.index')->with('msg-success', 'Berhasil menghapus data  ' . $metodebelajar->nama_metodeBelajar);
    }
    public function printPdf()
    {
        $metodebelajar = MetodeBelajar::all(); // Atau filter sesuai kebutuhan
        $pdf = Pdf::loadView('adminsdm.data-master.data-idp.metode-belajar.metodeBelajar_pdf', [
            'metodebelajar' => $metodebelajar,
            'type_menu' => 'metodebelajar',
        ]);
        return $pdf->stream('metodebelajar.pdf'); // atau ->download('learning-group.pdf')
    }
    public function exportExcel()
    {
        return Excel::download(new MetodeBelajarExport, 'data-metode-belajar.xlsx');
    }

    public function exportCSV()
    {
        return Excel::download(new MetodeBelajarExport, 'data-metode-belajar.csv');
    }
    public function exportDocx()
    {
        $metodebelajar = MetodeBelajar::all();

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
        $section->addText('Data Metode Belajar Individual Development Plan Perum Perhutani', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addTextBreak(1);

        // Tabel data
        $table = $section->addTable();

        $table->addRow();
        $table->addCell(1000)->addText("No", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(3000)->addText("Metode Belajar", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(5000)->addText("Keterangan", ['bold' => true], ['alignment' => 'center']);

        foreach ($metodebelajar as $i => $item) {
            $table->addRow();
            $table->addCell(1000)->addText($i + 1);
            $table->addCell(3000)->addText($item->nama_metodeBelajar);
            $table->addCell(5000)->addText($item->keterangan);
        }

        $fileName = 'data-metode-belajar.docx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
