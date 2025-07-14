<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\IDP;
use App\Models\User;

class RiwayatIdpMentorTable extends Component
{
    use WithPagination;

    public $search;
    public $jenjang;
    public $lg;
    public $tahun;
    public $selected = [];
    public $selectAll = false;
    protected string $paginationTheme = 'bootstrap';

    protected $updatesQueryString = ['search', 'jenjang', 'lg', 'tahun'];

    public function mount()
    {
        $this->search = request()->query('search');
        $this->tahun = request()->query('tahun');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $allIdps = IDP::query()
                ->where('is_template', false)
                ->where('id_mentor', Auth::id())
                ->whereHas('rekomendasis', function ($q) {
                    $q->whereNotNull('hasil_rekomendasi')
                        ->where('hasil_rekomendasi', '!=', '');
                })
                ->when($this->search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->whereHas('karyawan', fn($q2) => $q2->where('name', 'like', "%$search%"))
                            ->orWhereHas('mentor', fn($q2) => $q2->where('name', 'like', "%$search%"))
                            ->orWhereHas('supervisor', fn($q2) => $q2->where('name', 'like', "%$search%"))
                            ->orWhereHas('learningGroup', fn($q2) => $q2->where('nama_LG', 'like', "%$search%"))
                            ->orWhereHas('rekomendasis', fn($q2) => $q2->where('hasil_rekomendasi', 'like', "%$search%"))
                            ->orWhere('proyeksi_karir', 'like', "%$search%");
                    });
                })
                ->when($this->jenjang, fn($q) => $q->where('id_jenjang', $this->jenjang))
                ->when($this->lg, fn($q) => $q->where('id_LG', $this->lg))
                ->when($this->tahun, fn($q) => $q->whereYear('waktu_mulai', $this->tahun))
                ->pluck('id_idp')
                ->map(fn($id) => (string)$id)
                ->toArray();

            $this->selected = $allIdps;
        } else {
            $this->selected = [];
        }
    }


    public function getCurrentPageIdps()
    {
        return IDP::query()
            ->where('is_template', false)
            ->where('id_mentor', Auth::id())
            ->whereHas('rekomendasis', function ($q) {
                $q->whereNotNull('hasil_rekomendasi')
                    ->where('hasil_rekomendasi', '!=', '');
            })
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('karyawan', fn($q2) => $q2->where('name', 'like', "%$search%"))
                        ->orWhereHas('mentor', fn($q2) => $q2->where('name', 'like', "%$search%"))
                        ->orWhereHas('supervisor', fn($q2) => $q2->where('name', 'like', "%$search%"))
                        ->orWhereHas('learningGroup', fn($q2) => $q2->where('nama_LG', 'like', "%$search%"))
                        ->orWhereHas('rekomendasis', fn($q2) => $q2->where('hasil_rekomendasi', 'like', "%$search%"))
                        ->orWhere('proyeksi_karir', 'like', "%$search%");
                });
            })
            ->when($this->jenjang, fn($q) => $q->where('id_jenjang', $this->jenjang))
            ->when($this->lg, fn($q) => $q->where('id_LG', $this->lg))
            ->when($this->tahun, fn($q) => $q->whereYear('waktu_mulai', $this->tahun))
            ->orderBy('waktu_mulai', 'desc')
            ->paginate(5);
    }

    public function render()
    {
        $idps = $this->getCurrentPageIdps();

        return view('livewire.riwayat-idp-mentor-table', [
            'idps' => $idps,
        ]);
    }
}
