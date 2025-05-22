<?php

namespace App\Livewire;

use App\Models\Jenjang;
use Livewire\WithPagination;
use Livewire\Component;

class JenjangTable extends Component
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
        if ($jenjang = Jenjang::find($id)) {
            $jenjang->delete();
            session()->flash('msg-success', 'Jenjang berhasil dihapus');
        } else {
            session()->flash('msg-error', 'Jenjang tidak ditemukan');
        }
    }

    public function render()
{
    $jenjang = Jenjang::when($this->search, function ($query) {
                        return $query->where('nama_jenjang', 'like', "%{$this->search}%")
                                     ->orWhere('keterangan', 'like', "%{$this->search}%");
                    })
                    ->orderBy('nama_jenjang')
                    ->paginate(5)
                    ->withQueryString();

    return view('livewire.jenjang-table', [
        'jenjang' => $jenjang,
    ]);
    }
}
