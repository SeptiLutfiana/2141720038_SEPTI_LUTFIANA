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
    // public $semester;
    protected string $paginationTheme = 'bootstrap';
    protected $updatesQueryString = ['search'];

    public function mount()
    {
        // Mengambil search query dari URL
        $this->search = request()->query('search');
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
        $idps = IDP::when($this->search, function ($query) {
            return $query->where(function ($q) {
                $q->where('proyeksi_karir', 'like', "%{$this->search}%")
                    ->orWhere('npk', 'like', "%{$this->search}%");
            });
        })
            ->when($this->jenjang, function ($query) {
                return $query->where('id_jenjang', $this->jenjang);
            })
            ->when($this->lg, function ($query) {
                return $query->where('id_LG', $this->lg);
            })
            // ->when($this->semester, function ($query) {
            //     return $query->whereHas('semester', function ($q) {
            //         $q->where('semester', $this->semester);
            //     });
            // })
            ->orderBy('proyeksi_karir')
            ->paginate(5)
            ->withQueryString();

        return view('livewire.idp-table', [
            'idps' => $idps,
        ]);
    }
}
