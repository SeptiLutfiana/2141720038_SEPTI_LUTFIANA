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
    public $jenjang;
    public $jabatan;
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
        $kompetensi = Kompetensi::where('jenis_kompetensi', 'Soft Kompetensi') // filter soft saja
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

        return view('livewire.kompetensi-table', [
            'kompetensi' => $kompetensi,
        ]);
    }
}
