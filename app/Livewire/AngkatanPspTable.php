<?php

namespace App\Livewire;

use App\Models\AngkatanPSP;
use Livewire\WithPagination;
use Livewire\Component;

class AngkatanPspTable extends Component
{
    use WithPagination;

    public $search = '';  // Tahun yang dicari
    public $years;  // Menyimpan tahun untuk dropdown
    protected string $paginationTheme = 'bootstrap';
    protected $updatesQueryString = ['search'];

    public function mount()
    {
        // Menyiapkan daftar tahun yang tersedia
        $this->years = AngkatanPSP::distinct()->pluck('tahun');
        
        // Mengambil query search dari URL
        $this->search = request()->query('search');
    }

    public function deleteId($id)
    {
        if ($angkatanpsp = AngkatanPSP::find($id)) {
            $angkatanpsp->delete();
            session()->flash('msg-success', 'berhasil dihapus');
        } else {
            session()->flash('msg-error', ' Data tidak ditemukan');
        }
    }

    public function render()
    {
        $angkatanpsp = AngkatanPSP::when($this->search, function ($query) {
                        return $query->where('tahun', 'like', "%{$this->search}%");
                    })
                    ->orderBy('tahun')
                    ->paginate(5)
                    ->withQueryString();

        return view('livewire.angkatan-psp-table', [
            'angkatanPsp' => $angkatanpsp,
        ]);
    }
}
