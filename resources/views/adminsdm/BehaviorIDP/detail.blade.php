@extends('layouts.app')

@section('title', 'Detail IDP Karyawan')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Detail IDP Karyawan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('adminsdm.BehaviorIDP.indexGiven') }}">Data IDP</a></div>
                    <div class="breadcrumb-item">Detail IDP</div>
                </div>
            </div>

            <div class="section-body">
                <div class="card">
                    <div class="card-body">
                        <h4>Informasi IDP - {{ $idps->karyawan->name }}</h4>
                        <small class="text-muted d-block mt-1">
                            Jenjang: {{ $idps->jenjang->nama_jenjang ?? '-' }} |
                            Jabatan: {{ $idps->jabatan->nama_jabatan ?? '-' }} |
                            Divisi: {{ $idps->divisi->nama_divisi ?? '-' }} |
                            Penempatan: {{ $idps->penempatan->nama_penempatan ?? '-' }} | <br>
                            Learning Group: {{ $idps->learninggroup->nama_LG ?? '-' }} |
                            Semester: {{ $idps->semester->nama_semester ?? '-' }} |
                            Angkatan PSP:
                            {{ $idp->angkatanpsp->bulan ?? '-' }} {{ $idp->angkatanpsp->tahun ?? '-' }}
                        </small>
                        <br>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>Proyeksi Karir</label>
                                <input readonly type="text" class="form-control" value="{{ $idps->proyeksi_karir }}">
                            </div>

                            <div class="form-group col-md-12">
                                <label>Deskripsi</label>
                                <textarea readonly type="text" class="form-control"style="height:6rem;">{{ $idps->deskripsi_idp }}</textarea>
                            </div>

                            <div class="form-group col-md-12">
                                <label>Mentor</label>
                                <input readonly type="text" class="form-control"
                                    value="{{ $idps->mentor->name ?? '-' }}">
                            </div>

                            <div class="form-group col-md-12">
                                <label>Supervisor</label>
                                <input readonly type="text" class="form-control"
                                    value="{{ $idps->supervisor->name ?? '-' }}">
                            </div>

                            <div class="form-group col-md-6">
                                <label>Waktu Mulai</label>
                                <input readonly type="text" class="form-control"
                                    value="{{ \Carbon\Carbon::parse($idps->waktu_mulai)->format('d-m-Y') }}">
                            </div>

                            <div class="form-group col-md-6">
                                <label>Waktu Selesai</label>
                                <input readonly type="text" class="form-control"
                                    value="{{ \Carbon\Carbon::parse($idps->waktu_selesai)->format('d-m-Y') }}">
                            </div>

                            <div class="form-group col-md-12">
                                <label>Status Approval Mentor</label>
                                <input readonly type="text" class="form-control"
                                    value="{{ $idps->status_approval_mentor }}">
                            </div>

                            <div class="form-group col-md-12">
                                <label>Status Pengajuan IDP</label>
                                <input readonly type="text" class="form-control"
                                    value="{{ $idps->status_pengajuan_idp }}">
                            </div>

                            <div class="form-group col-md-12">
                                <label>Status Pengerjaan IDP</label>
                                <input readonly type="text" class="form-control" value="{{ $idps->status_pengerjaan }}">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Saran Pengajuan IDP</label>
                                <textarea readonly type="text" class="form-control" style="height:4rem;">{{ $idps->saran_idp }}</textarea>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Daftar Kompetensi</label> <br>
                                <label> Soft Kompetensi</label>
                                @foreach ($idps->idpKompetensis->where('kompetensi.jenis_kompetensi', 'Soft Kompetensi') as $kom)
                                    <div class="accordion border-bottom mb-2 pb-2">
                                        @php
                                            $statuses = $kom->pengerjaans->pluck('status_pengerjaan');
                                        @endphp

                                        <button class="accordion-button text-start w-100 d-flex align-items-center"
                                            onclick="toggleAccordion(this)"
                                            style="border: none; background: none; padding: 0;">

                                            <span class="accordion-icon me-2">›</span>

                                            <span class="kompetensi-nama">
                                                {{ $kom->kompetensi->nama_kompetensi }}

                                                @if ($statuses->isNotEmpty())
                                                    @php
                                                        if ($statuses->every(fn($s) => $s === 'Disetujui Mentor')) {
                                                            $statusText = 'Disetujui Mentor';
                                                            $statusColor = '#3b82f6'; // biru
                                                        } else {
                                                            $statusText = 'Menunggu Persetujuan';
                                                            $statusColor = '#22c55e'; // hijau
                                                        }
                                                    @endphp
                                                    <span
                                                        style="
                                                            padding: 3px 8px; 
                                                            border-radius: 12px; 
                                                            color: white;
                                                            font-weight: 600;
                                                            background-color: {{ $statusColor }};
                                                        ">
                                                        {{ $statusText }}
                                                    </span>
                                                @endif
                                            </span>
                                        </button>
                                        <div class="accordion-content ps-4 mt-2" style="display: none;">
                                            <p><span>{{ $kom->kompetensi->keterangan }}</span></p>
                                            <p><strong>Metode Belajar:</strong>
                                                @foreach ($kom->metodeBelajars as $metode)
                                                    <span class="badge badge-info">{{ $metode->nama_metodeBelajar }}</span>
                                                @endforeach
                                            </p>
                                            <p><strong>Sasaran:</strong> <br></span>{!! nl2br(e($kom->sasaran)) !!}</p>
                                            <p><strong>Aksi:</strong> <br> {!! nl2br(e($kom->aksi)) !!}</p>
                                            <p><strong>Riwayat Upload Implementasi (Hasil)</strong></p>
                                            <table class="table table-bordered table-sm">
                                                <thead class="table-light">
                                                    <tr class="text-center">
                                                        <th width="2%">No</th>
                                                        <th width="5%">File</th>
                                                        <th width="40%">Keterangan</th>
                                                        <th width="10%">Tanggal Upload</th>
                                                        <th width="15%">Status</th>
                                                        <th width="15%">Saran</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($kom->pengerjaans as $index => $peng)
                                                        @php
                                                            $ext = strtolower(
                                                                pathinfo($peng->upload_hasil, PATHINFO_EXTENSION),
                                                            );
                                                            $icon = match ($ext) {
                                                                'pdf' => 'bi bi-file-earmark-pdf-fill text-danger',
                                                                'doc',
                                                                'docx'
                                                                    => 'bi bi-file-earmark-word-fill text-primary',
                                                                'xls',
                                                                'xlsx'
                                                                    => 'bi bi-file-earmark-excel-fill text-success',
                                                                'jpg',
                                                                'jpeg',
                                                                'png'
                                                                    => 'bi bi-file-earmark-image-fill text-warning',
                                                                'mp4' => 'bi bi-file-earmark-play-fill text-dark',
                                                                default => 'bi bi-file-earmark-fill',
                                                            };
                                                            $fileUrl = asset('storage/' . $peng->upload_hasil);
                                                            $isPreviewable = in_array($ext, [
                                                                'pdf',
                                                                'jpg',
                                                                'jpeg',
                                                                'png',
                                                                'mp4',
                                                            ]);
                                                        @endphp
                                                        <tr>
                                                            <td class="text-center">{{ $loop->iteration }}</td>
                                                            <td class="text-center">
                                                                @if ($isPreviewable)
                                                                    {{-- File bisa dibuka langsung --}}
                                                                    <a href="{{ $fileUrl }}" target="_blank"
                                                                        title="Lihat file">
                                                                        <i class="{{ $icon }}"
                                                                            style="font-size: 1.5rem;"></i>
                                                                    </a>
                                                                @else
                                                                    {{-- File harus didownload --}}
                                                                    <a href="{{ $fileUrl }}" download
                                                                        title="Download file">
                                                                        <i class="{{ $icon }}"
                                                                            style="font-size: 1.5rem;"></i>
                                                                    </a>
                                                                @endif
                                                            </td>
                                                            <td>{{ $peng->keterangan_hasil ?? '-' }}</td>
                                                            <td class="text-center">
                                                                {{ $peng->created_at->format('d-m-Y') }}</td>
                                                            @php
                                                                $statusColors = [
                                                                    'Menunggu Persetujuan' => [
                                                                        'bg' => '#d1fae5',
                                                                        'text' => '#065f46',
                                                                    ], // hijau muda & hijau tua
                                                                    'Disetujui Mentor' => [
                                                                        'bg' => '#bfdbfe',
                                                                        'text' => '#1e3a8a',
                                                                    ], // biru muda & biru tua
                                                                    'Ditolak Mentor' => [
                                                                        'bg' => '#fecaca',
                                                                        'text' => '#991b1b',
                                                                    ], // merah muda & merah tua
                                                                    'Revisi Mentor' => [
                                                                        'bg' => '#fef3c7',
                                                                        'text' => '#92400e',
                                                                    ], // kuning muda & kuning tua
                                                                ];

                                                                $bgColor =
                                                                    $statusColors[$peng->status_pengerjaan]['bg'] ??
                                                                    '#e5e7eb'; // default abu-abu
                                                                $textColor =
                                                                    $statusColors[$peng->status_pengerjaan]['text'] ??
                                                                    '#374151'; // default abu-abu gelap
                                                            @endphp

                                                            <td class="text-center">
                                                                <span
                                                                    style="background-color: {{ $bgColor }}; color: {{ $textColor }}; padding: 4px 10px; border-radius: 9999px;">
                                                                    {{ $peng->status_pengerjaan }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $peng->saran }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-group col-md-12">
                                <label> Hard Kompetensi</label>
                                @foreach ($idps->idpKompetensis->where('kompetensi.jenis_kompetensi', 'Hard Kompetensi') as $kom)
                                    <div class="accordion border-bottom mb-2 pb-2">
                                        @php
                                            $statuses = $kom->pengerjaans->pluck('status_pengerjaan');
                                        @endphp

                                        <button class="accordion-button text-start w-100 d-flex align-items-center"
                                            onclick="toggleAccordion(this)"
                                            style="border: none; background: none; padding: 0;">

                                            <span class="accordion-icon me-2">›</span>

                                            <span class="kompetensi-nama">
                                                {{ $kom->kompetensi->nama_kompetensi }}

                                                @if ($statuses->isNotEmpty())
                                                    @php
                                                        if ($statuses->every(fn($s) => $s === 'Disetujui Mentor')) {
                                                            $statusText = 'Disetujui Mentor';
                                                            $statusColor = '#3b82f6'; // biru
                                                        } else {
                                                            $statusText = 'Menunggu Persetujuan';
                                                            $statusColor = '#22c55e'; // hijau
                                                        }
                                                    @endphp
                                                    <span
                                                        style="
                                                            padding: 3px 8px; 
                                                            border-radius: 12px; 
                                                            color: white;
                                                            font-weight: 600;
                                                            background-color: {{ $statusColor }};
                                                        ">
                                                        {{ $statusText }}
                                                    </span>
                                                @endif
                                            </span>
                                        </button>

                                        <div class="accordion-content ps-4 mt-2" style="display: none;">
                                            <p><span>{{ $kom->kompetensi->keterangan }}</span></p>
                                            <p><strong>Metode Belajar:</strong>
                                                @foreach ($kom->metodeBelajars as $metode)
                                                    <span
                                                        class="badge badge-info">{{ $metode->nama_metodeBelajar }}</span>
                                                @endforeach
                                            </p>
                                            <p><strong>Sasaran:</strong> <br></span>{!! nl2br(e($kom->sasaran)) !!}</p>
                                            <p><strong>Aksi:</strong> <br> {!! nl2br(e($kom->aksi)) !!}</p>
                                            <p><strong>Riwayat Upload Implementasi (Hasil)</strong></p>
                                            <table class="table table-bordered table-sm">
                                                <thead class="table-light">
                                                    <tr class="text-center">
                                                        <th width="2%">No</th>
                                                        <th width="5%">File</th>
                                                        <th width="40%">Keterangan</th>
                                                        <th width="10%">Tanggal Upload</th>
                                                        <th width="15%">Status</th>
                                                        <th width="15%">Saran</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($kom->pengerjaans as $index => $peng)
                                                        @php
                                                            $ext = strtolower(
                                                                pathinfo($peng->upload_hasil, PATHINFO_EXTENSION),
                                                            );
                                                            $icon = match ($ext) {
                                                                'pdf' => 'bi bi-file-earmark-pdf-fill text-danger',
                                                                'doc',
                                                                'docx'
                                                                    => 'bi bi-file-earmark-word-fill text-primary',
                                                                'xls',
                                                                'xlsx'
                                                                    => 'bi bi-file-earmark-excel-fill text-success',
                                                                'jpg',
                                                                'jpeg',
                                                                'png'
                                                                    => 'bi bi-file-earmark-image-fill text-warning',
                                                                'mp4' => 'bi bi-file-earmark-play-fill text-dark',
                                                                default => 'bi bi-file-earmark-fill',
                                                            };
                                                            $fileUrl = asset('storage/' . $peng->upload_hasil);
                                                            $isPreviewable = in_array($ext, [
                                                                'pdf',
                                                                'jpg',
                                                                'jpeg',
                                                                'png',
                                                                'mp4',
                                                            ]);
                                                        @endphp
                                                        <tr>
                                                            <td class="text-center">{{ $loop->iteration }}</td>
                                                            <td class="text-center">
                                                                @if ($isPreviewable)
                                                                    {{-- File bisa dibuka langsung --}}
                                                                    <a href="{{ $fileUrl }}" target="_blank"
                                                                        title="Lihat file">
                                                                        <i class="{{ $icon }}"
                                                                            style="font-size: 1.5rem;"></i>
                                                                    </a>
                                                                @else
                                                                    {{-- File harus didownload --}}
                                                                    <a href="{{ $fileUrl }}" download
                                                                        title="Download file">
                                                                        <i class="{{ $icon }}"
                                                                            style="font-size: 1.5rem;"></i>
                                                                    </a>
                                                                @endif
                                                            </td>
                                                            <td>{{ $peng->keterangan_hasil ?? '-' }}</td>
                                                            <td class="text-center">
                                                                {{ $peng->created_at->format('d-m-Y') }}</td>
                                                            @php
                                                                $statusColors = [
                                                                    'Menunggu Persetujuan' => [
                                                                        'bg' => '#d1fae5',
                                                                        'text' => '#065f46',
                                                                    ], // hijau muda & hijau tua
                                                                    'Disetujui Mentor' => [
                                                                        'bg' => '#bfdbfe',
                                                                        'text' => '#1e3a8a',
                                                                    ], // biru muda & biru tua
                                                                    'Ditolak Mentor' => [
                                                                        'bg' => '#fecaca',
                                                                        'text' => '#991b1b',
                                                                    ], // merah muda & merah tua
                                                                    'Revisi Mentor' => [
                                                                        'bg' => '#fef3c7',
                                                                        'text' => '#92400e',
                                                                    ], // kuning muda & kuning tua
                                                                ];

                                                                $bgColor =
                                                                    $statusColors[$peng->status_pengerjaan]['bg'] ??
                                                                    '#e5e7eb'; // default abu-abu
                                                                $textColor =
                                                                    $statusColors[$peng->status_pengerjaan]['text'] ??
                                                                    '#374151'; // default abu-abu gelap
                                                            @endphp

                                                            <td class="text-center">
                                                                <span
                                                                    style="background-color: {{ $bgColor }}; color: {{ $textColor }}; padding: 4px 10px; border-radius: 9999px;">
                                                                    {{ $peng->status_pengerjaan }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $peng->saran }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <a class="btn btn-primary" href="{{ route('adminsdm.BehaviorIDP.indexGiven') }}">Kembali</a>
                    </div>
                </div>
            </div>
    </div>
    </div>
    </section>
    </div>

    <script>
        function toggleAccordion(button) {
            const content = button.nextElementSibling;
            const icon = button.querySelector('.accordion-icon');
            if (content.style.display === "none" || content.style.display === "") {
                content.style.display = "block";
                icon.innerHTML = "˅";
            } else {
                content.style.display = "none";
                icon.innerHTML = "›";
            }
        }
    </script>

@endsection
