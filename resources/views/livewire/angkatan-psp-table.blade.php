<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Bulan</th>
                <th class="text-center">Tahun</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($angkatanPsp as $item)
                <tr>
                    <td class="text-center" style="width: 50px;">{{ $loop->iteration + ($angkatanPsp->currentPage() - 1) * $angkatanPsp->perPage() }}</td>
                    <td class="text-center">{{ $item->bulan }}</td>
                    <td class="text-center">{{ $item->tahun }}</td>
                    <td class="text-left" style="width: 120px;">
                        <a href="{{ route('adminsdm.data-master.karyawan.angkatan-psp.edit', $item->id_angkatanpsp)}}" class="btn btn-warning btn-sm mb-1"><i class="fas fa-edit"></i> Edit</a>
                        <br>
                        <a href="{{ route('adminsdm.data-master.karyawan.angkatan-psp.show', $item->id_angkatanpsp)}}" class="btn btn-primary btn-sm mb-1"><i class="fas fa-info-circle"></i> Detail</a>
                        <br>
                        <form action="{{ route('adminsdm.data-master.karyawan.angkatan-psp.destroy', $item->id_angkatanpsp) }}" method="POST" style="display: inline;">
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
    {{ $angkatanPsp->links() }}
    </div>
    