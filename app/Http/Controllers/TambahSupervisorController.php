<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\SupervisorExport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class TambahSupervisorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $supervisor = UserRole::with('user')
            ->where('id_role', 2)
            ->when($search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('npk', 'like', "%$search%");
                });
            })
            ->orderByDesc('id') // bisa juga berdasarkan user.name kalau perlu join
            ->paginate(10)
            ->withQueryString();

        return view('adminsdm.data-master.supervisor.index', [
            'type_menu' => 'supervisor',
            'supervisor' => $supervisor,
            'search' => $search,
        ]);
    }

    public function create()
    {

        $role = Role::where('nama_role', 'supervisor')->first();


        $existingSupervisorIds = UserRole::where('id_role', $role->id_role)->pluck('id_user');

        $supervisor = User::whereNotIn('id', $existingSupervisorIds)
            ->orderBy('name')
            ->get();

        return view('adminsdm.data-master.supervisor.create', [
            'type_menu' => 'supervisor',
            'supervisor' => $supervisor,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id', // Memastikan id_user ada di tabel users
        ]);


        $supervisor = UserRole::where('id_user', $request->id_user)
            ->where('id_role', 2) // Pastikan id_role yang digunakan adalah 2
            ->first();

        if ($supervisor) {
            return back()->with('error', 'User ini sudah menjadi supervisor.');
        }

        UserRole::create([
            'id_user' => $request->id_user,
            'id_role' => 2, // R
        ]);

        return redirect()->route('adminsdm.data-master.supervisor.index')->with('msg-success', 'Supervisor berhasil ditambahkan.');
    }

    public function show($id)
    {
        $supervisor = UserRole::with('user', 'role')
            ->where('id_user', $id) // Gunakan id_user, bukan id
            ->where('id_role', 2) // Pastikan menggunakan id_role
            ->firstOrFail();

        return view('adminsdm.data-master.supervisor.detail', [
            'supervisor' => $supervisor,
            'type_menu' => 'supervisor',
        ]);
    }

    public function destroy($id)
    {
        $supervisor = UserRole::where('id_user', $id)
            ->where('id_role', 2)
            ->firstOrFail();

        $supervisor->delete();

        return redirect()->route('adminsdm.data-master.supervisor.index')->with('msg-success', 'Supervisor berhasil dihapus: ' . $supervisor->user->name);
    }
    public function printPDF()
    {
        $supervisor = UserRole::with('user')
            ->where('id_role', 2)
            ->get();

        $pdf = Pdf::loadView('adminsdm.data-master.supervisor.supervisor_pdf', [
            'supervisor' => $supervisor,
            'type_menu' => 'supervisor',
        ]);
        return $pdf->stream('data-supervisor.pdf');
    }
    public function exportExcel()
    {
        return Excel::download(new SupervisorExport, 'data-supervisor.xlsx');
    }

    public function exportCSV()
    {
        return Excel::download(new SupervisorExport, 'data-supervisor.csv');
    }
    public function exportWord()
    {
        $supervisor = UserRole::with('user')
            ->where('id_role', 2)
            ->get();

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $section->addText('Data Supervisor Individual Development Plan Perum Perhutani', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addTextBreak();

        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);
        $table->addRow();
        $table->addCell()->addText('No', ['bold' => true]);
        $table->addCell()->addText('Nama', ['bold' => true]);
        $table->addCell()->addText('NPK', ['bold' => true]);
        $table->addCell()->addText('No HP', ['bold' => true]);
        $table->addCell()->addText('Jabatan', ['bold' => true]);
        $table->addCell()->addText('Penempatan', ['bold' => true]);
        $table->addCell()->addText('Divisi', ['bold' => true]);
        foreach ($supervisor as $i => $item) {
            $table->addRow();
            $table->addCell()->addText($i + 1);
            $table->addCell()->addText($item->user->name);
            $table->addCell()->addText($item->user->npk);
            $table->addCell()->addText($item->user->no_hp);
            $table->addCell()->addText($item->user->jabatan->nama_jabatan);
            $table->addCell()->addText($item->user->penempatan->nama_penempatan);
            $table->addCell()->addText($item->user->divisi->nama_divisi);
        }

        $filename = 'data-supervisor.docx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
