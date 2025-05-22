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
            'type_menu' => 'LG',
            'LG' => $LG,
            'search' => $search,
        ]);
    }
    public function create()
    {
      return view('adminsdm.data-master.karyawan.learning-group.create', [
          'type_menu' => 'tambah-LG',
      ]);
    }
    public function store(Request $request)
  {
      // Cek apakah user menggunakan form input manual
      if ($request->filled('input_manual')) {
          // Validasi untuk input manual
          $request->validate([
              'nama_LG' => 'required|string',
              'keterangan' => 'required|string',
          ], [
              'nama_LG.required' => 'Nama Learning Group harus diisi',
              'keterangan.required' => 'Keterangan harus diisi',
          ]);
  
          LearingGroup::create([
              'nama_LG' => $request->nama_LG,
              'keterangan' => $request->keterangan,
          ]);
  
          return redirect()->route('adminsdm.data-master.karyawan.learning-group.index')
              ->with('msg-success', 'Berhasil menambahkan data learning group ' . $request->nama_LG);
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
          Excel::import(new LearningGroupImport, $request->file('file_import'));
  
          // Redirect ke halaman Data dengan pesan sukses
          return redirect()->route('adminsdm.data-master.karyawan.learning-group.index')
              ->with('msg-success', 'Berhasil mengimpor data LG dari file.');
      } catch (\Exception $e) {
          // Jika ada error saat mengimpor, tangani dan tampilkan pesan error
          return redirect()->back()->with('msg-error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
      }
  }
  
      // Kalau tidak dua-duanya
      return redirect()->back()->with('msg-error', 'Tidak ada data yang dikirim.');
  }
  
    public function show($id)
    {
        $LG = LearingGroup::findOrFail($id);
    
        return view('adminsdm.data-master.karyawan.learning-group.detail', [
            'LG'    => $LG,
            'type_menu' => 'LG', 
        ]);
    }  
    public function edit($id)
  {
      $LG = LearingGroup::findOrFail($id);
      return view('adminsdm.data-master.karyawan.learning-group.edit', [
          'LG'    => $LG,
          'type_menu' => 'LG', 
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
            'type_menu' => 'LG',
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
