<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Judul</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($panduan as $item)
                <tr>
                    <td class="text-center" style="width: 50px;">
                        {{ $loop->iteration + ($panduan->currentPage() - 1) * $panduan->perPage() }}
                    </td>
                    <td class="text-center">{{ $item->judul }}</td>
                    <td class="text-left" style="width: 150px;">
                        <a href="{{ route('adminsdm.Panduan.edit', $item->id_panduan) }}"
                            class="btn btn-warning btn-sm mb-1"><i class="fas fa-edit"></i></a>
                        <a href="{{ route('adminsdm.Panduan.show',  $item->id_panduan) }}"
                            class="btn btn-primary btn-sm mb-1"> <i class="fas fa-info-circle"></i></a>
                        <form action="{{ route('adminsdm.Panduan.destroy', $item->id_panduan) }}"
                            method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm rounded mb-1" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Belum ada data yang tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    {{ $panduan->links() }}
</div>
