<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Proyeksi Karir</th>
                <th class="text-center">Nama Karyawan</th>
                <th class="text-center">Nama Mentor</th>
                <th class="text-center">Nama Supervisor</th>
                <th class="text-center">Learning Group</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($idps as $idp)
                <tr>
                    <td class="text-center" style="width: 50px;">
                        {{ $loop->iteration + ($idps->currentPage() - 1) * $idps->perPage() }}
                    </td>
                    <td>{{ $idp->proyeksi_karir }}</td>
                    <td>{{ $idp->karyawan->name }}</td>
                    <td>{{ $idp->mentor->name }}</td>
                    <td>{{ $idp->supervisor->name }}</td>
                    <td>{{ $idp->karyawan->learningGroup->nama_LG }}</td>
                    <td class="text-left" style="width: 120px;">
                        <a href="#" class="btn btn-warning btn-sm mb-1">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <br>
                        <a href="{{ route('adminsdm.BehaviorIDP.show', $idp->id_idp)}}" class="btn btn-primary btn-sm mb-1">
                            <i class="fas fa-info-circle"></i> Detail
                        </a>
                        <br>
                        <form action="#" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm rounded mb-1">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination --}}
    {{ $idps->links() }}
</div>
