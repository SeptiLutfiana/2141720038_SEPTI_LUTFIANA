<?php

namespace App\Livewire;

use App\Models\Kompetensi;
use Livewire\WithPagination;
use Livewire\Component;

class KompetensiTable extends Component
{
    use WithPagination;

    public $search;
    public $jenis;
    protected string $paginationTheme = 'bootstrap';
    protected $updatesQueryString = ['search'];

    public function mount()
    {
        // Mengambil search query dari URL
        $this->search = request()->query('search');
    }
    public function deleteId($id)
    {
        if ($kompetensi = Kompetensi::find($id)) {
            $kompetensi->delete();
            session()->flash('msg-success', 'Kompetensi berhasil dihapus');
        } else {
            session()->flash('msg-error', 'Kompetensi tidak ditemukan');
        }
    }

    public function render()
{
    $kompetensi = Kompetensi::when($this->search, function ($query) {
                        return $query->where('nama_kompetensi', 'like', "%{$this->search}%")
                                     ->orWhere('jenis_kompetensi', 'like', "%{$this->search}%")
                                     ->orWhere('keterangan', 'like', "%{$this->search}%");
                    })
                    ->when($this->jenis, function ($query) {
                        return $query->where('jenis_kompetensi', $this->jenis);
                    })
                    ->orderBy('nama_kompetensi')
                    ->paginate(5)
                    ->withQueryString();

    return view('livewire.kompetensi-table', [
        'kompetensi' => $kompetensi,
    ]);
    }
}
