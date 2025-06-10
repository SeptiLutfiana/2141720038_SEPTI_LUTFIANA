<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\IDP;
use App\Models\User;

class RiwayatIdpMentorTable extends Component
{
    use WithPagination;
    public $search;
    public $jenjang;
    public $lg;
    public $tahun;
    protected string $paginationTheme = 'bootstrap';
    protected $updatesQueryString = ['search', 'jenjang', 'lg', 'tahun'];

    public function mount()
    {
        // Mengambil search query dari URL
        $this->search = request()->query('search');
        $this->tahun = request()->query('tahun');
    }
    public function deleteId($id)
    {
        if ($user = User::find($id)) {
            $user->delete();
            session()->flash('msg-success', 'berhasil dihapus');
        } else {
            session()->flash('msg-error', 'tidak ditemukan');
        }
    }

    public function render()
    {
        $user = Auth::user(); // Ambil user yang sedang login
        $idps = IDP::query()
            ->where('is_template', false) // Filter utama untuk Bank IDP
            ->where('id_mentor', $user->id) // Ambil IDP hanya milik user login
            ->whereHas('rekomendasis', function ($q) {
                $q->whereNotNull('hasil_rekomendasi')
                    ->where('hasil_rekomendasi', '!=', '');
            })
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('karyawan', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    })->orWhereHas('mentor', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    })->orWhereHas('supervisor', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    })->orWhereHas('learningGroup', function ($q2) use ($search) {
                        $q2->where('nama_LG', 'like', "%$search%");
                    })->orWhereHas('rekomendasis', function ($q2) use ($search) {
                        $q2->where('hasil_rekomendasi', 'like', "%$search%");
                    })->orWhere('proyeksi_karir', 'like', "%$search%");
                });
            })
            ->when($this->jenjang, function ($query) {
                return $query->where('id_jenjang', $this->jenjang);
            })
            ->when($this->lg, function ($query) {
                return $query->where('id_LG', $this->lg);
            })
            ->when($this->tahun, function ($query) {
                return $query->whereYear('waktu_mulai', $this->tahun);
            })
            ->orderBy('proyeksi_karir')
            ->paginate(5)
            ->withQueryString();

        return view('livewire.riwayat-idp-mentor-table', [
            'idps' => $idps,
        ]);
    }
}
