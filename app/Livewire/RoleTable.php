<?php

namespace App\Livewire;

use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;

class RoleTable extends Component
{
    use WithPagination;

    public $search = '';
    protected string $paginationTheme = 'bootstrap';
    protected $updatesQueryString = ['search'];

    public function mount()
    {
        // Mengambil search query dari URL
        $this->search = request()->query('search');
    }
    public function deleteId($id)
    {
        if ($role = Role::find($id)) {
            $role->delete();
            session()->flash('msg-success', 'Role berhasil dihapus');
        } else {
            session()->flash('msg-error', 'Role tidak ditemukan');
        }
    }

    public function render()
{
    $role = Role::when($this->search, function ($query) {
                        return $query->where('nama_role', 'like', "%{$this->search}%")
                                     ->orWhere('keterangan', 'like', "%{$this->search}%");
                    })
                    ->orderBy('nama_role')
                    ->paginate(5)
                    ->withQueryString();

    return view('livewire.role-table', [
        'role' => $role,
    ]);
    }
}
