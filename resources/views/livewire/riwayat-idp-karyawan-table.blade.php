<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Proyeksi Karir</th>
                {{-- <th class="text-center">Nama Karyawan</th> --}}
                <th class="text-center">Nama Mentor</th>
                <th class="text-center">Nama Supervisor</th>
                <th class="text-center">Direktorat</th>
                <th class="text-center">Progres IDP</th>
                <th class="text-center">Hasil Rekomendasi</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if ($idps->count())
                @foreach ($idps as $idp)
                    <tr>
                        <td class="text-center" style="width: 50px;">
                            {{ $loop->iteration + ($idps->currentPage() - 1) * $idps->perPage() }}
                        </td>
                        <td class="text-center">{{ $idp->proyeksi_karir }}</td>
                        {{-- <td class="text-center">{{ optional($idp->karyawan)->name ?? '-' }}</td> --}}
                        <td class="text-center">{{ optional($idp->mentor)->name ?? '-' }}</td>
                        <td class="text-center">{{ $idp->supervisor->name ?? '-' }}</td>
                        <td class="text-center">{{ $idp->karyawan->learningGroup->nama_LG ?? '-' }}</td>
                        <td class="text-center">
                            @php
                                $idpKompetensis = $idp->idpKompetensis;
                                $totalKompetensi = $idpKompetensis->count();
                                $jumlahSelesai = 0;

                                foreach ($idpKompetensis as $kom) {
                                    $totalUpload = $kom->pengerjaans->count(); // banyak upload per kompetensi
                                    $jumlahDisetujui = $kom->pengerjaans
                                        ->where('status_pengerjaan', 'Disetujui Mentor')
                                        ->count();

                                    // Kompetensi dianggap selesai jika semua upload-nya disetujui
                                    if ($totalUpload > 0 && $totalUpload === $jumlahDisetujui) {
                                        $jumlahSelesai++;
                                    }
                                }

                                $persen = $totalKompetensi > 0 ? round(($jumlahSelesai / $totalKompetensi) * 100) : 0;

                                // Warna progress bar berdasarkan persentase
                                $warna = 'bg-danger';
                                if ($persen >= 80) {
                                    $warna = 'bg-success';
                                } elseif ($persen >= 50) {
                                    $warna = 'bg-warning';
                                }
                            @endphp

                            <div style="font-size: 10px;" class="text-muted mb-1">
                                {{ $jumlahSelesai }}/{{ $totalKompetensi }} | {{ $persen }}%
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 999px;">
                                <div class="progress-bar {{ $warna }}" role="progressbar"
                                    style="width: {{ $persen }}%; border-radius: 999px;"
                                    aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </td>
                        <td class="text-center">{{ $idp->rekomendasis->first()->hasil_rekomendasi ?? 'Menunggu Penilaian' }}</td>
                        <td class="text-left" style="width: 120px;">
                            <a href="{{ route('karyawan.IDP.RiwayatIDP.showRiwayatIdp', $idp->id_idp) }}"
                                class="btn btn-primary btn-sm mb-1">
                                <i class="fas fa-info-circle"></i> Detail
                            </a>
                            <br>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8" class="text-center text-muted py-3">
                        Data Tidak Ditemukan
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- Pagination --}}
    {{ $idps->links() }}
</div>
