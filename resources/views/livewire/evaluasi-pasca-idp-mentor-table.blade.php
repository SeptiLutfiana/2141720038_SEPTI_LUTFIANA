<div>

    <table class="table">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>Nama Karyawan</th>
                <th>Judul IDP</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($idps as $index => $idp)
                <tr class="text-center">
                    <td>{{ $idps->firstItem() + $index }}</td>
                    <td>{{ $idp->user->name }}</td>
                    <td>{{ $idp->proyeksi_karir }}</td>
                    <td>
                        <span class="badge badge-warning">Belum Dievaluasi</span>
                    </td>
                    <td>
                        <a href="{{ route('mentor.EvaluasiIdp.EvaluasiPascaIdp.createMentor', [
                            'id_idp' => $idp->id_idp,
                            'id_user' => Auth::id(),
                            'jenis' => 'pasca',
                        ]) }}"
                            class="btn btn-primary btn-sm">
                            <i class="fas fa-pen mr-1"></i> Kerjakan
                        </a>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $idps->links() }}

</div>
