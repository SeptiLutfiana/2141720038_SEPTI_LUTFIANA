<?php

namespace App\Livewire;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\IDP;
use Livewire\Component;

class MentorIdp extends Component
{
    use WithPagination;
    public $search;
    public $jenjang;
    public $lg;
    public $tahun;
    protected string $paginationTheme = 'bootstrap';
    protected $updatesQueryString = ['search', 'jenjang', 'lg', 'tahun'];

    public function mount()
    {
        // Mengambil search query dari URL
        $this->search = request()->query('search');
        $this->tahun = request()->query('tahun');
    }
    public function deleteId($id)
    {
        if ($user = User::find($id)) {
            $user->delete();
            session()->flash('msg-success', 'berhasil dihapus');
        } else {
            session()->flash('msg-error', 'tidak ditemukan');
        }
    }

    public function render()
    {

        $mentorId = Auth::id();
        $idps = IDP::with(['mentor', 'supervisor', 'karyawan'])
            ->where('id_mentor', $mentorId) // Filter utama untuk Bank IDP
            ->when($this->search, function ($query) {
                return $query->where(function ($q) {
                    $q->where('proyeksi_karir', 'like', "%{$this->search}%")
                        ->orWhere('npk', 'like', "%{$this->search}%");
                });
            })
            ->when($this->jenjang, function ($query) {
                return $query->where('id_jenjang', $this->jenjang);
            })
            ->when($this->lg, function ($query) {
                return $query->where('id_LG', $this->lg);
            })
            ->when($this->tahun, function ($query) {
                return $query->whereYear('waktu_mulai', $this->tahun);
            })
            ->orderBy('proyeksi_karir')
            ->paginate(5)
            ->withQueryString();
        return view('livewire.mentor-idp', [
            'idps' => $idps,
        ]);
    }
}
