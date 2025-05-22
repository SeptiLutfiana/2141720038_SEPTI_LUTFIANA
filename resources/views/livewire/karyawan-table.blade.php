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
                @foreach ($user as $item)
                    <tr>
                        <td class="text-center" style="width: 50px;">
                            {{ $loop->iteration + ($user->currentPage() - 1) * $user->perPage() }}</td>
                        <td>{{ $item->npk }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->jenjang->nama_jenjang ?? '-' }}</td>
                        <td>{{ $item->jabatan->nama_jabatan ?? '-' }}</td>
                        <td>{{ $item->roles->pluck('nama_role')->join(', ') }}</td>
                        <td class="text-left" style="width: 120px;">
                            <a href="{{ route('adminsdm.data-master.karyawan.data-karyawan.edit', $item->id) }}"
                                class="btn btn-warning btn-sm mb-1"><i class="fas fa-edit"></i> Edit</a>
                            <br>
                            <a href="{{ route('adminsdm.data-master.karyawan.data-karyawan.show', $item->id) }}"
                                class="btn btn-primary btn-sm mb-1"><i class="fas fa-info-circle"></i> Detail</a>
                            <br>
                            <form action="{{ route('adminsdm.data-master.karyawan.data-karyawan.destroy', $item->id) }}"
                                method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm rounded mb-1">
                                    <i class="fas fa-trash"></i> Hapus
                            </form>
                            <br>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
        {{-- Pagination --}}
        {{ $user->links() }}
</div>

