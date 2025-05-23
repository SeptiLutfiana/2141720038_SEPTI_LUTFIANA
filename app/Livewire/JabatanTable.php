<?php

namespace App\Livewire;

use Livewire\WithPagination;
use App\Models\Jabatan;
use Livewire\Component;

class JabatanTable extends Component
{
    use WithPagination;

    public $search = '';
    public $jenjang;
    protected string $paginationTheme = 'bootstrap';
    protected $updatesQueryString = ['search'];

    public function mount()
    {
        // Mengambil search query dari URL
        $this->search = request()->query('search');
    }
    public function deleteId($id)
    {
        if ($jabatan = Jabatan::find($id)) {
            $jabatan->delete();
            session()->flash('msg-success', 'Jabatan berhasil dihapus');
        } else {
            session()->flash('msg-error', 'Jabatan tidak ditemukan');
        }
    }

    public function render()
    {
        $jabatan = Jabatan::with('jenjang') // eager loading supaya tidak N+1
            ->when($this->search, function ($query) {
                return $query->where(function ($q) {
                    $q->where('nama_jabatan', 'like', "%{$this->search}%")
                        ->orWhere('keterangan', 'like', "%{$this->search}%")
                        ->orWhereHas('jenjang', function ($q) {
                            $q->where('nama_jenjang', 'like', "%{$this->search}%");
                        });
                });
            })
             ->when($this->jenjang, function ($query) {
                return $query->where('id_jenjang', $this->jenjang);
            })
            ->orderBy('nama_jabatan')
            ->paginate(5)
            ->withQueryString();

        return view('livewire.jabatan-table', [
            'jabatan' => $jabatan,
            
        ]);
    }
}
