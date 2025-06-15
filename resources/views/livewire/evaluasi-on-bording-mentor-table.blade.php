<div>
    <table class="table">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>Nama Karyawan</th>
                <th>Judul IDP</th>
                <th>Status</th>
                <th>Progres</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($idps as $index => $idp)
                <tr class="text-center">
                    <td>{{ $idps->firstItem() + $index }}</td>
                    <td>{{ $idp->user->name }}</td>
                    <td>{{ $idp->proyeksi_karir }}</td>
                    <td class="text-center">
                        @php
                            $idpKompetensis = $idp->idpKompetensis ?? collect();
                            $totalKompetensi = $idpKompetensis->count();
                            $jumlahSelesai = 0;

                            foreach ($idpKompetensis as $kom) {
                                $totalUpload = $kom->pengerjaans->count();
                                $jumlahDisetujui = $kom->pengerjaans
                                    ->where('status_pengerjaan', 'Disetujui Mentor')
                                    ->count();

                                if ($totalUpload > 0 && $totalUpload === $jumlahDisetujui) {
                                    $jumlahSelesai++;
                                }
                            }

                            $persen = $totalKompetensi > 0 ? round(($jumlahSelesai / $totalKompetensi) * 100) : 0;
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
                    <td>
                        <span class="badge badge-info">Siap Dievaluasi</span>
                    </td>
                    <td>
                        <a href="{{ route('mentor.EvaluasiIdp.EvaluasiOnBording.create', [
                            'id_idp' => $idp->id_idp,
                            'id_user' => Auth::id(),
                            'jenis' => 'onboarding',
                        ]) }}"
                            class="btn btn-success btn-sm">
                            <i class="fas fa-comment-dots mr-1"></i> Evaluasi
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $idps->links() }}
</div>
