<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Pertanyaan</th>
                <th class="text-center">Tipe Pertanyaan</th>
                <th class="text-center">Jenis Evaluasi</th>
                <th class="text-center">Untuk Role</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if ($bankEvaluasi->count() > 0)
                @foreach ($bankEvaluasi as $item)
                    <tr>
                        <td class="text-center" style="width: 50px;">
                            {{ $loop->iteration + ($bankEvaluasi->currentPage() - 1) * $bankEvaluasi->perPage() }}</td>
                        <td>{{ $item->pertanyaan }}</td>
                        <td class="text-center">{{ $item->tipe_pertanyaan }}</td>
                        <td class="text-center">{{ $item->jenis_evaluasi }}</td>
                        <td class="text-center">{{ $item->untuk_role }}</td>
                        <td class="text-left" style="width: 120px;">
                            <a href="{{ route('adminsdm.BankEvaluasi.edit', $item->id_bank_evaluasi) }}"
                                class="btn btn-warning btn-sm mb-2"><i class="fas fa-edit"></i>
                                Edit</a><br>
                            <a href="{{ route('adminsdm.BankEvaluasi.show', $item->id_bank_evaluasi) }}"
                                class="btn btn-primary btn-sm mb-1"><i class="fas fa-info-circle"></i>
                                Detail</a><br>
                            <button wire:click="deleteId({{ $item->id_bank_evaluasi }})"
                                class="btn btn-danger btn-sm rounded mb-1"
                                onclick="return confirm('Yakin ingin menghapus pertanyaan ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada data bank evaluasi yang tersedia.</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- Pagination --}}
    {{ $bankEvaluasi->links() }}
</div>
