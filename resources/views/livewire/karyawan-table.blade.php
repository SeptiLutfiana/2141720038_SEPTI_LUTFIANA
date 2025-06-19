<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">NPK</th>
                <th class="text-center">Nama Lengkap</th>
                <th class="text-center">Jenjang</th>
                <th class="text-center">Jabatan</th>
                <th class="text-center">User Role</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if ($user->count())
                @foreach ($user as $item)
                    <tr class="text-center">
                        <td class="text-center" style="width: 50px;">
                            {{ $loop->iteration + ($user->currentPage() - 1) * $user->perPage() }}</td>
                        <td>{{ $item->npk }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->jenjang->nama_jenjang ?? '-' }}</td>
                        <td>{{ $item->jabatan->nama_jabatan ?? '-' }}</td>
                        <td>{{ $item->roles->pluck('nama_role')->join(', ') }}</td>
                        <td class="text-left" style="width: 100px;">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Aksi
                                </button>
                                <div class="dropdown-menu dropdown-menu-right p-1"
                                    style="min-width: 130px; width: 130px;"> 
                                    <a
                                        class="dropdown-item d-flex align-items-center py-1"
                                        href="{{ route('adminsdm.data-master.karyawan.data-karyawan.edit', $item->id) }}">
                                        <i class="fas fa-edit text-warning mr-2" style="width: 18px;"></i> Edit
                                    </a>
                                    <a class="dropdown-item d-flex align-items-center py-1"
                                        href="{{ route('adminsdm.data-master.karyawan.data-karyawan.show', $item->id) }}">
                                        <i class="fas fa-info-circle text-success mr-2" style="width: 18px;"></i> Detail
                                    </a>
                                    <a href="#" class="dropdown-item text-danger d-flex align-items-center py-1"
                                        onclick="event.preventDefault(); if(confirm('Yakin ingin menghapus data ini?')) document.getElementById('delete-karyawan-{{ $item->id }}').submit();">
                                        <i class="fas fa-trash-alt mr-2" style="width: 18px;"></i> Hapus
                                    </a>
                                    <form id="delete-karyawan-{{ $item->id }}"
                                        action="{{ route('adminsdm.data-master.karyawan.data-karyawan.destroy', $item->id) }}"
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
                    <td colspan="6" class="text-center text-muted py-3">
                        Data Tidak Ditemukan
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
    {{-- Pagination --}}
    {{ $user->links() }}
</div>
