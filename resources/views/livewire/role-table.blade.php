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
            @foreach($role as $item)
                <tr>
                    <td class="text-center" style="width: 50px;">{{ $loop->iteration + ($role->currentPage() - 1) * $role->perPage() }}</td>
                    <td>{{ $item->nama_role }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td class="text-left" style="width: 120px;">
                        <a href="{{ route('adminsdm.data-master.karyawan.role.edit', $item->id_role)}}" class="btn btn-warning btn-sm mb-1"><i class="fas fa-edit"></i> Edit</a>
                        <br>
                        <a href="{{ route('adminsdm.data-master.karyawan.role.show', $item->id_role)}}" class="btn btn-primary btn-sm mb-1"><i class="fas fa-info-circle"></i> Detail</a>
                        <br>
                        <form action="{{ route('adminsdm.data-master.karyawan.role.destroy', $item->id_role) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm rounded mb-1">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                        <br>
                     </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    {{-- Pagination --}}
    {{ $role->links() }}
    </div>
    