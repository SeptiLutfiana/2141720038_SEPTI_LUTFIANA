<?php

namespace App\Livewire;

use App\Models\IDP;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class IdpTable extends Component
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
        $idps = IDP::query()
            ->where('is_template', false) // Filter utama untuk Bank IDP
            ->doesntHave('rekomendasis') // Tidak punya data rekomendasi sama sekali
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('karyawan', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    })->orWhereHas('mentor', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    })->orWhereHas('supervisor', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    });
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

        return view('livewire.idp-table', [
            'idps' => $idps,
        ]);
    }
}
