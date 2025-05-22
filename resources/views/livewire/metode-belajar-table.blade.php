<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Nama Metode Belajar</th>
                <th class="text-center">Keterangan</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($metodebelajar as $item)
                <tr>
                    <td class="text-center" style="width: 50px;">{{ $loop->iteration + ($metodebelajar->currentPage() - 1) * $metodebelajar->perPage() }}</td>
                    <td class="text-center">{{ $item->nama_metodeBelajar }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td class="text-left" style="width: 120px;">
                        <a href="{{ route('adminsdm.data-master.data-idp.metode-belajar.edit', $item->id_metodeBelajar)}}" class="btn btn-warning btn-sm mb-1"> <i class="fas fa-edit"></i> Edit</a>
                        <br>
                        <a href="{{ route('adminsdm.data-master.data-idp.metode-belajar.show', $item->id_metodeBelajar)}}" class="btn btn-primary btn-sm mb-1"> <i class="fas fa-info-circle"></i> Detail</a>
                        <br>
                        <form action="{{ route('adminsdm.data-master.data-idp.metode-belajar.destroy', $item->id_metodeBelajar) }}" method="POST" style="display: inline;">
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
    {{ $metodebelajar->links() }}
</div>
