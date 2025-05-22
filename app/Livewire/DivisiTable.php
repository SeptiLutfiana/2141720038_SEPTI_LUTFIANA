<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Divisi;

class DivisiTable extends Component
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
        if ($divisi = Divisi::find($id)) {
            $divisi->delete();
            session()->flash('msg-success', 'Divisi berhasil dihapus');
        } else {
            session()->flash('msg-error', 'Divisi tidak ditemukan');
        }
    }

    public function render()
{
    $divisi = Divisi::when($this->search, function ($query) {
                        return $query->where('nama_divisi', 'like', "%{$this->search}%")
                                     ->orWhere('keterangan', 'like', "%{$this->search}%");
                    })
                    ->orderBy('nama_divisi')
                    ->paginate(5)
                    ->withQueryString();

    return view('livewire.divisi-table', [
        'divisi' => $divisi,
    ]);
    }

}
