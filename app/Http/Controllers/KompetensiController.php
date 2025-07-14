<?php

namespace App\Http\Controllers;

use App\Exports\HardKompetensiExport;
use App\Exports\KompetensiExport;
use App\Imports\KompetensiImport;
use App\Models\Jabatan;
use App\Models\Jenjang;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Kompetensi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Carbon;
use PhpOffice\PhpWord\SimpleType\Jc;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Support\Facades\Log;

class KompetensiController extends Controller
{
    public function indexSoft(Request $request)
    {
        $search = $request->query('search');
        $id_jenjang = $request->query('id_jenjang');
        $id_jabatan = $request->query('id_jabatan');

        $listJenjang = Jenjang::all();
        $listJabatan = Jabatan::all();

        $kompetensi = Kompetensi::where('jenis_kompetensi', 'Soft Kompetensi')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama_kompetensi', 'like', "%$search%")
                        ->orWhere('keterangan', 'like', "%$search%");
                });
            })
            ->when($id_jenjang, function ($query, $id_jenjang) {
                return $query->where('id_jenjang', $id_jenjang);
            })
            ->when($id_jabatan, function ($query, $id_jabatan) {
                return $query->where('id_jabatan', $id_jabatan);
            })
            ->orderBy('nama_kompetensi')
            ->paginate(10)
            ->withQueryString(); // mempertahankan query di pagination

        return view('adminsdm.data-master.kompetensi.index-soft', [
            'type_menu'     => 'kompetensi',
            'kompetensi'    => $kompetensi,
            'listJenjang'   => $listJenjang,
            'id_jenjang'    => $id_jenjang,
            'listJabatan'   => $listJabatan,
            'id_jabatan'    => $id_jabatan,
            'search'        => $search,
        ]);
    }
    public function indexHard(Request $request)
    {
        $search = $request->query('search');
        $id_jenjang = $request->query('id_jenjang');
        $id_jabatan = $request->query('id_jabatan');

        $listJenjang = Jenjang::all();
        $listJabatan = Jabatan::all();

        $kompetensi = Kompetensi::where('jenis_kompetensi', 'Hard Kompetensi')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama_kompetensi', 'like', "%$search%")
                        ->orWhere('keterangan', 'like', "%$search%");
                });
            })
            ->when($id_jenjang, function ($query, $id_jenjang) {
                return $query->where('id_jenjang', $id_jenjang);
            })
            ->orderBy('nama_kompetensi')
            ->paginate(10)
            ->withQueryString();

        return view('adminsdm.data-master.kompetensi.index-hard', [
            'type_menu'     => 'kompetensi',
            'kompetensi'    => $kompetensi,
            'listJenjang'   => $listJenjang,
            'id_jenjang'    => $id_jenjang,
            'listJabatan'   => $listJabatan,
            'id_jabatan'    => $id_jabatan,
            'search'        => $search,
        ]);
    }

    public function create()
    {
        $jenjang = Jenjang::all();
        $jabatan = Jabatan::all();

        return view('adminsdm.data-master.kompetensi.create', [
            'type_menu' => 'kompetensi',
            'jenjang' => $jenjang,
            'jabatan' => $jabatan,

        ]);
    }
    public function getJabatanByJenjang($id_jenjang)
    {
        $jabatan = Jabatan::where('id_jenjang', $id_jenjang)->get(['id_jabatan', 'nama_jabatan']);
        return response()->json($jabatan);
    }


    public function store(Request $request)
    {
        // Manual Input
        if ($request->filled('input_manual')) {

            // Validasi umum
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

            // Jika Hard Kompetensi, validasi tambahan
            if ($request->jenis_kompetensi === 'Hard Kompetensi') {
                $request->validate([
                    'id_jenjang' => 'required|exists:jenjangs,id_jenjang',
                    'id_jabatan' => 'required|exists:jabatans,id_jabatan',
                ]);
            }

            // Cek duplikat (Hard atau Soft)
            $query = \App\Models\Kompetensi::where('nama_kompetensi', $request->nama_kompetensi)
                ->where('jenis_kompetensi', $request->jenis_kompetensi);

            if ($request->jenis_kompetensi === 'Hard Kompetensi') {
                $query->where('id_jenjang', $request->id_jenjang)
                    ->where('id_jabatan', $request->id_jabatan);
            }

            if ($query->exists()) {
                return redirect()->back()->with('msg-error', 'Data Kompetensi tersebut sudah ada.');
            }

            // Simpan
            $kompetensi = Kompetensi::create([
                'id_jenjang' => $request->jenis_kompetensi === 'Hard Kompetensi' ? $request->id_jenjang : null,
                'id_jabatan' => $request->jenis_kompetensi === 'Hard Kompetensi' ? $request->id_jabatan : null,
                'nama_kompetensi' => $request->nama_kompetensi,
                'jenis_kompetensi' => $request->jenis_kompetensi,
                'keterangan' => $request->keterangan,
            ]);

            $route = $kompetensi->jenis_kompetensi === 'Soft Kompetensi'
                ? 'adminsdm.data-master.kompetensi.indexSoft'
                : 'adminsdm.data-master.kompetensi.indexHard';

            return redirect()->route($route)
                ->with('msg-success', $kompetensi->jenis_kompetensi . ' ' . $request->nama_kompetensi . ' berhasil ditambahkan.');
        }

        // Upload File
        $request->validate([
            'file_import' => 'required|mimes:xlsx,csv|max:512',
        ], [
            'file_import.required' => 'File harus diupload.',
            'file_import.mimes' => 'Format harus .xlsx atau .csv.',
            'file_import.max' => 'Ukuran maksimal 0.5MB.',
        ]);

        try {
            $import = new \App\Imports\KompetensiImport;
            $collection = Excel::toCollection($import, $request->file('file_import'))[0];

            $countHard = 0;
            $countSoft = 0;

            foreach ($collection as $row) {
                $jenis = ucwords(strtolower(trim($row['jenis_kompetensi'] ?? '')));
                if ($jenis === 'Hard Kompetensi') $countHard++;
                elseif ($jenis === 'Soft Kompetensi') $countSoft++;
            }

            // Lakukan import ke DB
            Excel::import($import, $request->file('file_import'));

            // Jika tidak ada data yang berhasil
            if ($import->barisBerhasil === 0) {
                return redirect()->back()
                    ->with('msg-error', 'Tidak ada data yang berhasil diimpor.')
                    ->with('duplikat', $import->duplikat);
            }

            $route = $countHard >= $countSoft
                ? 'adminsdm.data-master.kompetensi.indexHard'
                : 'adminsdm.data-master.kompetensi.indexSoft';

            return redirect()->route($route)
                ->with('msg-success', "Berhasil mengimpor {$import->barisBerhasil} data kompetensi.")
                ->with('duplikat', $import->duplikat);
        } catch (\Exception $e) {
            return redirect()->back()->with('msg-error', 'Gagal mengimpor file: ' . $e->getMessage());
        }
    }

    public function showSoft($id)
    {
        $kompetensi = Kompetensi::findOrFail($id);

        return view('adminsdm.data-master.kompetensi.detail-soft', [
            'kompetensi'    => $kompetensi,
            'type_menu' => 'kompetensi',
        ]);
    }
    public function showHard($id)
    {
        $kompetensi = Kompetensi::findOrFail($id);

        return view('adminsdm.data-master.kompetensi.detail-hard', [
            'kompetensi'    => $kompetensi,
            'type_menu' => 'kompetensi',
        ]);
    }
    public function edit($id)
    {
        $jenjang = Jenjang::all();
        $jabatan = Jabatan::all();
        $kompetensi = Kompetensi::findOrFail($id);
        return view('adminsdm.data-master.kompetensi.edit', [
            'kompetensi'    => $kompetensi,
            'jenjang' => $jenjang,
            'jabatan' => $jabatan,
            'type_menu' => 'kompetensi',
        ]);
    }

    public function update(Request $request, $id)
    {
        $kompetensi = Kompetensi::findOrFail($id);

        $baseRules = [
            'nama_kompetensi' => 'required|string',
            'keterangan' => 'required|string',
        ];

        if ($kompetensi->jenis_kompetensi === 'Hard Kompetensi') {
            $baseRules['id_jenjang'] = 'required|exists:jenjangs,id_jenjang';
            $baseRules['id_jabatan'] = 'required|exists:jabatans,id_jabatan';
        }

        $validated = $request->validate($baseRules);

        // Update berdasarkan jenis
        if ($kompetensi->jenis_kompetensi === 'Hard Kompetensi') {
            $kompetensi->update($validated);
        } else {
            $kompetensi->update([
                'nama_kompetensi' => $validated['nama_kompetensi'],
                'keterangan' => $validated['keterangan'],
            ]);
        }

        // Redirect ke index yang sesuai
        $route = $kompetensi->jenis_kompetensi === 'Soft Kompetensi'
            ? 'adminsdm.data-master.kompetensi.indexSoft'
            : 'adminsdm.data-master.kompetensi.indexHard';

        return redirect()->route($route)
            ->with('msg-success', $kompetensi->jenis_kompetensi . ' ' . $kompetensi->nama_kompetensi . ' berhasil diperbarui.');
    }

    public function destroy(Kompetensi $kompetensi)
    {
        $nama = $kompetensi->nama_kompetensi;
        $jenis = $kompetensi->jenis_kompetensi;

        $kompetensi->delete(); // Hapus setelah ambil data penting

        if ($jenis === 'Soft Kompetensi') {
            return redirect()->route('adminsdm.data-master.kompetensi.indexSoft')
                ->with('msg-success', 'Soft Kompetensi ' . $nama . ' berhasil dihapus.');
        } else {
            return redirect()->route('adminsdm.data-master.kompetensi.indexHard')
                ->with('msg-success', 'Hard Kompetensi ' . $nama . ' berhasil dihapus.');
        }
    }

    public function printPdfSoft()
    {
        $kompetensi = Kompetensi::where('jenis_kompetensi', 'Soft Kompetensi')->get();
        $waktuCetak = Carbon::now()->translatedFormat('d F Y H:i');
        $pdf = Pdf::loadView('adminsdm.data-master.kompetensi.soft-kompetensi-pdf', [
            'kompetensi' => $kompetensi,
            'waktuCetak' => $waktuCetak,
            'jenis' => 'Soft Kompetensi',
            'type_menu' => 'kompetensi',
        ]);
        return $pdf->stream('soft-kompetensi.pdf'); // atau ->download('learning-group.pdf')
    }
    public function printPdfHard()
    {
        $kompetensi = Kompetensi::where('jenis_kompetensi', 'Hard Kompetensi')
            ->get()
            ->sortBy([
                fn($a, $b) => $a->jenjang->nama_jenjang <=> $b->jenjang->nama_jenjang,
                fn($a, $b) => $a->jabatan->nama_jabatan <=> $b->jabatan->nama_jabatan,
                fn($a, $b) => $a->nama_kompetensi <=> $b->nama_kompetensi,
            ]);
        $waktuCetakHard = Carbon::now()->translatedFormat('d F Y H:i');
        $pdf = Pdf::loadView('adminsdm.data-master.kompetensi.hard-kompetensi-pdf', [
            'kompetensi' => $kompetensi,
            'jenis' => 'Hard Kompetensi',
            'waktuCetakHard' => $waktuCetakHard,
            'type_menu' => 'kompetensi',
        ]);
        return $pdf->stream('hard-kompetensi.pdf'); // atau ->download('learning-group.pdf')
    }
    public function exportExcelSoft()
    {
        return Excel::download(new KompetensiExport, 'data-soft-kompetensi.xlsx');
    }

    public function exportCSVSoft()
    {
        return Excel::download(new KompetensiExport, 'data-soft-kompetensi.csv');
    }
    public function exportExcelHard()
    {
        return Excel::download(new HardKompetensiExport, 'data-hard-kompetensi.xlsx');
    }

    public function exportCSVHard()
    {
        return Excel::download(new HardKompetensiExport, 'data-hard-kompetensi.csv');
    }
    public function exportDocxSoft()
    {
        $kompetensi = Kompetensi::all();
        $softKompetensi = $kompetensi->where('jenis_kompetensi', 'Soft Kompetensi');

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // === Gunakan Tabel untuk logo dan judul ===
        $headerTable = $section->addTable();
        $headerTable->addRow();

        // Kolom 1: Logo
        $cellLogo = $headerTable->addCell(1500); // Lebar disesuaikan
        $cellLogo->addImage(public_path('./img/logo-perhutani.png'), [
            'width' => 80,
            'height' => 80,
            'wrappingStyle' => 'square',
            'alignment' => Jc::LEFT,
        ]);

        // Kolom 2: Judul
        $cellTitle = $headerTable->addCell(8000); // Lebar disesuaikan
        $cellTitle->addText(
            'Data Soft Kompetensi Individual Development Plan Perum Perhutani',
            ['bold' => true, 'size' => 16],
            ['alignment' => Jc::CENTER]
        );

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
        $table->addCell(5000)->addText("Keterangan", ['bold' => true]);

        foreach ($softKompetensi->values() as $i => $item) {
            $table->addRow();
            $table->addCell(1000)->addText($i + 1); // Akan mulai dari 1 juga
            $table->addCell(3000)->addText($item->nama_kompetensi);
            $table->addCell(5000)->addText($item->keterangan);
        }

        $fileName = 'soft-kompetensi.docx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
    public function exportDocxHard()
    {
        $hardKompetensi = Kompetensi::with('jenjang', 'jabatan')
            ->where('jenis_kompetensi', 'Hard Kompetensi')
            ->get() // <= ini penting agar menjadi Collection
            ->sortBy([
                fn($a, $b) => $a->jenjang->nama_jenjang <=> $b->jenjang->nama_jenjang,
                fn($a, $b) => $a->jabatan->nama_jabatan <=> $b->jabatan->nama_jabatan,
                fn($a, $b) => $a->nama_kompetensi <=> $b->nama_kompetensi,
            ]);


        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // === Gunakan Tabel untuk logo dan judul ===
        $headerTable = $section->addTable();
        $headerTable->addRow();

        // Kolom 1: Logo
        $cellLogo = $headerTable->addCell(1500); // Lebar disesuaikan
        $cellLogo->addImage(public_path('./img/logo-perhutani.png'), [
            'width' => 80,
            'height' => 80,
            'wrappingStyle' => 'square',
            'alignment' => Jc::LEFT,
        ]);

        // Kolom 2: Judul
        $cellTitle = $headerTable->addCell(8000); // Lebar disesuaikan
        $cellTitle->addText(
            'Data Soft Kompetensi Individual Development Plan Perum Perhutani',
            ['bold' => true, 'size' => 16],
            ['alignment' => Jc::CENTER]
        );

        $section->addTextBreak(1);

        // Judul
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
        $table->addCell(3000)->addText("Jenjang", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(3000)->addText("Jabatan", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(5000)->addText("Keterangan", ['bold' => true], ['alignment' => 'center']);

        foreach ($hardKompetensi->values() as $i => $item) {
            $table->addRow();
            $table->addCell(1000)->addText($i + 1); // Akan selalu mulai dari 1
            $table->addCell(3000)->addText($item->nama_kompetensi);
            $table->addCell(3000)->addText($item->jenjang->nama_jenjang);
            $table->addCell(3000)->addText($item->jabatan->nama_jabatan);
            $table->addCell(5000)->addText($item->keterangan);
        }
        $fileName = 'hard-kompetensi.docx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
