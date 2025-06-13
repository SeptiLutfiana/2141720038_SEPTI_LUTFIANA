<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EvaluasiIdp;
use App\Models\IDP;
use Illuminate\Support\Facades\Auth;

class EvaluasiPascaIdpMentorTable extends Component
{
    use WithPagination;

    public $jenisEvaluasi = 'pasca';

    public function render()
    {
        $mentor = Auth::user(); // user login = mentor

        $idps = IDP::whereHas('rekomendasis', function ($q) {
            $q->whereIn('hasil_rekomendasi', ['Disarankan', 'Disarankan dengan Pengembangan']);
        })
            // Karyawan yang dimentori oleh user ini (mentor)
            ->whereHas('user', function ($q) use ($mentor) {
                $q->where('id_mentor', $mentor->id); // pastikan ada kolom id_mentor di tabel users
            })
            // Belum dievaluasi oleh mentor
            ->whereDoesntHave('evaluasiIdp', function ($q) use ($mentor) {
                $q->where('jenis_evaluasi', 'pasca')
                    ->where('id_user', $mentor->id)
                    ->whereHas('jawaban.bankEvaluasi', function ($sub) {
                        $sub->where('untuk_role', 'mentor');
                    });
            })
            ->with('user') // agar bisa tampil nama karyawannya
            ->paginate(10);

        return view('livewire.evaluasi-pasca-idp-mentor-table', [
            'idps' => $idps
        ]);
    }
}
