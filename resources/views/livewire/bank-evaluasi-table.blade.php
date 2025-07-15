<div>
    <div class="table-responsive">
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
                                {{ $loop->iteration + ($bankEvaluasi->currentPage() - 1) * $bankEvaluasi->perPage() }}
                            </td>
                            <td>{{ $item->pertanyaan }}</td>
                            <td class="text-center">{{ $item->tipe_pertanyaan }}</td>
                            <td class="text-center">{{ $item->jenis_evaluasi }}</td>
                            <td class="text-center">{{ $item->untuk_role }}</td>
                            <td class="text-left" style="width: 100px;">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Aksi
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right p-1"
                                        style="min-width: 130px; width: 130px;">
                                        <a class="dropdown-item d-flex align-items-center py-1"
                                            href="{{ route('adminsdm.BankEvaluasi.edit', $item->id_bank_evaluasi) }}">
                                            <i class="fas fa-edit text-warning mr-2" style="width: 18px;"></i> Edit
                                        </a>
                                        <a class="dropdown-item d-flex align-items-center py-1"
                                            href="{{ route('adminsdm.BankEvaluasi.show', $item->id_bank_evaluasi) }}">
                                            <i class="fas fa-info-circle text-success mr-2" style="width: 18px;"></i>
                                            Detail
                                        </a>
                                        <a href="#"
                                            class="dropdown-item text-danger d-flex align-items-center py-1"
                                            onclick="event.preventDefault(); if(confirm('Yakin ingin menghapus pertanyaan ini?')) @this.deleteId({{ $item->id_bank_evaluasi }})">
                                            <i class="fas fa-trash-alt mr-2" style="width: 18px;"></i> Hapus
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada data bank evaluasi yang tersedia.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- Pagination --}}
        {{ $bankEvaluasi->links() }}
    </div>
</div>
