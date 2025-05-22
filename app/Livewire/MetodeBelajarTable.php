<?php

namespace App\Livewire;

use App\Models\MetodeBelajar;
use Livewire\WithPagination;
use Livewire\Component;

class MetodeBelajarTable extends Component
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
        if ($metodebelajar = MetodeBelajar::find($id)) {
            $metodebelajar->delete();
            session()->flash('msg-success', 'Metode Belajar berhasil dihapus');
        } else {
            session()->flash('msg-error', 'Metode Belajar tidak ditemukan');
        }
    }

    public function render()
{
    $metodebelajar = MetodeBelajar::when($this->search, function ($query) {
                        return $query->where('nama_metodeBelajar', 'like', "%{$this->search}%")
                                     ->orWhere('keterangan', 'like', "%{$this->search}%");
                    })
                    ->orderBy('nama_metodeBelajar')
                    ->paginate(5)
                    ->withQueryString();

    return view('livewire.metode-belajar-table', [
        'metodebelajar' => $metodebelajar,
    ]);
    }

}
