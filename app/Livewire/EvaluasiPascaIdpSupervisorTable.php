<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EvaluasiIdp;
use Illuminate\Support\Facades\Auth;
use App\Models\IDP;
class EvaluasiPascaIdpSupervisorTable extends Component
{

     use WithPagination;

    public $jenisEvaluasi = 'pasca';

    public function render()
    {
        $supervisor = Auth::user(); // user login = mentor

        $idps = IDP::whereHas('rekomendasis', function ($q) {
            $q->whereIn('hasil_rekomendasi', ['Disarankan', 'Disarankan dengan Pengembangan']);
        })
            // Karyawan yang dimentori oleh user ini (mentor)
            ->whereHas('user', function ($q) use ($supervisor) {
                $q->where('id_supervisor', $supervisor->id); // pastikan ada kolom id_mentor di tabel users
            })
            // Belum dievaluasi oleh mentor
            ->whereDoesntHave('evaluasiIdp', function ($q) use ($supervisor) {
                $q->where('jenis_evaluasi', 'pasca')
                    ->where('id_user', $supervisor->id)
                    ->whereHas('jawaban.bankEvaluasi', function ($sub) {
                        $sub->where('untuk_role', 'supervisor');
                    });
            })
            ->with('user') // agar bisa tampil nama karyawannya
            ->paginate(10);

        return view('livewire.evaluasi-pasca-idp-supervisor-table', [
            'idps' => $idps
        ]);
    }
}
