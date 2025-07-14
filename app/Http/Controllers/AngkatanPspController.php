<?php

namespace App\Http\Controllers;

use App\Exports\AngkatanPspExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AngkatanPspImport;
use App\Models\AngkatanPSP;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class AngkatanPspController extends Controller
{
    public function index(Request $request)
    {
        // Ambil nilai 'search' (tahun) dari query string
        $search = $request->query('search');

        // Ambil tahun yang tersedia dalam database untuk dropdown
        $years = AngkatanPSP::distinct()->pluck('tahun');  // Menarik tahun unik

        // Query untuk mencari data berdasarkan bulan atau tahun
        $angkatanpsp = AngkatanPSP::when($search, function ($query, $search) {
            return $query->where('tahun', 'like', "%$search%");
        })
            ->orderBy('bulan')
            ->paginate(10) // menampilkan 10 data per halaman
            ->withQueryString(); // agar ?search=... tetap terbawa saat paging

        // Mengirimkan data ke view
        return view('adminsdm.data-master.karyawan.angkatan-psp.index', [
            'type_menu' => 'data-master',
            'angkatanpsp' => $angkatanpsp,
            'search' => $search,
            'years' => $years, // Kirim daftar tahun ke view
        ]);
    }


    public function create()
    {
        return view('adminsdm.data-master.karyawan.angkatan-psp.create', [
            'type_menu' => 'data-master',
        ]);
    }
    public function store(Request $request)
    {
        if ($request->filled('input_manual')) {
            $request->validate([
                'bulan' => 'required|string|in:Januari,Februari,Maret,April,Mei,Juni,Juli,Agustus,September,Oktober,November,Desember',
                'tahun' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
            ], [
                'bulan.required' => 'Bulan harus diisi',
                'bulan.in' => 'Bulan yang dipilih tidak valid',
                'tahun.required' => 'Tahun harus diisi',
            ]);
            if (AngkatanPSP::where('bulan', $request->bulan)->where('tahun', $request->tahun)->exists()) {
                return redirect()->back()->with('msg-error', 'Data untuk bulan ' . $request->bulan . ' tahun ' . $request->tahun . ' sudah ada.');
            }
            AngkatanPSP::create([
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
            ]);

            return redirect()->route('adminsdm.data-master.karyawan.angkatan-psp.index')
                ->with('msg-success', 'Berhasil menambahkan data ' . $request->bulan);
        }

        // Validasi upload file
        $request->validate([
            'file_import' => 'required|mimes:xlsx,csv|max:512',
        ], [
            'file_import.required' => 'File harus diupload.',
            'file_import.mimes' => 'File harus berformat .xlsx atau .csv.',
            'file_import.max' => 'Ukuran file maksimal 0.5MB.',
        ]);

        try {
            $import = new AngkatanPspImport;
            Excel::import($import, $request->file('file_import'));

            if (!$import->headerValid) {
                return redirect()->back()->with('msg-error', 'Format file tidak sesuai. Kolom "bulan" dan "tahun" wajib ada.');
            }

            if ($import->barisBerhasil === 0) {
                $pesan = 'Tidak ada data yang berhasil ditambahkan.';
                if (!empty($import->duplikat)) {
                    $pesan .= '<br>Detail kesalahan:<br>' . implode('<br>', $import->duplikat);
                }
                return redirect()->back()->with('msg-error', $pesan);
            }

            if (!empty($import->duplikat)) {
                return redirect()->route('adminsdm.data-master.karyawan.angkatan-psp.index')
                    ->with('msg-success', "✅ Berhasil mengimpor {$import->barisBerhasil} data. Namun beberapa baris dilewati:<br>" . implode('<br>', $import->duplikat));
            }

            return redirect()->route('adminsdm.data-master.karyawan.angkatan-psp.index')
                ->with('msg-success', "✅ Berhasil mengimpor {$import->barisBerhasil} data.");
        } catch (\Exception $e) {
            return redirect()->back()->with('msg-error', '❌ Gagal impor: ' . $e->getMessage());
        }
    }
    public function show($id)
    {
        $angkatanpsp = AngkatanPSP::findOrFail($id);

        return view('adminsdm.data-master.karyawan.angkatan-psp.detail', [
            'angkatanpsp' => $angkatanpsp,
            'type_menu' => 'data-master',
        ]);
    }
    public function edit($id)
    {
        $angkatanpsp = AngkatanPSP::findOrFail($id);
        return view('adminsdm.data-master.karyawan.angkatan-psp.edit', [
            'angkatanpsp'    => $angkatanpsp,
            'type_menu' => 'data-master',
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'bulan' => 'required|string|in:Januari,Februari,Maret,April,Mei,Juni,Juli,Agustus,September,Oktober,November,Desember',
            'tahun' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
        ], [
            'bulan.required' => 'Bulan harus diisi',
        ]);

        $angkatanpsp = AngkatanPSP::findOrFail($id);

        $angkatanpsp->update($request->all());

        return redirect()->route('adminsdm.data-master.karyawan.angkatan-psp.index')
            ->with('msg-success', 'Berhasil mengubah data ' . $angkatanpsp->bulan);
    }


    public function destroy(AngkatanPSP $angkatanpsp)
    {
        $angkatanpsp->delete();
        return redirect()->route('adminsdm.data-master.karyawan.angkatan-psp.index')->with('msg-success', 'Berhasil menghapus data' . $angkatanpsp->bulan);
    }
    public function printPdf()
    {
        $angkatanpsp = AngkatanPSP::all(); // Atau filter sesuai kebutuhan
        $pdf = Pdf::loadView('adminsdm.data-master.karyawan.angkatan-psp.angkatanpsp_pdf', [
            'angkatanpsp' => $angkatanpsp,
            'type_menu' => 'data-master',
        ]);
        return $pdf->stream('angkatanpsp.pdf'); // atau ->download('learning-group.pdf')
    }
    public function exportExcel()
    {
        return Excel::download(new AngkatanPspExport, 'data-angkatanpsp.xlsx');
    }

    public function exportCSV()
    {
        return Excel::download(new AngkatanPspExport, 'data-angkatanpsp.csv');
    }
    public function exportDocx()
    {
        $angkatanpsp = AngkatanPSP::all();

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
        $section->addText('Data Angkatan PSP Perum Perhutani', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addTextBreak(1);

        // Tabel data
        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80,
        ]);

        $table->addRow();
        $table->addCell(1000)->addText("No", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(3000)->addText("Bulan", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(5000)->addText("Tahun", ['bold' => true], ['alignment' => 'center']);

        foreach ($angkatanpsp as $i => $item) {
            $table->addRow();
            $table->addCell(1000)->addText($i + 1);
            $table->addCell(3000)->addText($item->bulan);
            $table->addCell(5000)->addText($item->tahun);
        }

        $fileName = 'angkatanpsp.docx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
