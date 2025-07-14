<?php

namespace App\Http\Controllers;

use App\Exports\JabatanExport;
use App\Imports\JabatanImport;
use App\Models\Jabatan;
use App\Models\Jenjang;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;


class JabatanController extends Controller
{
    public function index(Request $request)
    {
        $id_jenjang = $request->query('id_jenjang');
        $search = $request->query('search');
        $listJenjang = Jenjang::all();
        $jabatan = Jabatan::with('jenjang') // eager loading relasi jenjang
            ->when($search, function ($query, $search) {
                return $query->where('nama_jabatan', 'like', "%$search%")
                    ->orWhere('keterangan', 'like', "%$search%");
            })
            ->when($id_jenjang, function ($query, $id_jenjang) {
                return $query->where('id_jenjang', $id_jenjang);
            })
            ->orderBy('nama_jabatan')
            ->paginate(10)
            ->withQueryString(); // agar ?search=... tetap terbawa saat paging

        return view('adminsdm.data-master.karyawan.jabatan.index', [
            'type_menu' => 'data-master',
            'jabatan' => $jabatan,
            'search' => $search,
            'listJenjang' => $listJenjang,
            'id_jenjang' => $id_jenjang,
        ]);
    }
    public function create()
    {
        $jenjang = Jenjang::all();
        return view('adminsdm.data-master.karyawan.jabatan.create', [
            'type_menu' => 'data-master',
            'jenjang' => $jenjang,
        ]);
    }
    public function store(Request $request)
    {
        // Jika menggunakan input manual
        if ($request->filled('input_manual')) {
            $request->validate([
                'nama_jabatan' => 'required|string|max:100',
                'keterangan' => 'required|string|max:255',
                'id_jenjang' => 'required|exists:jenjangs,id_jenjang',
            ], [
                'nama_jabatan.required' => 'Nama Jabatan harus diisi.',
                'keterangan.required' => 'Keterangan harus diisi.',
                'id_jenjang.required' => 'Jenjang harus dipilih.',
                'id_jenjang.exists' => 'Jenjang tidak valid.',
            ]);

            Jabatan::create([
                'nama_jabatan' => $request->nama_jabatan,
                'keterangan' => $request->keterangan,
                'id_jenjang' => $request->id_jenjang,
            ]);

            return redirect()->route('adminsdm.data-master.karyawan.jabatan.index')
                ->with('msg-success', 'Berhasil menambahkan data Jabatan ' . $request->nama_jabatan);
        }

        // Jika menggunakan upload file
        if ($request->hasFile('file_import')) {
            $request->validate([
                'file_import' => 'required|mimes:xlsx,csv|max:512',
            ], [
                'file_import.required' => 'File harus diupload.',
                'file_import.mimes' => 'File harus berformat .xlsx atau .csv.',
                'file_import.max' => 'Ukuran file maksimal 0.5MB.',
            ]);

            try {
                $import = new JabatanImport;
                Excel::import($import, $request->file('file_import'));

                if ($import->barisBerhasil === 0 && count($import->duplikat) > 0) {
                    return redirect()->back()
                        ->with('msg-error', 'Semua baris gagal diimpor karena sudah ada di database.')
                        ->with('duplikat', $import->duplikat);
                }

                if (count($import->duplikat) > 0) {
                    return redirect()->route('adminsdm.data-master.karyawan.jabatan.index')
                        ->with('msg-warning', 'Sebagian data berhasil diimpor, namun beberapa baris duplikat.')
                        ->with('duplikat', $import->duplikat);
                }
                return redirect()->route('adminsdm.data-master.karyawan.jabatan.index')
                    ->with('msg-success', 'Berhasil mengimpor ' . $import->barisBerhasil . ' data jabatan.');
            } catch (\Exception $e) {
                return redirect()->back()->with('msg-error', ' Gagal impor: ' . $e->getMessage());
            }
        }

        // Jika tidak memilih input manual maupun upload file
        return redirect()->back()->with('msg-error', 'Tidak ada data yang dikirim.');
    }
    public function show($id)
    {
        // Mengambil data Jabatan berdasarkan ID
        $jabatan = Jabatan::findOrFail($id);

        return view('adminsdm.data-master.karyawan.jabatan.detail', [
            'jabatan'    => $jabatan,
            'type_menu' => 'data-master',
        ]);
    }
    public function edit($id)
    {
        $jenjang = Jenjang::all();
        $jabatan = Jabatan::findOrFail($id);
        return view('adminsdm.data-master.karyawan.jabatan.edit', [
            'jabatan'    => $jabatan,
            'jenjang' => $jenjang,
            'type_menu' => 'data-master',
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_jabatan' => 'required|string',
            'keterangan' => 'required|string',
            'id_jenjang' => 'required|exists:jenjangs,id_jenjang',

        ], [
            'nama_jabatan.required' => 'Nama Jabatan harus diisi',
            'id_jenjang.required' => 'Jenjang harus dipilih',
            'id_jenjang.exists' => 'Jenjang tidak valid',
        ]);

        // Mengambil data Jabatan berdasarkan ID
        $jabatan = Jabatan::findOrFail($id);

        // Melakukan update data jabatan

        $jabatan->update([
            'nama_jabatan' => $request->nama_jabatan,
            'keterangan' => $request->keterangan,
            'id_jenjang' => $request->id_jenjang,
        ]);
        return redirect()->route('adminsdm.data-master.karyawan.jabatan.index')
            ->with('msg-success', 'Berhasil mengubah data Jabatan ' . $jabatan->nama_jabatan);
    }


    public function destroy(Jabatan $jabatan)
    {
        $jabatan->delete();
        return redirect()->route('adminsdm.data-master.karyawan.jabatan.index')->with('msg-success', 'Berhasil menghapus data jabatan ' . $jabatan->nama_jabatan);
    }
    public function printPdf()
    {
        $jabatan = Jabatan::all(); // Atau filter sesuai kebutuhan
        $pdf = Pdf::loadView('adminsdm.data-master.karyawan.jabatan.jabatan_pdf', [
            'jabatan' => $jabatan,
            'type_menu' => 'data-master',
        ]);
        return $pdf->stream('jabatan.pdf'); // atau ->download('learning-group.pdf')
    }
    public function exportExcel()
    {
        return Excel::download(new JabatanExport, 'data-jabatan.xlsx');
    }

    public function exportCSV()
    {
        return Excel::download(new JabatanExport, 'data-jabatan.csv');
    }
    public function exportDocx()
    {
        $jabatan = Jabatan::all();

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
        $section->addText('Data Jabatan Perum Perhutani', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addTextBreak(1);

        // Tabel data
        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80,
        ]);
        $table->addRow();
        $table->addCell(1000)->addText("No", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(3000)->addText("Nama Jabatan", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(5000)->addText("Jenjang", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(5000)->addText("Keterangan", ['bold' => true], ['alignment' => 'center']);

        foreach ($jabatan as $i => $item) {
            $table->addRow();
            $table->addCell(1000)->addText($i + 1);
            $table->addCell(3000)->addText($item->nama_jabatan);
            $table->addCell(5000)->addText($item->jenjang->nama_jenjang);
            $table->addCell(5000)->addText($item->keterangan);
        }

        $fileName = 'jabatan.docx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
