<div>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Nama Kompetensi</th>
                    <th class="text-center">Keterangan</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kompetensi as $item)
                    <tr>
                        <td class="text-center" style="width: 50px;">
                            {{ $loop->iteration + ($kompetensi->currentPage() - 1) * $kompetensi->perPage() }}</td>
                        <td>{{ $item->nama_kompetensi }}</td>
                        <td>{{ $item->keterangan }}</td>
                        <td class="text-left" style="width: 100px;">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Aksi
                                </button>
                                <div class="dropdown-menu dropdown-menu-right p-1"
                                    style="min-width: 130px; width: 130px;">
                                    <a class="dropdown-item d-flex align-items-center py-1"
                                        href="{{ route('adminsdm.data-master.kompetensi.edit', ['id' => $item->id_kompetensi, 'from' => 'soft']) }}">
                                        <i class="fas fa-edit text-warning mr-2" style="width: 18px;"></i> Edit
                                    </a>
                                    <a class="dropdown-item d-flex align-items-center py-1"
                                        href="{{ route('adminsdm.data-master.kompetensi.showSoft', $item->id_kompetensi) }}">
                                        <i class="fas fa-info-circle text-success mr-2" style="width: 18px;"></i> Detail
                                    </a>
                                    <a href="#" class="dropdown-item text-danger d-flex align-items-center py-1"
                                        onclick="event.preventDefault(); if(confirm('Yakin ingin menghapus data ini?')) document.getElementById('delete-kompetensi-{{ $item->id_kompetensi }}').submit();">
                                        <i class="fas fa-trash-alt mr-2" style="width: 18px;"></i> Hapus
                                    </a>
                                    <form id="delete-kompetensi-{{ $item->id_kompetensi }}"
                                        action="{{ route('adminsdm.data-master.kompetensi.destroy', $item->id_kompetensi) }}"
                                        method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        {{ $kompetensi->links() }}
    </div>
</div>
