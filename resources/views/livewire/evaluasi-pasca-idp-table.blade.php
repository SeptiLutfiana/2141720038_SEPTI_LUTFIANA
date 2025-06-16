<div>
    <table class="table table-striped">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>Nama Pengisi</th>
                <th>Judul IDP</th>
                <th>Tanggal Evaluasi</th>
                <th>Jenis Evaluasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($evaluasiPasca as $item)
                <tr class="text-center">
                    <td>{{ $loop->iteration + ($evaluasiPasca->currentPage() - 1) * $evaluasiPasca->perPage() }}</td>
                    <td>{{ $item->user->name ?? '-' }}</td>
                    <td>{{ $item->idps->proyeksi_karir ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_evaluasi)->format('d M Y') }}</td>
                    <td>{{ ucfirst($item->jenis_evaluasi) }}</td>
                    <td>
                        <a href="{{ route('adminsdm.BankEvaluasi.EvaluasiPascaIdp.showKaryawan', $item->id_evaluasi_idp) }}"
                            class="btn btn-primary btn-sm mb-1"><i class="fas fa-info-circle"></i> Detail</a>
                        <button wire:click="deleteId({{ $item->id_evaluasi_idp }})" class="btn btn-danger btn-sm mb-1"
                            onclick="return confirm('Yakin ingin menghapus data ini?')">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada data evaluasi pasca IDP.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $evaluasiPasca->links() }}
</div>
