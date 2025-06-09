<?php

namespace App\Livewire;

use App\Models\Panduan;
use Livewire\Component;
use Livewire\WithPagination;

class PanduanIdpTable extends Component
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
        if ($semester = Panduan::find($id)) {
            $semester->delete();
            session()->flash('msg-success', 'Panduan IDP berhasil dihapus');
        } else {
            session()->flash('msg-error', 'Panduan IDP tidak ditemukan');
        }
    }

    public function render()
    {
        $panduan = Panduan::when($this->search, function ($query) {
            return $query->where('judul', 'like', "%{$this->search}%");
        })
            ->orderBy('judul')
            ->paginate(5)
            ->withQueryString();

        return view('livewire.panduan-idp-table', [
            'panduan' => $panduan,
        ]);
    }
}
