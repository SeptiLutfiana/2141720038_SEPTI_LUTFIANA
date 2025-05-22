<?php

namespace App\Livewire;
use Livewire\WithPagination;
use App\Models\Jabatan;
use Livewire\Component;

class JabatanTable extends Component
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
        if ($jabatan = Jabatan::find($id)) {
            $jabatan->delete();
            session()->flash('msg-success', 'Jabatan berhasil dihapus');
        } else {
            session()->flash('msg-error', 'Jabatan tidak ditemukan');
        }
    }

    public function render()
{
    $jabatan = Jabatan::when($this->search, function ($query) {
                        return $query->where('nama_jabatan', 'like', "%{$this->search}%")
                                     ->orWhere('keterangan', 'like', "%{$this->search}%");
                    })
                    ->orderBy('nama_jabatan')
                    ->paginate(5)
                    ->withQueryString();

    return view('livewire.jabatan-table', [
        'jabatan' => $jabatan,
    ]);
    }

}

