<div>
    <table class="table table-striped">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>Nama Karyawan</th>
                {{-- <th>NPK Karyawan</th>
                <th>Jenjang Karyawan</th> --}}
                <th>Learning Group</th>
                <th>Proyeksi Karir</th>
                <th>Nama Mentor</th>
                <th>Rating Rekomendasi</th>
                <th>Progres IDP</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if ($idps->count())
                @forelse($idps as $i => $item)
                    <tr class="text-center">
                        <td class="text-center" style="width: 30px;">{{ $i + 1 }}</td>
                        <td>{{ $item->karyawan->name ?? 'Menunggu Applay IDP' }}</td>
                        {{-- <td>{{ $item->karyawan->npk ?? 'Menunggu Applay IDP' }}</td>
                        <td>{{ $item->jenjang->nama_jenjang }}</td> --}}
                        <td>{{ $item->learningGroup->nama_LG }}</td>
                        <td>{{ $item->proyeksi_karir }}</td>
                        <td>{{ $item->mentor->name ?? 'Menunggu Persetujuan Mentor' }}</td>
                        @php
                            $hasil = $item->rekomendasis->first()->hasil_rekomendasi ?? null;
                            $bgColor = '#fef3c7'; // default: kuning muda (Menunggu Penilaian)
                            $textColor = '#92400e'; // default: coklat gelap

                            if ($hasil === 'Disarankan') {
                                $bgColor = '#dbeafe'; // biru muda
                                $textColor = '#1e3a8a'; // biru tua
                            } elseif ($hasil === 'Disarankan dengan Pengembangan') {
                                $bgColor = '#d1fae5'; // hijau muda
                                $textColor = '#065f46'; // hijau tua
                            } elseif ($hasil === 'Tidak Disarankan') {
                                $bgColor = '#fee2e2'; // merah muda
                                $textColor = '#991b1b'; // merah tua
                            } elseif (!$hasil) {
                                $hasil = 'Menunggu Penilaian';
                            }
                        @endphp

                        <td class="text-center">
                            <span
                                style="background-color: {{ $bgColor }}; color: {{ $textColor }}; padding: 1px 4px; border-radius: 9999px">
                                {{ $hasil }}
                            </span>
                        </td>
                        <td>
                            @php
                                $idpKompetensis = $item->idpKompetensis;
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
                        <td class="text-left" style="width: 140px;">
                            <a href="{{ route('supervisor.IDP.showSupervisor', $item->id_idp) }}"
                                class="btn btn-primary btn-sm mb-1">
                                <i class="bi bi-pencil-square"></i> Nilai
                            </a>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data.</td>
                    </tr>
                @endforelse
            @else
                <tr>
                    <td colspan="12" class="text-center text-muted py-3">
                        Data Tidak Ditemukan
                    </td>
                </tr>
            @endif

        </tbody>
    </table>

    {{-- Pagination --}}
    {{ $idps->links() }}
</div>
