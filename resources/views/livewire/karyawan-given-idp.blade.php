<div>
    <table class="table table-striped">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>Proyeksi Karir</th>
                <th>Mentor</th>
                <th>Supervisor</th>
                <th>Persetujuan Mentor</th>
                <th>Pengajuan IDP</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if ($idps->count())
                @forelse($idps as $i => $item)
                    <tr class="text-left">
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item->proyeksi_karir }}</td>
                        <td>{{ $item->mentor->name }}</td>
                        <td>{{ $item->supervisor->name }}</td>
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
                                style="background-color: {{ $bgColor }}; color: {{ $textColor }}; padding: 0px 2px; border-radius: 9999px;">
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
                                style="background-color: {{ $bgColor }}; color: {{ $textColor }}; padding: 0px 2px; border-radius: 9999px;">
                                {{ $status }}
                            </span>
                        </td>
                        {{-- <td class="text-left" style="width: 150px;">
                            <a href="{{ route('karyawan.IDP.showKaryawan', $item->id_idp) }}"
                                class="btn btn-primary btn-sm mb-1"> <i class="fas fa-external-link-alt"></i>
                                Kerjakan</a>
                            <br>
                            <br>
                        </td> --}}
                        <td class="text-right" style="width: 100px;">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Aksi
                                </button>
                                <div class="dropdown-menu dropdown-menu-right p-1"
                                    style="min-width: 120px; width: 120px;">
                                    @if (
                                        // Cek jika status pengajuan IDP tidak disetujui
                                        $item->status_pengajuan_idp !== 'Tidak Disetujui' &&
                                            // Status mentor 'Disetujui' dan pengajuan IDP 'Revisi' atau kondisi lainnya
                                            (($item->status_pengajuan_idp === 'Revisi' && $item->status_approval_mentor === 'Disetujui') ||
                                                ($item->status_pengajuan_idp === 'Tidak Disetujui' && $item->status_approval_mentor === 'Ditolak') ||
                                                ($item->status_pengajuan_idp === 'Tidak Disetujui' && $item->status_approval_mentor === 'Disetujui') ||
                                                $item->status_approval_mentor === 'Ditolak'))
                                        <a class="dropdown-item d-flex align-items-center py-1"
                                            href="{{ route('karyawan.IDP.editIdp', $item->id_idp) }}">
                                            <i class="fas fa-edit text-warning mr-2" style="width: 18px;"></i> Edit
                                        </a>
                                    @elseif ($item->status_pengajuan_idp === 'Disetujui' && $item->status_approval_mentor === 'Disetujui')
                                        <a class="dropdown-item d-flex align-items-center py-1"
                                            href="{{ route('karyawan.IDP.showKaryawan', ['id' => $item->id_idp, 'pengerjaan' => $item->id_pengerjaan ?? '']) }}">
                                            <i class="fas fa-external-link-alt text-primary mr-2"
                                                style="width: 18px;"></i> Kerjakan
                                        </a>
                                    @endif

                                    <!-- Selalu tampilkan tombol Detail, kecuali jika status pengajuan IDP adalah 'Tidak Disetujui' -->
                                    <a class="dropdown-item d-flex align-items-center py-1"
                                        href="{{ route('karyawan.IDP.detailKaryawan', $item->id_idp) }}">
                                        <i class="fas fa-eye text-info mr-2" style="width: 18px;"></i> Detail
                                    </a>
                                </div>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Belum ada data.</td>
                    </tr>
                @endforelse
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
