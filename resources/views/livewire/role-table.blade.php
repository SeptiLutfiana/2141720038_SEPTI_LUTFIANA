<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Nama Role User</th>
                <th class="text-center">Keterangan</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($role as $item)
                <tr>
                    <td class="text-center" style="width: 50px;">
                        {{ $loop->iteration + ($role->currentPage() - 1) * $role->perPage() }}</td>
                    <td>{{ $item->nama_role }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td class="text-left" style="width: 100px;">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                Aksi
                            </button>
                            <div class="dropdown-menu dropdown-menu-right p-1" style="min-width: 130px; width: 130px;">
                                <a class="dropdown-item d-flex align-items-center py-1"
                                    href="{{ route('adminsdm.data-master.karyawan.role.edit', $item->id_role) }}">
                                    <i class="fas fa-edit text-warning mr-2" style="width: 18px;"></i> Edit
                                </a>
                                <a class="dropdown-item d-flex align-items-center py-1"
                                    href="{{ route('adminsdm.data-master.karyawan.role.show', $item->id_role) }}">
                                    <i class="fas fa-info-circle text-success mr-2" style="width: 18px;"></i> Detail
                                </a>
                                {{-- <a href="#" class="dropdown-item text-danger d-flex align-items-center py-1"
                                    onclick="event.preventDefault(); if(confirm('Yakin ingin menghapus data ini?')) document.getElementById('delete-role-{{ $item->id_role }}').submit();">
                                    <i class="fas fa-trash-alt mr-2" style="width: 18px;"></i> Hapus
                                </a>
                                <form id="delete-role-{{ $item->id_role }}"
                                    action="{{ route('adminsdm.data-master.karyawan.role.destroy', $item->id_role) }}"
                                    method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form> --}}
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination --}}
    {{ $role->links() }}
</div>
