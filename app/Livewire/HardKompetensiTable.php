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
    protected $updatesQueryString = ['search', 'jabatan', 'jenjang'];

    public function mount()
    {
        // Ambil search dari URL
        $this->search = request()->query('search', '');
    }

    public function render()
    {
        $kompetensi = Kompetensi::where('jenis_kompetensi', 'Hard Kompetensi')
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('karyawan', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    })->orWhereHas('supervisor', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    })->orWhereHas('learninggroup', function ($q2) use ($search) {
                        $q2->where('nama_LG', 'like', "%$search%");
                    })->orWhereHas('jenjang', function ($q2) use ($search) {
                        $q2->where('nama_jenjang', 'like', "%$search%");
                    })->orWhere('proyeksi_karir', 'like', "%$search%");
                });
            })
            ->when($this->jenjang, function ($query) {
                return $query->where('id_jenjang', $this->jenjang);
            })
            ->when($this->jabatan, function ($query) {
                return $query->where('id_jabatan', $this->jabatan);
            })
            ->orderBy('created_at')
            ->paginate(5)
            ->withQueryString();

        return view('livewire.hard-kompetensi-table', [
            'kompetensi' => $kompetensi,
        ]);
    }

    public function updatingSearch()
    {
        // Reset halaman pagination jika search berubah
        $this->resetPage();
    }
}
