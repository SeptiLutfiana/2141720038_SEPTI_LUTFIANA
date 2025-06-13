<?php

namespace App\Livewire;

use App\Models\BankEvaluasi;
use Livewire\Component;
use Livewire\WithPagination;

class BankEvaluasiTable extends Component
{
    use WithPagination;

    public $search = '';
    protected string $paginationTheme = 'bootstrap';
    protected $updatesQueryString = ['search'];
    public $jenisEvaluasi;
    public $tipePertanyaan;
    public $untukRole;

    public function mount($search = '', $jenisEvaluasi = null, $tipePertanyaan = null, $untukRole=null)
    {
        // Mengambil search query dari URL
        $this->search = request()->query('search');
        $this->jenisEvaluasi = $jenisEvaluasi;
        $this->tipePertanyaan =$tipePertanyaan;
        $this->untukRole = $untukRole;
    }
    public function deleteId($id)
    {
        if ($bankEvaluasi = BankEvaluasi::find($id)) {
            $bankEvaluasi->delete();
            session()->flash('msg-success', 'Pertanyaan berhasil dihapus');
        } else {
            session()->flash('msg-error', 'Pertanyaan tidak ditemukan');
        }
    }
    public function render()
    {
        $query = BankEvaluasi::query();

        if ($this->search) {
            $query->where('pertanyaan', 'like', '%' . $this->search . '%');
        }

        if ($this->jenisEvaluasi) {
            $query->where('jenis_evaluasi', $this->jenisEvaluasi);
        }
         if ($this->tipePertanyaan) {
            $query->where('tipe_pertanyaan', $this->tipePertanyaan);
        }
         if ($this->untukRole) {
            $query->where('untuk_role', $this->untukRole);
        }

        $bankEvaluasi = $query->latest()->paginate(5);

        return view('livewire.bank-evaluasi-table', compact('bankEvaluasi'));
    }
}
