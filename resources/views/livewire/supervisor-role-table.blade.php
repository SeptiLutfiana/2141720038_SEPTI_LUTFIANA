<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Nama Supervisor</th>
                <th class="text-center">Direktorat</th>
                <th class="text-center">Penempatan</th>
                <th class="text-center">Role User</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($supervisors as $item)
                <tr>
                    <td class="text-center" style="width: 50px;">
                        {{ $loop->iteration + ($supervisors->currentPage() - 1) * $supervisors->perPage() }}</td>

                    {{-- Ambil nama user dari relasi User --}}
                    <td class="text-center">{{ $item->user->name ?? '-' }}</td>
                    <td class="text-center">{{ $item->user->learningGroup->nama_LG ?? '-' }}</td>
                    <td class="text-center">{{ $item->user->penempatan->nama_penempatan ?? '-' }}</td>
                    {{-- Ambil nama role dari relasi Role --}}
                    <td class="text-center">{{ $item->role->nama_role ?? '-' }}</td>

                    <td class="text-left" style="width: 100px;">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                Aksi
                            </button>
                            <div class="dropdown-menu dropdown-menu-right p-1" style="min-width: 130px; width: 130px;">
                                <a class="dropdown-item d-flex align-items-center py-1"
                                    href="{{ route('adminsdm.data-master.supervisor.show', $item->id_user) }}">
                                    <i class="fas fa-info-circle text-success mr-2" style="width: 18px;"></i> Detail
                                </a>
                                <a href="#" class="dropdown-item text-danger d-flex align-items-center py-1"
                                    onclick="event.preventDefault(); if(confirm('Yakin ingin menghapus data ini?')) document.getElementById('delete-supervisor-{{ $item->id_user }}').submit();">
                                    <i class="fas fa-trash-alt mr-2" style="width: 18px;"></i> Hapus
                                </a>
                                <form id="delete-supervisor-{{ $item->id_user }}"
                                    action="{{ route('adminsdm.data-master.supervisor.destroy', $item->id_user) }}"
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
    {{ $supervisors->links() }}
</div>
