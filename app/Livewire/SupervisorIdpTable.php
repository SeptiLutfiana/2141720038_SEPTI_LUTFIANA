<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\IDP;
use Illuminate\Support\Facades\DB;
class SupervisorIdpTable extends Component
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
        $supervisorId = Auth::id();

        // Subquery untuk hitung berapa kompetensi yang disetujui semua pengerjaannya
        $subquery = DB::table('idp_kompetensis')
            ->select('id_idp', DB::raw('COUNT(*) as total'))
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('idp_kompetensi_pengerjaans')
                    ->whereRaw('idp_kompetensi_pengerjaans.id_idpKom = idp_kompetensis.id_idpKom')
                    ->groupBy('idp_kompetensi_pengerjaans.id_idpKom')
                    ->havingRaw('COUNT(*) = SUM(CASE WHEN status_pengerjaan = "Disetujui Mentor" THEN 1 ELSE 0 END)');
            })
            ->groupBy('id_idp');

        // Gabungkan dengan query utama
        $idps = IDP::with(['mentor', 'supervisor', 'karyawan'])
            ->select('idps.*')
            ->joinSub($subquery, 'selesai', function ($join) {
                $join->on('idps.id_idp', '=', 'selesai.id_idp');
            })
            ->where('id_supervisor', $supervisorId)
            ->when(
                $this->search,
                fn($q) =>
                $q->where('proyeksi_karir', 'like', "%{$this->search}%")
                    ->orWhere('npk', 'like', "%{$this->search}%")
            )
            ->when($this->jenjang, fn($q) => $q->where('id_jenjang', $this->jenjang))
            ->when($this->lg, fn($q) => $q->where('id_LG', $this->lg))
            ->when($this->tahun, fn($q) => $q->whereYear('waktu_mulai', $this->tahun))
            ->orderBy('proyeksi_karir')
            ->paginate(10)
            ->withQueryString();

        return view('livewire.supervisor-idp-table', [
            'idps' => $idps,
        ]);
    }
}
