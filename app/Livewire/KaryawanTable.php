<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\WithPagination;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class KaryawanTable extends Component
{
    use WithPagination;
    public $search;
    public $jenjang;
    public $lg;
    public $role;
    public $semester;
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
        $user = User::when($this->search, function ($query) {
            return $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('npk', 'like', "%{$this->search}%");
            });
        })
            ->when($this->jenjang, function ($query) {
                return $query->where('id_jenjang', $this->jenjang);
            })
            ->when($this->lg, function ($query) {
                return $query->where('id_LG', $this->lg);
            })
            ->when($this->role, function ($query) {
                return $query->whereHas('roles', function ($q) {
                    $q->where('roles.id_role', $this->role);
                });
            })
            ->when($this->semester, function ($query) {
                return $query->whereHas('semester', function ($q) {
                    $q->where('id_semester', $this->role);
                });
            })
            ->orderBy('name')
            ->paginate(5)
            ->withQueryString();

        return view('livewire.karyawan-table', [
            'user' => $user,
        ]);
    }
}
