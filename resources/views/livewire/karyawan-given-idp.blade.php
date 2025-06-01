<div>
    <table class="table table-striped">
        <thead>
            <tr class="">
                <th>No</th>
                <th>Proyeksi Karir</th>
                <th>Persetujuan Mentor</th>
                <th>Status Pengajuan IDP</th>
                <th>Progres IDP</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if ($idps->count())
                @forelse($idps as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item->proyeksi_karir }}</td>
                        <td>{{ $item->status_approval_mentor }}</td>
                        <td>{{ $item->status_pengajuan_idp }}</td>
                        <td>
                            @php
                                // Data asli dari IDP
                                $total = $item->idpKompetensis->count(); // total kompetensi
                                $selesai = $item->where('status_pengerjaan', 'Disetujui Mentor')->count(); // hanya yang disetujui mentor
                                $persen = $total > 0 ? round(($selesai / $total) * 100) : 0;

                                // Warna progress bar
                                $warna = 'bg-danger';
                                if ($persen >= 80) {
                                    $warna = 'bg-success';
                                } elseif ($persen >= 50) {
                                    $warna = 'bg-warning';
                                }
                            @endphp
                            <div style="font-size: 10px;" class="text-muted mb-1">
                                {{ $selesai }}/{{ $total }} | {{ $persen }}%
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 999px;">
                                <div class="progress-bar {{ $warna }}" role="progressbar"
                                    style="width: {{ $persen }}%; border-radius: 999px;"
                                    aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </td>

                        <td class="text-left" style="width: 150px;">
                            <a href="{{ route('karyawan.IDP.showKaryawan', $item->id_idp) }}"
                                class="btn btn-primary btn-sm mb-1"> <i class="fas fa-external-link-alt"></i>
                                Kerjakan</a>
                            <br>
                            <br>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data.</td>
                    </tr>
                @endforelse
            @else
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">
                        Data Tidak Ditemukan
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- Pagination --}}
    {{ $idps->links() }}
</div>
