<?php

namespace App\Livewire;

use App\Models\UserRole;
use Livewire\Component;
use Livewire\WithPagination;

class UserRoleTable extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function render()
    {
        // Ambil hanya user role dengan role 'mentor'
        $role = \App\Models\Role::where('nama_role', 'mentor')->first();
        $mentorRoleId = $role ? $role->id_role : null; // Ambil id_role jika role ditemukan, jika tidak null
        $mentors = UserRole::with(['user', 'role'])
            ->where('id_role', $mentorRoleId)
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('nama_role', 'like', '%' . $this->search . '%')
                      ->orWhere('npk', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(10);

        return view('livewire.user-role-table', [
            'mentors' => $mentors
        ]);
    }
}
