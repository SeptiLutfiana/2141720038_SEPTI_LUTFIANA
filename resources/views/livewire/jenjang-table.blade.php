<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Nama Jenjang</th>
                <th class="text-center">Keterangan</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jenjang as $item)
                <tr>
                    <td class="text-center" style="width: 50px;">{{ $loop->iteration + ($jenjang->currentPage() - 1) * $jenjang->perPage() }}</td>
                    <td>{{ $item->nama_jenjang }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td class="text-left" style="width: 120px;">
                        <a href="{{ route('adminsdm.data-master.karyawan.jenjang.edit', $item->id_jenjang)}}" class="btn btn-warning btn-sm mb-1"><i class="fas fa-edit"></i> Edit</a>
                        <br>
                        <a href="{{ route('adminsdm.data-master.karyawan.jenjang.show', $item->id_jenjang)}}" class="btn btn-primary btn-sm mb-1"><i class="fas fa-info-circle"></i> Detail</a>
                        <br>
                        <form action="{{ route('adminsdm.data-master.karyawan.jenjang.destroy', $item->id_jenjang) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm rounded">
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
    {{ $jenjang->links() }}
    </div>
    