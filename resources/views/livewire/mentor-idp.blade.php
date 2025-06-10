<div>
    <table class="table table-striped">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>Nama Karyawan</th>
                <th>Proyeksi Karir</th>
                <th>Persetujuan Mentor</th>
                <th>Persetujuan IDP</th>
                <th>Supervisor</th>
                <th>Progres IDP</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if ($idps->count())
                @forelse($idps as $i => $item)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $item->karyawan->name }}</td>
                        <td>{{ $item->proyeksi_karir }}</td>
                        <td class="text-center">
                            @php
                                $status = $item->status_approval_mentor;
                                $bgColor = '';
                                $textColor = '';

                                if ($status === 'Menunggu Persetujuan') {
                                    $bgColor = '#fef3c7'; // kuning muda
                                    $textColor = '#92400e'; // coklat gelap
                                } elseif ($status === 'Disetujui') {
                                    $bgColor = '#d1fae5'; // hijau muda
                                    $textColor = '#065f46'; // hijau tua
                                } elseif ($status === 'Ditolak') {
                                    $bgColor = '#fee2e2'; // merah muda
                                    $textColor = '#991b1b'; // merah tua
                                }
                            @endphp

                            <span
                                style="background-color: {{ $bgColor }}; color: {{ $textColor }}; padding: 1px 6px; border-radius: 9999px;">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="text-center">
                            @php
                                $status = $item->status_pengajuan_idp;
                                $bgColor = '';
                                $textColor = '';

                                if ($status === 'Menunggu Persetujuan') {
                                    $bgColor = '#fef3c7'; // kuning muda
                                    $textColor = '#92400e'; // coklat gelap
                                } elseif ($status === 'Disetujui') {
                                    $bgColor = '#d1fae5'; // hijau muda
                                    $textColor = '#065f46'; // hijau tua
                                } elseif ($status === 'Revisi') {
                                    $bgColor = '#dbeafe'; // biru muda
                                    $textColor = '#1e3a8a'; // biru tua
                                } elseif ($status === 'Tidak Disetujui') {
                                    $bgColor = '#fee2e2'; // merah muda
                                    $textColor = '#991b1b'; // merah tua
                                }
                            @endphp

                            <span
                                style="background-color: {{ $bgColor }}; color: {{ $textColor }}; padding: 1px 6px; border-radius: 9999px;">
                                {{ $status }}
                            </span>
                        </td>

                        <td>{{ $item->supervisor->name }}</td>
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
                        <td class="text-left" style="width: 150px;">
                            @if (
                                ($item->status_approval_mentor === 'Menunggu Persetujuan' &&
                                    $item->status_pengajuan_idp === 'Menunggu Persetujuan') ||
                                    ($item->status_approval_mentor === 'Disetujui' && $item->status_pengajuan_idp === 'Menunggu Persetujuan'))
                                <a href="{{ route('mentor.IDP.verfikasi', $item->id_idp) }}"
                                    class="btn btn-warning btn-sm mb-1">
                                    <i class="fas fa-edit"></i> Verifikasi
                                </a>
                            @else
                                <a href="{{ route('mentor.IDP.mentor.idp.show', $item->id_idp) }}"
                                    class="btn btn-primary btn-sm mb-1">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            @endif
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
