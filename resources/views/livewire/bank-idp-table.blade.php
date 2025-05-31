<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Proyeksi Karir</th>
                <th class="text-center">Sasaran Jenjang</th>
                <th class="text-center">Learning Group</th>
                <th class="text-center">Nama Supervisor</th>
                <th class="text-center">Kuota IDP</th>
                <th class="text-center">Jumlah Karyawan</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if ($idps->count())
                @foreach ($idps as $idp)
                    <tr>
                        <td class="text-center" style="width: 50px;">
                            {{ $loop->iteration + ($idps->currentPage() - 1) * $idps->perPage() }}
                        </td>
                        <td class="text-center">{{ $idp->proyeksi_karir }}</td>
                        <td class="text-center">{{ $idp->jenjang->nama_jenjang }}</td>
                        <td class="text-center">{{ $idp->learningGroup->nama_LG }}</td>
                        <td class="text-center">{{ $idp->supervisor->name }}</td>
                        <td class="text-center" style="width: 120px;">{{ $idp->max_applies }}</td>
                        <td class="text-center" style="width: 200px;">{{ $idp->current_applies }}</td>

                        {{-- <td>{{ $idp->lg->nama_LG}}</td> --}}
                        <td class="text-left" style="width: 120px;">
                            <a href="{{ route('adminsdm.BehaviorIDP.ListIDP.editBank', $idp->id_idp) }}"
                                class="btn btn-warning btn-sm mb-1">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <br>
                            <a href="{{ route('adminsdm.BehaviorIDP.ListIDP.showBank', $idp->id_idp) }}"
                                class="btn btn-primary btn-sm mb-1">
                                <i class="fas fa-info-circle"></i> Detail
                            </a>
                            <br>
                            <form action="{{ route('adminsdm.BehaviorIDP.ListIDP.destroyBank', $idp->id_idp) }}"
                                method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm rounded mb-1">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">
                        Data Tidak Ditemukan
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- Pagination --}}
    {{ $idps->links() }}
</div>
