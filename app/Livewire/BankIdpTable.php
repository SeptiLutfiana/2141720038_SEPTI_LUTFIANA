<?php

namespace App\Livewire;

use App\Models\IDP;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class BankIdpTable extends Component
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
            ->where('is_template', true) // Filter utama untuk Bank IDP
            ->when($this->search, function ($query) {
                $search = $this->search; // Pastikan variabel tersedia di dalam closure
                return $query->where(function ($q) use ($search) {
                    $q->where('proyeksi_karir', 'like', "%{$search}%")
                        ->orWhereHas('supervisor', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('mentor', function ($q3) use ($search) {
                            $q3->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('karyawan', function ($q4) use ($search) {
                            $q4->where('name', 'like', "%{$search}%");
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

        return view('livewire.bank-idp-table', [
            'idps' => $idps,
        ]);
    }
}
