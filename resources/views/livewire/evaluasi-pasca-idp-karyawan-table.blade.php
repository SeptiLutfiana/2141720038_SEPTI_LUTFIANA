<div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Judul IDP</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($idps as $index => $idp)
                <tr>
                    <td>{{ $idps->firstItem() + $index }}</td>
                    <td>{{ $idp->proyeksi_karir }}</td>
                    <td>
                        <span class="badge badge-warning">Belum Dievaluasi</span>
                    </td>
                    <td>
                        <a href="{{ route('adminsdm.BankEvaluasi.EvaluasiPascaIdp.create', [
                            'id_idp' => $idp->id_idp,
                            'id_user' => auth()->id(),
                            'jenis' => 'pasca',
                        ]) }}"
                            class="btn btn-sm btn-primary">
                            Kerjakan Evaluasi
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">Tidak ada IDP yang perlu dievaluasi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $idps->links() }}
    </div>
</div>
