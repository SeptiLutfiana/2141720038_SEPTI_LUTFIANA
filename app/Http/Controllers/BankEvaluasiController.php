<?php

namespace App\Http\Controllers;

use App\Exports\BankEvaluasiExport;
use App\Models\BankEvaluasi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;


class BankEvaluasiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $jenisEvaluasi = $request->query('jenis_evaluasi');
        $tipePertanyaan = $request->query('tipe_pertanyaan');
        $untukRole = $request->query('untuk_role');

        $bankEvaluasi = BankEvaluasi::when($search, function ($query, $search) {
            return $query->where('pertanyaan', 'like', "%$search%")
                ->orWhere('jenis_evaluasi', 'like', "%$search%")
                ->orWhere('tipe_pertanyaan', 'like', "%$search%")
                ->orWhere('untuk_role', 'like', "%$search%");
        })
            ->when($jenisEvaluasi, function ($query, $jenisEvaluasi) {
                return $query->where('jenis_evaluasi', $jenisEvaluasi);
            })
            ->when($tipePertanyaan, function ($query, $tipePertanyaan) {
                return $query->where('tipe_pertanyaan', $tipePertanyaan);
            })
            ->when($untukRole, function ($query, $untukRole) {
                return $query->where('untuk_role', $untukRole);
            })

            ->orderBy('pertanyaan')
            ->paginate(5)
            ->withQueryString();

        return view('adminsdm.BankEvaluasi.index', [
            'type_menu' => 'evaluasi',
            'evaluasi' => $bankEvaluasi,
            'search' => $search,
            'jenisEvaluasi' => $jenisEvaluasi,
            'tiperPertanyaan' => $tipePertanyaan,
            'untuk_role' => $untukRole,
        ]);
    }
    public function create()
    {
        return view('adminsdm.BankEvaluasi.create', [
            'type_menu' => 'evaluasi',
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'jenis_evaluasi' => 'required|in:onboarding,pasca',
            'untuk_role' => 'required|in:karyawan,mentor,supervisor',
            'tipe_pertanyaan' => 'required|in:likert,esai',
            'pertanyaan' => 'required|string',
        ], [
            'jenis_evaluasi.required' => 'Jenis evaluasi wajib diisi.',
            'untuk_role.required' => 'Role target wajib diisi.',
            'tipe_pertanyaan.required' => 'Tipe pertanyaan wajib diisi.',
            'pertanyaan.required' => 'Isi pertanyaan wajib diisi.',
        ]);

        BankEvaluasi::create($request->all());

        return redirect()->route('adminsdm.BankEvaluasi.index')->with('msg-success', 'Pertanyaan evaluasi berhasil ditambahkan.');
    }
    public function show($id)
    {
        $bankEvaluasi = BankEvaluasi::findOrFail($id);

        return view('adminsdm.BankEvaluasi.detail', [
            'bankEvaluasi'    => $bankEvaluasi,
            'type_menu' => 'evaluasi',
        ]);
    }
    public function edit($id)
    {
        $bankEvaluasi = BankEvaluasi::findOrFail($id);
        return view('adminsdm.BankEvaluasi.edit', [
            'bankEvaluasi'    => $bankEvaluasi,
            'type_menu' => 'evaluasi',
        ]);
    }
    public function update(Request $request, $id)
    {
        // Validasi data input
        $request->validate([
            'jenis_evaluasi' => 'required|in:onboarding,pasca',
            'untuk_role' => 'required|in:karyawan,mentor,supervisor',
            'tipe_pertanyaan' => 'required|in:likert,esai',
            'pertanyaan' => 'required|string|max:1000',
        ]);

        // Cari data berdasarkan ID
        $bankEvaluasi = BankEvaluasi::findOrFail($id);

        // Update data
        $bankEvaluasi->jenis_evaluasi = $request->jenis_evaluasi;
        $bankEvaluasi->untuk_role = $request->untuk_role;
        $bankEvaluasi->tipe_pertanyaan = $request->tipe_pertanyaan;
        $bankEvaluasi->pertanyaan = $request->pertanyaan;
        $bankEvaluasi->save();

        // Redirect kembali ke index dengan pesan sukses
        return redirect()->route('adminsdm.BankEvaluasi.index')
            ->with('msg-success', 'Pertanyaan evaluasi berhasil diperbarui.');
    }
    public function destroy(BankEvaluasi $bankEvaluasi)
    {
        $bankEvaluasi->delete();
        return redirect()->route('adminsdm.BankEvaluasi.index')->with('msg-success', 'Berhasil menghapus pertanyaan');
    }
    public function printPdf()
    {
        $bankEvaluasi = BankEvaluasi::orderByRaw("FIELD(untuk_role, 'karyawan', 'mentor', 'supervisor')")
            ->orderBy('id_bank_evaluasi') // opsional, untuk urut dalam masing-masing role
            ->get();
        $waktuCetak = Carbon::now()->translatedFormat('d F Y H:i');

        $pdf = Pdf::loadView('adminsdm.BankEvaluasi.bankevaluasi_pdf', [
            'bankEvaluasi' => $bankEvaluasi,
            'type_menu' => 'evaluasi',
            'waktuCetak' => $waktuCetak,
        ])->setPaper('a4', 'landscape');;
        return $pdf->stream('data-evaluasi.pdf'); // atau ->download('learning-group.pdf')
    }
    public function exportExcel()
    {
        return Excel::download(new BankEvaluasiExport, 'data-pertanyaan-evalausi.xlsx');
    }

    public function exportCSV()
    {
        return Excel::download(new BankEvaluasiExport, 'data-pertanyaan-evaluasi.csv');
    }
    public function exportDocx()
    {
        $bankEvaluasi = BankEvaluasi::orderByRaw("FIELD(untuk_role, 'karyawan', 'mentor', 'supervisor')")
            ->orderBy('id_bank_evaluasi')
            ->get();
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
        $section->addText('Data Pertanyaan Evaluasi', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addTextBreak(1);

        // Tabel data
        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80,
        ]);
        $table->addRow();
        $table->addCell(1000)->addText("No", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(3000)->addText("Pertanyaan", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(5000)->addText("Untuk Role", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(5000)->addText("Tipe Pertanyaan", ['bold' => true], ['alignment' => 'center']);
        $table->addCell(5000)->addText("Jenis Evaluasi", ['bold' => true], ['alignment' => 'center']);
        foreach ($bankEvaluasi as $i => $item) {
            $table->addRow();
            $table->addCell(1000)->addText($i + 1);
            $table->addCell(3000)->addText($item->pertanyaan);
            $table->addCell(5000)->addText($item->untuk_role);
            $table->addCell(5000)->addText($item->tipe_pertanyaan);
            $table->addCell(5000)->addText($item->jenis_evaluasi);
        }

        $fileName = 'pertanyaan-evaluasi.docx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
