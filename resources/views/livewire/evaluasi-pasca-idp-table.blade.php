<div>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr class="text-center">
                    <th>No</th>
                    <th>Nama Pengisi</th>
                    <th>Judul IDP</th>
                    <th>Tanggal Evaluasi</th>
                    <th>Jenis Evaluasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($evaluasiPasca as $item)
                    <tr class="text-center">
                        <td>{{ $loop->iteration + ($evaluasiPasca->currentPage() - 1) * $evaluasiPasca->perPage() }}
                        </td>
                        <td>{{ $item->user->name ?? '-' }}</td>
                        <td>{{ $item->idps->proyeksi_karir ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_evaluasi)->format('d M Y') }}</td>
                        <td>{{ ucfirst($item->jenis_evaluasi) }}</td>
                        <td class="text-left" style="width: 100px;">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Aksi
                                </button>
                                <div class="dropdown-menu dropdown-menu-right p-1"
                                    style="min-width: 130px; width: 130px;">
                                    <a class="dropdown-item d-flex align-items-center py-1"
                                        href="{{ route('adminsdm.BankEvaluasi.EvaluasiPascaIdp.showKaryawan', $item->id_evaluasi_idp) }}">
                                        <i class="fas fa-info-circle text-success mr-2" style="width: 18px;"></i> Detail
                                    </a>
                                    <a href="#" class="dropdown-item text-danger d-flex align-items-center py-1"
                                        onclick="event.preventDefault(); if(confirm('Yakin ingin menghapus data ini?')) @this.deleteId({{ $item->id_evaluasi_idp }})">
                                        <i class="fas fa-trash-alt mr-2" style="width: 18px;"></i> Hapus
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada data evaluasi pasca IDP.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $evaluasiPasca->links() }}
    </div>
</div>
