<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Nama Learning Gorup</th>
                <th class="text-center">Keterangan</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($LG as $item)
                <tr>
                    <td class="text-center" style="width: 50px;">{{ $loop->iteration + ($LG->currentPage() - 1) * $LG->perPage() }}</td>
                    <td>{{ $item->nama_LG }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td class="text-left" style="width: 120px;">
                        <a href="{{ route('adminsdm.data-master.karyawan.learning-group.edit', $item->id_LG)}}" class="btn btn-warning btn-sm mb-1"><i class="fas fa-edit"></i> Edit</a>
                        <br>
                        <a href="{{ route('adminsdm.data-master.karyawan.learning-group.show', $item->id_LG)}}" class="btn btn-primary btn-sm mb-1"><i class="fas fa-info-circle"></i> Detail</a>
                        <br>
                        <form action="{{ route('adminsdm.data-master.karyawan.learning-group.destroy', $item->id_LG) }}" method="POST" style="display: inline;">
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
    {{ $LG->links() }}
    </div>
    