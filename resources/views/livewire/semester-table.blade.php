<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Nama Semester</th>
                <th class="text-center">Keterangan</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($semester as $item)
                <tr>
                    <td class="text-center" style="width: 50px;">{{ $loop->iteration + ($semester->currentPage() - 1) * $semester->perPage() }}</td>
                    <td class="text-center">{{ $item->nama_semester }}</td>
                    <td class="text-center">{{ $item->keterangan }}</td>
                    <td class="text-left" style="width: 120px;">
                        <a href="{{ route('adminsdm.data-master.data-idp.semester.edit', $item->id_semester)}}" class="btn btn-warning btn-sm mb-1"><i class="fas fa-edit"></i> Edit</a>
                        <br>
                        <a href="{{ route('adminsdm.data-master.data-idp.semester.show', $item->id_semester)}}" class="btn btn-primary btn-sm mb-1"> <i class="fas fa-info-circle"></i> Detail</a>
                        <br>
                        <form action="{{ route('adminsdm.data-master.data-idp.semester.destroy', $item->id_semester) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm rounded mb-1" title="Hapus">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                     </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination --}}
    {{ $semester->links() }}
</div>
