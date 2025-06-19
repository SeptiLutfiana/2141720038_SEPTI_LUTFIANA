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
            @foreach ($semester as $item)
                <tr>
                    <td class="text-center" style="width: 50px;">
                        {{ $loop->iteration + ($semester->currentPage() - 1) * $semester->perPage() }}</td>
                    <td class="text-center">{{ $item->nama_semester }}</td>
                    <td class="text-center">{{ $item->keterangan }}</td>
                    <td class="text-left" style="width: 100px;">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                Aksi
                            </button>
                            <div class="dropdown-menu dropdown-menu-right p-1" style="min-width: 130px; width: 130px;">
                                <a class="dropdown-item d-flex align-items-center py-1"
                                    href="{{ route('adminsdm.data-master.data-idp.semester.edit', $item->id_semester) }}">
                                    <i class="fas fa-edit text-warning mr-2" style="width: 18px;"></i> Edit
                                </a>
                                <a class="dropdown-item d-flex align-items-center py-1"
                                    href="{{ route('adminsdm.data-master.data-idp.semester.show', $item->id_semester) }}">
                                    <i class="fas fa-info-circle text-success mr-2" style="width: 18px;"></i> Detail
                                </a>
                                <a href="#" class="dropdown-item text-danger d-flex align-items-center py-1"
                                    onclick="event.preventDefault(); if(confirm('Yakin ingin menghapus data ini?')) document.getElementById('delete-semester-{{ $item->id_semester }}').submit();">
                                    <i class="fas fa-trash-alt mr-2" style="width: 18px;"></i> Hapus
                                </a>
                                <form id="delete-semester-{{ $item->id_semester }}"
                                    action="{{ route('adminsdm.data-master.data-idp.semester.destroy', $item->id_semester) }}"
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
    {{ $semester->links() }}
</div>
