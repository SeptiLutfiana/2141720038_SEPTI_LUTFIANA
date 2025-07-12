<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Nama Mentor</th>
                <th class="text-center">Direktorat</th>
                <th class="text-center">Penempatan</th>
                <th class="text-center">Role User</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mentors as $item)
                <tr>
                    <td class="text-center" style="width: 50px;">
                        {{ $loop->iteration + ($mentors->currentPage() - 1) * $mentors->perPage() }}</td>
                    {{-- Ambil nama user dari relasi User --}}
                    <td class="text-center">{{ $item->user->name ?? '-' }}</td>
                    <td class="text-center">{{ $item->user->learningGroup->nama_LG ?? '-' }}</td>
                    <td class="text-center">{{ $item->user->penempatan->nama_penempatan ?? '-' }}</td>

                    {{-- Ambil nama role dari relasi Role --}}
                    <td class="text-center">{{ $item->role->nama_role ?? '-' }}</td>

                    <td class="text-left" style="width: 120px;">
                        <a href="{{ route('adminsdm.data-master.mentor.show', $item->id_user) }}"
                            class="btn btn-primary btn-sm mb-1">
                            <i class="fas fa-info-circle"></i> Detail
                        </a>
                        <br>
                        <form action="{{ route('adminsdm.data-master.mentor.destroy', $item->id_user) }}" method="POST"
                            style="display: inline;">
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
    {{ $mentors->links() }}
</div>
{{-- JS untuk checkbox all --}}

