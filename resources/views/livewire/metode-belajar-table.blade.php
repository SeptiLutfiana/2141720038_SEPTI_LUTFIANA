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
            @foreach ($metodebelajar as $item)
                <tr>
                    <td class="text-center" style="width: 50px;">
                        {{ $loop->iteration + ($metodebelajar->currentPage() - 1) * $metodebelajar->perPage() }}</td>
                    <td class="text-center">{{ $item->nama_metodeBelajar }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td class="text-left" style="width: 100px;">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                Aksi
                            </button>
                            <div class="dropdown-menu dropdown-menu-right p-1" style="min-width: 130px; width: 130px;">
                                <a class="dropdown-item d-flex align-items-center py-1"
                                    href="{{ route('adminsdm.data-master.data-idp.metode-belajar.edit', $item->id_metodeBelajar) }}">
                                    <i class="fas fa-edit text-warning mr-2" style="width: 18px;"></i> Edit
                                </a>
                                <a class="dropdown-item d-flex align-items-center py-1"
                                    href="{{ route('adminsdm.data-master.data-idp.metode-belajar.show', $item->id_metodeBelajar) }}">
                                    <i class="fas fa-info-circle text-success mr-2" style="width: 18px;"></i> Detail
                                </a>
                                <a href="#" class="dropdown-item text-danger d-flex align-items-center py-1"
                                    onclick="event.preventDefault(); if(confirm('Yakin ingin menghapus data ini?')) document.getElementById('delete-metode-{{ $item->id_metodeBelajar }}').submit();">
                                    <i class="fas fa-trash-alt mr-2" style="width: 18px;"></i> Hapus
                                </a>
                                <form id="delete-metode-{{ $item->id_metodeBelajar }}"
                                    action="{{ route('adminsdm.data-master.data-idp.metode-belajar.destroy', $item->id_metodeBelajar) }}"
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
    {{ $metodebelajar->links() }}
</div>
