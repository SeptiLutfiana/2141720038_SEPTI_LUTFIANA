<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Judul</th>
                <th class="text-center"> Tujuan Panduan</th>
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
                    <td class="text-center">
                        @foreach ($item->roles as $pr)
                            <span>{{ $pr->nama_role }}</span>
                        @endforeach
                    </td>
                    <td class="text-left" style="width: 100px;">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                Aksi
                            </button>
                            <div class="dropdown-menu dropdown-menu-right p-1" style="min-width: 130px; width: 130px;">
                                <a class="dropdown-item d-flex align-items-center py-1"
                                    href="{{ route('adminsdm.Panduan.edit', $item->id_panduan) }}">
                                    <i class="fas fa-edit text-warning mr-2" style="width: 18px;"></i> Edit
                                </a>
                                <a class="dropdown-item d-flex align-items-center py-1"
                                    href="{{ route('adminsdm.Panduan.show', $item->id_panduan) }}">
                                    <i class="fas fa-info-circle text-success mr-2" style="width: 18px;"></i> Detail
                                </a>
                                <a href="#" class="dropdown-item text-danger d-flex align-items-center py-1"
                                    onclick="event.preventDefault(); if(confirm('Yakin ingin menghapus data ini?')) document.getElementById('delete-panduan-{{ $item->id_panduan }}').submit();">
                                    <i class="fas fa-trash-alt mr-2" style="width: 18px;"></i> Hapus
                                </a>
                                <form id="delete-panduan-{{ $item->id_panduan }}"
                                    action="{{ route('adminsdm.Panduan.destroy', $item->id_panduan) }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
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
