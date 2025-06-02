<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\IDP;
use Illuminate\Http\Request;
use App\Models\Jenjang;
use App\Models\LearingGroup;
use App\Models\IdpKompetensiPengerjaan;
use Illuminate\Notifications\DatabaseNotification;
use App\Notifications\PenilaianDiperbaruiNotification;

class MentorDashboardController extends Controller
{
    public function index()
    {
        return view('mentor.dashboard-mentor', [
            'type_menu' => 'mentor',
        ]);
    }
    public function indexMentor(Request $request)
    {
        $user = Auth::user(); // Ambil user yang sedang login
        $search = $request->query('search');
        $id_jenjang = $request->query('id_jenjang');
        $id_LG = $request->query('lg');
        $tahun = $request->query('tahun');
        $listTahun = IDP::whereNotNull('waktu_mulai')
            ->selectRaw('YEAR(waktu_mulai) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');
        $listJenjang = Jenjang::all();
        $listLG = LearingGroup::all();
        $idps = IDP::with([
            'mentor',
            'supervisor',
            'idpKompetensis.kompetensi',
            'idpKompetensis.metodeBelajars'
        ])
            ->where('id_mentor', $user->id) // Ambil IDP hanya milik user login
            ->when($search, function ($query, $search) {
                return $query->whereHas('mentor', function ($q) use ($search) {
                    $q->where('proyeksi_karir', 'like', "%$search%")
                        ->orWhere('id_mentor', 'like', "%$search%");
                });
            })
            ->when($id_jenjang, function ($query, $id_jenjang) {
                return $query->whereHas('mentor', function ($q) use ($id_jenjang) {
                    $q->where('id_jenjang', $id_jenjang);
                });
            })
            ->when($id_LG, function ($query, $id_LG) {
                return $query->whereHas('mentor', function ($q) use ($id_LG) {
                    $q->where('lg', $id_LG);
                });
            })
            ->when($tahun, function ($query, $tahun) {
                return $query->whereYear('waktu_mulai', $tahun);
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('mentor.IDP.index', [
            'idps' => $idps,
            'listJenjang' => $listJenjang,
            'listLG' => $listLG,
            'listTahun' => $listTahun,
            'search' => $search,
            'id_jenjang' => $id_jenjang,
            'lg' => $id_LG,
            'tahun' => $tahun,
            'type_menu' => 'mentor',
        ]);
    }
    public function showMentor($id, Request $request)
    {
        if ($request->has('notification_id')) {
            $notification = DatabaseNotification::find($request->notification_id);

            if ($notification && $notification->notifiable_id == Auth::id()) {
                $notification->markAsRead();
            }
        }
        $idps = IDP::with([
            'karyawan',
            'mentor',
            'supervisor',
            'idpKompetensis.kompetensi',
            'idpKompetensis.metodeBelajars',
            'idpKompetensis.pengerjaans'
        ])->findOrFail($id);
        $pengerjaansQuery = IdpKompetensiPengerjaan::whereHas('idpKompetensi', function ($q) use ($id) {
            $q->where('id_idp', $id);
        })->orderBy('created_at', 'desc');

        $pengerjaans = $pengerjaansQuery->paginate(5)->withQueryString();

        // Ambil id_pengerjaan dari query string jika ada
        $highlightPengerjaan = null;
        if ($request->has('pengerjaan')) {
            $highlightPengerjaan = IdpKompetensiPengerjaan::with([
                'idpKompetensi.kompetensi',
            ])->find($request->pengerjaan);
        }
        return view('mentor.IDP.detail', [
            'idps' => $idps,
            'pengerjaans' => $pengerjaans,
            'highlightPengerjaan' => $highlightPengerjaan,
            'type_menu' => 'karyawan',
        ]);
    }
    public function updatePenilaian(Request $request, $id)
    {
        $request->validate([
            'status_pengerjaan' => 'required',
            'saran' => 'nullable|string'
        ]);

        $pengerjaan = IdpKompetensiPengerjaan::findOrFail($id);
        $pengerjaan->status_pengerjaan = $request->status_pengerjaan;
        $pengerjaan->saran = $request->saran;
        $pengerjaan->save();
        $user = Auth::user();
        // Ambil karyawan dari relasi
        $karyawan = $pengerjaan->idpKompetensi->idp->karyawan;

        // Buat pesan sesuai status
        $pesanStatus = match ($request->status_pengerjaan) {
            'Menunggu Persetujuan' => 'IDP Anda sedang menunggu persetujuan dari mentor.',
            'Disetujui Mentor' => 'Pengerjaan IDP Anda telah disetujui oleh mentor.',
            'Ditolak Mentor' => 'Pengerjaan IDP Anda ditolak oleh mentor.',
            'Revisi Mentor' => 'Pengerjaan IDP Anda perlu direvisi sesuai saran mentor.',
            default => 'Penilaian IDP Anda telah diperbarui oleh mentor.'
        };

        // Kirim notifikasi
        $karyawan->notify(new PenilaianDiperbaruiNotification([
            'id_idp' => $pengerjaan->idpKompetensi->idp->id_idp,
            'id_idpKomPeng' => $pengerjaan->id_idpKomPeng,
            'status' => $request->status_pengerjaan,
            'saran' => $request->saran,
            'nama_mentor' => $user->name,
            'untuk_role' => 'karyawan',
            'message' => $pesanStatus,
        ]));
        return redirect()->back()->with('success', 'Penilaian berhasil diperbarui.');
    }
}
