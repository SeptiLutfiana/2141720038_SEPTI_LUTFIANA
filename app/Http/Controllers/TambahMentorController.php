<?php

namespace App\Http\Controllers;

use App\Exports\MentorExport;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class TambahMentorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $mentor = UserRole::with('user') // eager load user
            ->where('id_role', 3) // hanya yang berperan sebagai mentor
            ->when($search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('npk', 'like', "%$search%");
                });
            })
            ->orderByDesc('id') // bisa juga berdasarkan user.name kalau perlu join
            ->paginate(10)
            ->withQueryString();

        return view('adminsdm.data-master.mentor.index', [
            'type_menu' => 'mentor',
            'mentor' => $mentor,
            'search' => $search,
        ]);
    }

    public function create()
    {
        // Ambil ID masing-masing role berdasarkan nama yang sesuai di tabel
        $idMentor = Role::where('nama_role', 'Mentor')->value('id_role');
        $idSupervisor = Role::where('nama_role', 'Supervisor')->value('id_role');
        $idAdmin = Role::where('nama_role', 'Admin SDM')->value('id_role');

        // Validasi jika salah satu role tidak ditemukan
        if (!$idMentor || !$idSupervisor || !$idAdmin) {
            return back()->with('error', 'Data role belum lengkap. Pastikan ada role Mentor, Supervisor, dan Admin SDM di tabel roles.');
        }

        // Ambil semua user ID yang sudah punya salah satu dari role ini
        $excludedUserIds = UserRole::whereIn('id_role', [
            $idMentor,
            $idSupervisor,
            $idAdmin
        ])->pluck('id_user')->unique();

        // Ambil user yang belum punya role tersebut
        $users = User::whereNotIn('id', $excludedUserIds)
            ->orderBy('name')
            ->with(['jenjang', 'jabatan', 'divisi', 'penempatan', 'learninggroup']) // kalau ingin ditampilkan di tom-select
            ->get();

        return view('adminsdm.data-master.mentor.create', [
            'type_menu' => 'mentor',
            'mentor' => $users, // Calon mentor yang belum jadi apa-apa
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id', // Memastikan id_user ada di tabel users
        ]);

        // Cek apakah user sudah menjadi mentor
        $mentor = UserRole::where('id_user', $request->id_user)
            ->where('id_role', 3) // Pastikan id_role yang digunakan adalah 3
            ->first();

        if ($mentor) {
            return back()->with('error', 'User ini sudah menjadi mentor.');
        }

        // Tambahkan role mentor
        UserRole::create([
            'id_user' => $request->id_user,
            'id_role' => 3, // Role ID untuk mentor
        ]);

        return redirect()->route('adminsdm.data-master.mentor.index')->with('msg-success', 'Mentor berhasil ditambahkan.');
    }

    public function show($id)
    {
        $mentor = UserRole::with('user', 'role')
            ->where('id_user', $id) // Gunakan id_user, bukan id
            ->where('id_role', 3) // Pastikan menggunakan id_role
            ->firstOrFail();

        return view('adminsdm.data-master.mentor.detail', [
            'mentor' => $mentor,
            'type_menu' => 'mentor',
        ]);
    }


    public function destroy($id)
    {
        $mentor = UserRole::where('id_user', $id)
            ->where('id_role', 3) // Role ID untuk mentor
            ->firstOrFail();

        $mentor->delete();

        return redirect()->route('adminsdm.data-master.mentor.index')->with('msg-success', 'Mentor berhasil dihapus: ' . $mentor->user->name);
    }
    public function printPDF()
    {
        $mentor = UserRole::with('user')
            ->where('id_role', 3)
            ->get();

        $pdf = Pdf::loadView('adminsdm.data-master.mentor.mentor_pdf', [
            'mentor' => $mentor,
            'type_menu' => 'mentor',
        ]);
        return $pdf->stream('data-mentor.pdf');
    }
    public function exportExcel()
    {
        return Excel::download(new MentorExport, 'data-mentor.xlsx');
    }

    public function exportCSV()
    {
        return Excel::download(new MentorExport, 'data-mentor.csv');
    }
    public function exportWord()
    {
        $mentor = UserRole::with('user')
            ->where('id_role', 3)
            ->get();

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $section->addText('Data Mentor Individual Development Plan Perum Perhutani', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
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
        foreach ($mentor as $i => $item) {
            $table->addRow();
            $table->addCell()->addText($i + 1);
            $table->addCell()->addText($item->user->name);
            $table->addCell()->addText($item->user->npk);
            $table->addCell()->addText($item->user->no_hp);
            $table->addCell()->addText($item->user->jabatan->nama_jabatan);
            $table->addCell()->addText($item->user->penempatan->nama_penempatan);
            $table->addCell()->addText($item->user->divisi->nama_divisi);
        }

        $filename = 'data-mentor.docx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
