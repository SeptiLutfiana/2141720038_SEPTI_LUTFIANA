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
            ->doesntHave('rekomendasis') // Tidak punya data rekomendasi sama sekali
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('karyawan', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    })->orWhereHas('mentor', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    })->orWhereHas('supervisor', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    })->orWhereHas('learninggroup', function ($q2) use ($search) {
                        $q2->where('nama_LG', 'like', "%$search%");
                    })->orWhereHas('jenjang', function ($q2) use ($search) {
                        $q2->where('nama_jenjang', 'like', "%$search%");
                    })->orWhereHas('karyawan', function ($q2) use ($search) {
                        $q2->where('npk', 'like', "%$search%");
                    })->orWhereHas('mentor', function ($q2) use ($search) {
                        $q2->where('npk', 'like', "%$search%");
                    })->orWhereHas('supervisor', function ($q2) use ($search) {
                        $q2->where('npk', 'like', "%$search%");
                    })->orWhereHas('rekomendasis', function ($q2) use ($search) {
                        $q2->where('hasil_rekomendasi', 'like', "%$search%");
                    })->orWhere('proyeksi_karir', 'like', "%$search%");
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
            ->orderBy('waktu_mulai', 'desc')
            ->paginate(5)
            ->withQueryString();
        return view('livewire.mentor-idp', [
            'idps' => $idps,
        ]);
    }
}
