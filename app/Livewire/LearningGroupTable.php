<?php

namespace App\Livewire;

use App\Models\LearingGroup;
use Livewire\WithPagination;
use Livewire\Component;

class LearningGroupTable extends Component
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
        if ($LG = LearingGroup::find($id)) {
            $LG->delete();
            session()->flash('msg-success', 'Learning Group berhasil dihapus');
        } else {
            session()->flash('msg-error', 'Learning Group tidak ditemukan');
        }
    }

    public function render()
{
    $LG = LearingGroup::when($this->search, function ($query) {
                        return $query->where('nama_LG', 'like', "%{$this->search}%")
                                     ->orWhere('keterangan', 'like', "%{$this->search}%");
                    })
                    ->orderBy('nama_LG')
                    ->paginate(5)
                    ->withQueryString();

    return view('livewire.learning-group-table', [
        'LG' => $LG,
    ]);
    }
}
