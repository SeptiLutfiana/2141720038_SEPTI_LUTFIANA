<?php

namespace App\Livewire;

use App\Models\BankEvaluasi;
use Livewire\Component;
use Livewire\WithPagination;

class BankEvaluasiTable extends Component
{
    use WithPagination;

    public $search = '';
    public $jenisEvaluasi = '';
    public $tipePertanyaan = '';
    public $untukRole = '';

    protected string $paginationTheme = 'bootstrap';

    // Query string binding agar tetap saat reload page
    protected $updatesQueryString = ['search', 'jenisEvaluasi', 'tipePertanyaan', 'untukRole'];

    public function mount($search = '', $jenisEvaluasi = '', $tipePertanyaan = '', $untukRole = '')
    {
        $this->search = request()->query('search', $search);
        $this->jenisEvaluasi = request()->query('jenisEvaluasi', $jenisEvaluasi);
        $this->tipePertanyaan = request()->query('tipePertanyaan', $tipePertanyaan);
        $this->untukRole = request()->query('untukRole', $untukRole);
    }

    // Reset pagination ke halaman 1 saat filter berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingJenisEvaluasi()
    {
        $this->resetPage();
    }
    public function updatingTipePertanyaan()
    {
        $this->resetPage();
    }
    public function updatingUntukRole()
    {
        $this->resetPage();
    }

    public function deleteId($id)
    {
        $item = BankEvaluasi::find($id);

        if ($item) {
            $item->delete();
            session()->flash('msg-success', 'Pertanyaan berhasil dihapus');
        } else {
            session()->flash('msg-error', 'Pertanyaan tidak ditemukan');
        }
    }

    public function render()
    {
        $data = BankEvaluasi::query()
            ->when($this->search, fn($q) => $q->where('pertanyaan', 'like', "%{$this->search}%"))
            ->when($this->jenisEvaluasi, fn($q) => $q->where('jenis_evaluasi', $this->jenisEvaluasi))
            ->when($this->tipePertanyaan, fn($q) => $q->where('tipe_pertanyaan', $this->tipePertanyaan))
            ->when($this->untukRole, fn($q) => $q->where('untuk_role', $this->untukRole))
            ->latest()
            ->paginate(5);

        return view('livewire.bank-evaluasi-table', [
            'bankEvaluasi' => $data,
        ]);
    }
}
