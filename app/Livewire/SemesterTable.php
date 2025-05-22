<?php

namespace App\Livewire;

use App\Models\Semester;
use Livewire\WithPagination;
use Livewire\Component;

class SemesterTable extends Component
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
        if ($semester = Semester::find($id)) {
            $semester->delete();
            session()->flash('msg-success', 'Semester berhasil dihapus');
        } else {
            session()->flash('msg-error', 'Semester tidak ditemukan');
        }
    }

    public function render()
{
    $semester = Semester::when($this->search, function ($query) {
                        return $query->where('nama_semester', 'like', "%{$this->search}%")
                                     ->orWhere('keterangan', 'like', "%{$this->search}%");
                    })
                    ->orderBy('nama_semester')
                    ->paginate(5)
                    ->withQueryString();

    return view('livewire.semester-table', [
        'semester' => $semester,
    ]);
    }

}
