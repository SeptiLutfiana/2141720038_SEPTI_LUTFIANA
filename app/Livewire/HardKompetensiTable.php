<?php

namespace App\Livewire;

use App\Models\Kompetensi;
use Livewire\WithPagination;
use Livewire\Component;

class HardKompetensiTable extends Component
{
    use WithPagination;

    public $search;
    public $jenis;
    public $jenjang;
    public $jabatan;
    protected string $paginationTheme = 'bootstrap';
    protected $updatesQueryString = ['search'];
    
    public function mount()
    {
        // Ambil search dari URL
        $this->search = request()->query('search', '');
    }

    public function render()
    {
        $kompetensi = Kompetensi::where('jenis_kompetensi', 'Hard Kompetensi')
            ->when($this->search, function ($query) {
                return $query->where('nama_kompetensi', 'like', "%{$this->search}%")
                    ->orWhere('keterangan', 'like', "%{$this->search}%");
            })
            ->when($this->jenjang, function ($query) {
                return $query->where('id_jenjang', $this->jenjang);
            })
            ->when($this->jabatan, function ($query) {
                return $query->where('id_jabatan', $this->jabatan);
            })
            ->orderBy('nama_kompetensi')
            ->paginate(5)
            ->withQueryString();

        return view('livewire.hard-kompetensi-table', [
            'kompetensi' => $kompetensi, // <-- WAJIB dikirim ke view!
        ]);
    }

    public function updatingSearch()
    {
        // Reset halaman pagination jika search berubah
        $this->resetPage();
    }
}
