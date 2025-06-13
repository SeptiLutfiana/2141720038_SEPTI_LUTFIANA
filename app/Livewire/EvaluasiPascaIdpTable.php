<?php

namespace App\Livewire;

use App\Models\EvaluasiIdp;
use Livewire\Component;
use Livewire\WithPagination;

class EvaluasiPascaIdpTable extends Component
{
    use WithPagination;

    public $search = '';
    protected string $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteId($id)
    {
        if ($evaluasi = EvaluasiIdp::find($id)) {
            $evaluasi->delete();
            session()->flash('msg-success', 'Evaluasi berhasil dihapus');
        } else {
            session()->flash('msg-error', 'Evaluasi tidak ditemukan');
        }
    }

    public function render()
    {
        $query = EvaluasiIdp::with('user')
            ->where('jenis_evaluasi', 'pasca');

        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        $evaluasiPasca = $query->latest()->paginate(5);

        return view('livewire.evaluasi-pasca-idp-table', compact('evaluasiPasca'));
    }
}
