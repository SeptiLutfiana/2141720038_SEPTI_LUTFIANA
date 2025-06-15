<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\IDP;
use Livewire\Attributes\Url;

class EvaluasiOnboardingKaryawanTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'tahun', keep: true)] // simpan parameter di URL & keep state saat reload
    public $tahun = '';

    public $daftarTahun = [];

    public function mount()
    {
        $this->daftarTahun = IDP::where('id_user', Auth::id())
            ->selectRaw('YEAR(waktu_mulai) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->toArray();
    }

    public function updatedTahun()
    {
        $this->resetPage(); // reset ke halaman 1 saat tahun diubah
    }

    public function render()
    {
        $query = IDP::where('id_user', Auth::id())
            ->whereHas('evaluasiIdp', fn($q) => $q->where('jenis_evaluasi', 'onboarding'))
            ->with(['mentor', 'evaluasiIdp']);

        if (!empty($this->tahun)) {
            $query->whereYear('waktu_mulai', $this->tahun);
        }

        return view('livewire.evaluasi-onboarding-karyawan-table', [
            'idps' => $query->paginate(5),
            'daftarTahun' => $this->daftarTahun,
        ]);
    }
}
