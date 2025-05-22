<?php

namespace App\Livewire;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\UserRole;

class SupervisorRoleTable extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $role = \App\Models\Role::where('nama_role', 'supervisor')->first();
        $supervisorRoleId = $role ? $role->id_role : null; // 
        $supervisors = UserRole::with(['user', 'role'])
            ->where('id_role', $supervisorRoleId)
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('npk', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(10);

        return view('livewire.supervisor-role-table', [
            'supervisors' => $supervisors
        ]);
    }
}
