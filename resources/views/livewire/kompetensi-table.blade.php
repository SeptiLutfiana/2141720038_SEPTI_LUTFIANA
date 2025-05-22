<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Nama Kompetensi</th>
                <th class="text-center">Jenis Kompetensi</th>
                <th class="text-center">Keterangan</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kompetensi as $item)
                <tr>
                    <td class="text-center" style="width: 50px;">{{ $loop->iteration + ($kompetensi->currentPage() - 1) * $kompetensi->perPage() }}</td>
                    <td>{{ $item->nama_kompetensi }}</td>
                    <td>{{ $item->jenis_kompetensi }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td class="text-left" style="width: 120px;">
                        <a href="{{ route('adminsdm.data-master.kompetensi.edit', $item->id_kompetensi)}}" class="btn btn-warning btn-sm mb-1"><i class="fas fa-edit"></i> Edit</a>
                        <br>
                        <a href="{{ route('adminsdm.data-master.kompetensi.show', $item->id_kompetensi)}}" class="btn btn-primary btn-sm mb-1"><i class="fas fa-info-circle"></i> Detail</a>
                        <br>
                        <form action="{{ route('adminsdm.data-master.kompetensi.destroy', $item->id_kompetensi) }}" method="POST" style="display: inline;">
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
    {{ $kompetensi->links() }}
</div>
