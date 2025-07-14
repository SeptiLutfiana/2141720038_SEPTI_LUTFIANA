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
            'type_menu' => 'data-master',
            'penempatan' => $penempatan,
            'search' => $search,
        ]);
    }
    public function create()
    {
        return view('adminsdm.data-master.karyawan.penempatan.create', [
            'type_menu' => 'data-master',
        ]);
    }
    public function store(Request $request)
    {
        // Cek apakah user menggunakan input manual
        if ($request->filled('input_manual')) {
            // Validasi form manual
            $request->validate([
                'nama_penempatan' => 'required|string',
                'keterangan' => 'required|string',
            ], [
                'nama_penempatan.required' => 'Nama Penempatan harus diisi',
                'keterangan.required' => 'Keterangan harus diisi',
            ]);

            // Cek duplikat di database
            $duplikat = Penempatan::where('nama_penempatan', $request->nama_penempatan)->exists();
            if ($duplikat) {
                return redirect()->back()->with('msg-error', 'Penempatan dengan nama "' . $request->nama_penempatan . '" sudah ada di database.');
            }

            // Simpan jika tidak duplikat
            Penempatan::create([
                'nama_penempatan' => $request->nama_penempatan,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('adminsdm.data-master.karyawan.penempatan.index')
                ->with('msg-success', 'Berhasil menambahkan data penempatan: ' . $request->nama_penempatan);
        }

        // Jika user memilih upload file
        if ($request->hasFile('file_import')) {
            // Validasi file
            $request->validate([
                'file_import' => 'required|mimes:xlsx,csv|max:512', // Maks 0.5MB
            ], [
                'file_import.required' => 'File harus diupload.',
                'file_import.mimes' => 'File harus berformat .xlsx atau .csv.',
                'file_import.max' => 'Ukuran file maksimal 0.5MB.',
            ]);

            try {
                // Gunakan custom import
                $import = new PenempatanImport;
                Excel::import($import, $request->file('file_import'));

                if (!$import->headerValid) {
                    return redirect()->back()->with('msg-error', $import->duplikat[0] ?? 'Format header tidak valid.');
                }
                if ($import->barisBerhasil === 0 && count($import->duplikat) > 0) {
                    return redirect()->back()
                        ->with('msg-error', 'Semua baris gagal diimpor karena sudah ada di database.')
                        ->with('duplikat', $import->duplikat);
                }
                if (!empty($import->duplikat)) {
                    return redirect()->back()
                        ->with('msg-error', "{$import->barisBerhasil} baris berhasil diimpor. Beberapa baris gagal:")
                        ->with('duplikat', $import->duplikat);
                }

                return redirect()->route('adminsdm.data-master.karyawan.penempatan.index')
                    ->with('msg-success', 'Berhasil mengimpor ' . $import->barisBerhasil . ' data penempatan.');
            } catch (\Exception $e) {
                return redirect()->back()->with('msg-error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
            }
        }

        // Tidak ada input manual atau file
        return redirect()->back()->with('msg-error', 'Tidak ada data yang dikirim.');
    }


    public function show($id)
    {
        // Mengambil data penempatan berdasarkan ID
        $penempatan = Penempatan::findOrFail($id);

        return view('adminsdm.data-master.karyawan.penempatan.detail', [
            'penempatan'    => $penempatan,
            'type_menu' => 'data-master',
        ]);
    }
    public function edit($id)
    {
        $penempatan = Penempatan::findOrFail($id);
        return view('adminsdm.data-master.karyawan.penempatan.edit', [
            'penempatan'    => $penempatan,
            'type_menu' => 'data-master',
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
            'type_menu' => 'data-master',
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
