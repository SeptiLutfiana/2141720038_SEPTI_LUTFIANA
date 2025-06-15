<?php

namespace App\Livewire;

use App\Models\IDP;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class EvaluasiOnBordingMentorTable extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap'; // Tambahkan ini
    public function render()
    {
        $mentor = Auth::user();

        // Ambil semua IDP aktif milik karyawan yang dimentori oleh user login
        $all = IDP::whereDate('waktu_mulai', '<=', now())
            ->whereDate('waktu_selesai', '>=', now())
            ->whereDoesntHave('rekomendasis') // ✅ Tambahkan ini
            ->whereHas('user', function ($q) use ($mentor) {
                $q->where('id_mentor', $mentor->id);
            })
            ->with(['user', 'evaluasiIdp' => function ($q) use ($mentor) {
                $q->where('jenis_evaluasi', 'onboarding')
                    ->where('id_user', $mentor->id);
            }])
            ->get();

        // Filter: hanya tampilkan jika belum pernah dievaluasi atau sudah lebih dari 14 hari
        $filtered = $all->filter(function ($idp) use ($mentor) {
            $lastEval = $idp->evaluasiIdp->sortByDesc('tanggal_evaluasi')->first();

            if (!$lastEval) return true;

            return Carbon::parse($lastEval->tanggal_evaluasi)->diffInDays(now()) >= 14;
        });

        // Manual pagination
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 5;
        $currentPageItems = $filtered->slice(($page - 1) * $perPage, $perPage)->values();
        $paginated = new LengthAwarePaginator(
            $currentPageItems,
            $filtered->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.evaluasi-on-bording-mentor-table', [
            'idps' => $paginated
        ]);
    }
}
