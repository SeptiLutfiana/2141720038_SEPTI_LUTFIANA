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
            ->whereHas('user', function ($q) use ($mentor) {
                $q->where('id_mentor', $mentor->id);
            })
            ->with(['user', 'rekomendasis', 'evaluasiIdp' => function ($q) use ($mentor) {
                $q->where('jenis_evaluasi', 'onboarding')
                    ->where('id_user', $mentor->id);
            }])
            ->get();

        // Filter: hanya tampilkan jika belum pernah dievaluasi atau sudah lebih dari 14 hari
        $filtered = $all->filter(function ($idp) use ($mentor) {
            $now = Carbon::now();
            $isHMinus1 = Carbon::parse($idp->waktu_selesai)->isSameDay($now->copy()->addDay());
            $lastEval = $idp->evaluasiIdp->sortByDesc('tanggal_evaluasi')->first();

            // 1. Jika belum pernah dievaluasi
            if (!$lastEval) return true;

            // 2. Jika sudah lebih dari 14 hari dari evaluasi terakhir
            if (Carbon::parse($lastEval->tanggal_evaluasi)->diffInDays($now) >= 14) return true;

            // 3. Jika hari ini adalah H-1 dan belum ada rekomendasi
            if ($isHMinus1 && $idp->rekomendasis->isEmpty()) {
                // Jangan tampilkan kalau evaluasi onboarding hari ini sudah dilakukan
                if ($lastEval && Carbon::parse($lastEval->tanggal_evaluasi)->isSameDay($now)) {
                    return false;
                }
                return true;
            }
            return false;
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
