<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EvaluasiIdp;
use App\Models\IDP;
use Illuminate\Support\Facades\Auth;

class EvaluasiPascaIdpKaryawanTable extends Component
{

    use WithPagination;

    public $jenisEvaluasi = 'pasca';

    public function render()
    {
        $user = Auth::user();

        // Ambil semua IDP milik user yang sudah direkomendasikan, tapi belum dievaluasi oleh role-nya
        $idps = IDP::where('id_user', $user->id)
            ->whereHas('rekomendasis', function ($q) {
                $q->whereIn('hasil_rekomendasi', ['Disarankan', 'Disarankan dengan Pengembangan']);
            })
            ->whereDoesntHave('evaluasiIdp', function ($q) use ($user) {
                $q->where('jenis_evaluasi', 'pasca')
 ->whereHas('jawaban.bankEvaluasi', function ($sub) {
                        $sub->where('untuk_role', 'karyawan');
                    });
            })
            ->paginate(10);

        return view('livewire.evaluasi-pasca-idp-karyawan-table', [
            'idps' => $idps
        ]);
    }
}
