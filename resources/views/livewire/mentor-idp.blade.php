<div>
    <table class="table table-striped">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>Nama Karyawan</th>
                <th>Proyeksi Karir</th>
                <th>Persetujuan Mentor</th>
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
                            <span
                                style="background-color: #d1fae5; color: #065f46; padding: 4px 10px; border-radius: 9999px;">
                                {{ $item->status_approval_mentor }}
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
                            <a href="{{ route('mentor.IDP.mentor.idp.show', $item->id_idp) }}"
                                class="btn btn-primary btn-sm mb-1"> <i class="fas fa-external-link-alt"></i>
                                Detail</a>
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
