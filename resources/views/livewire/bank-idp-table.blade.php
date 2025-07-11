<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Proyeksi Karir</th>
                <th class="text-center">Sasaran Jenjang</th>
                <th class="text-center">Direktorat</th>
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
                        <td class="text-center" style="width: 120px;">{{ $idp->current_applies }}</td>

                        {{-- <td>{{ $idp->lg->nama_LG}}</td> --}}
                        <td class="text-left" style="width: 120px;">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Aksi
                                </button>
                                <div class="dropdown-menu dropdown-menu-right p-1"
                                    style="min-width: 130px; width: 130px;">
                                    <a class="dropdown-item"
                                        href="{{ route('adminsdm.BehaviorIDP.ListIDP.editBank', $idp->id_idp) }}">
                                        <i class="fas fa-edit text-warning"></i> Edit
                                    </a>
                                    <a class="dropdown-item"
                                        href="{{ route('adminsdm.BehaviorIDP.ListIDP.showBank', $idp->id_idp) }}">
                                        <i class="fas fa-info-circle text-primary"></i> Detail
                                    </a>
                                    <a href="#" class="dropdown-item text-danger d-flex align-items-center py-1"
                                        onclick="event.preventDefault(); if(confirm('Yakin ingin menghapus data ini?')) document.getElementById('form-delete-{{ $idp->id_idp }}').submit();">
                                        <i class="fas fa-trash-alt mr-1" style="width: 18px; line-height: 1;"></i>
                                        <span class="d-inline-block">Hapus</span>
                                    </a>
                                    <form id="form-delete-{{ $idp->id_idp }}"
                                        action="{{ route('adminsdm.BehaviorIDP.ListIDP.destroyBank', $idp->id_idp) }}"
                                        method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8" class="text-center text-muted py-3">
                        Data Tidak Ditemukan
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- Pagination --}}
    {{ $idps->links() }}
</div>
