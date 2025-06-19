<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Proyeksi Karir</th>
                <th class="text-center">Nama Karyawan</th>
                <th class="text-center">Nama Mentor</th>
                <th class="text-center">Nama Supervisor</th>
                <th class="text-center">Learning Group</th>
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
                        <td class="text-center">{{ optional($idp->karyawan)->name ?? '-' }}</td>
                        <td class="text-center">{{ optional($idp->mentor)->name ?? '-' }}</td>
                        <td class="text-center">{{ $idp->supervisor->name ?? '-' }}</td>
                        <td class="text-center">{{ $idp->karyawan->learningGroup->nama_LG ?? '-' }}</td>
                        <td>
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
                        <td>{{ $idp->rekomendasis->first()->hasil_rekomendasi ?? 'Menunggu Penilaian' }}</td>
                        <td class="text-left" style="width: 100px;">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Aksi
                                </button>
                                <div class="dropdown-menu dropdown-menu-right p-1"
                                    style="min-width: 130px; width: 130px;">
                                    <a class="dropdown-item d-flex align-items-center py-1"
                                        href="{{ route('adminsdm.BehaviorIDP.RiwayatIDP.showRiwayatIdp', $idp->id_idp) }}">
                                        <i class="fas fa-info-circle text-success mr-2" style="width: 18px;"></i> Detail
                                    </a>
                                    <a href="#" class="dropdown-item text-danger d-flex align-items-center py-1"
                                        onclick="event.preventDefault(); if(confirm('Yakin ingin menghapus data ini?')) document.getElementById('form-delete-{{ $idp->id_idp }}').submit();">
                                        <i class="fas fa-trash-alt mr-2" style="width: 18px; line-height: 1;"></i> Hapus
                                    </a>
                                    <form id="form-delete-{{ $idp->id_idp }}"
                                        action="{{ route('adminsdm.BehaviorIDP.destroyGiven', $idp->id_idp) }}"
                                        method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
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
