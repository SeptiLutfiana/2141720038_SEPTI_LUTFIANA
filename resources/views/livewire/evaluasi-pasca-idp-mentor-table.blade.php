<div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Karyawan</th>
                <th>Judul IDP</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($idps as $index => $idp)
                <tr>
                    <td>{{ $idps->firstItem() + $index }}</td>
                    <td>{{ $idp->user->name }}</td>
                    <td>{{ $idp->proyeksi_karir }}</td>
                    <td>
                        <a href="{{ route('mentor.EvaluasiIdp.EvaluasiPascaIdp.createMentor', [
                            'id_idp' => $idp->id_idp,
                            'id_user' => Auth::id(),
                            'jenis' => 'pasca',
                        ]) }}"
                            class="btn btn-primary btn-sm">
                            Evaluasi
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $idps->links() }}

</div>
