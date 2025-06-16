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
    public $tahun;
    protected $updatesQueryString = ['search','tahun'];

    public function mount()
    {
        $this->search = request()->query('search');
        $this->tahun = request()->query('tahun');
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
        $query = EvaluasiIdp::with('user', 'idps')
            ->where('jenis_evaluasi', 'pasca');

        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->tahun) {
            $query->whereYear('tanggal_evaluasi', $this->tahun);
        }
        $evaluasiPasca = $query->latest()->paginate(5);

        return view('livewire.evaluasi-pasca-idp-table', [
            'evaluasiPasca' => $evaluasiPasca,
        ]);
    }
}
