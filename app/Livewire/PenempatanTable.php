<?php

namespace App\Livewire;

use App\Models\Penempatan;
use Livewire\WithPagination;
use Livewire\Component;

class PenempatanTable extends Component
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
        if ($penempatan = Penempatan::find($id)) {
            $penempatan->delete();
            session()->flash('msg-success', 'penempatan berhasil dihapus');
        } else {
            session()->flash('msg-error', 'penempatan tidak ditemukan');
        }
    }

    public function render()
{
    $penempatan = Penempatan::when($this->search, function ($query) {
                        return $query->where('nama_penempatan', 'like', "%{$this->search}%")
                                     ->orWhere('keterangan', 'like', "%{$this->search}%");
                    })
                    ->orderBy('nama_penempatan')
                    ->paginate(5)
                    ->withQueryString();

    return view('livewire.penempatan-table', [
        'penempatan' => $penempatan,
    ]);
    }
}
