@extends('layouts.app')

@section('title', 'Detail IDP')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Detail IDP</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('karyawan.IDP.indexKaryawan') }}">Data IDP</a></div>
                    <div class="breadcrumb-item">Detail IDP</div>
                </div>
            </div>

            <div class="section-body">
                <div class="card" style="border-left: 5px solid #28a745; background-color: #e6f9d7;">
                    <div class="card-body" style="color: #212529;">
                        Isilah form dibawah ini dengan baik dan benar. Semua data yang anda inputkan pada form ini haruslah
                        asli dan bukan rekayasa serta benar-benar dapat dipertanggungjawabkan. Refleksi personal adalah
                        proses merenung dan mengevaluasi diri sendiri terhadap pengalaman, tindakan, dan pemikiran yang
                        telah kita lakukan. Ini seperti "mencerminkan" diri sendiri untuk memahami diri lebih dalam,
                        mengidentifikasi kekuatan dan kelemahan, serta belajar dari pengalaman.
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4>Informasi IDP - {{ $idps->karyawan->name }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>Proyeksi Karir</label>
                                <input readonly type="text" class="form-control" value="{{ $idps->proyeksi_karir }}">
                            </div>

                            <div class="form-group col-md-12">
                                <label>Deskripsi</label>
                                <input readonly type="text" class="form-control" value="{{ $idps->deskripsi_idp }}">
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
                                <input readonly type="text" class="form-control" value="{{ $idps->saran_idp }}">
                            </div>

                            <div class="form-group col-md-12">
                                <label>Daftar Kompetensi</label> <br>
                                <label>Soft Kompetensi</label>
                                @foreach ($idps->idpKompetensis->where('kompetensi.jenis_kompetensi', 'Soft Kompetensi') as $index => $kom)
                                    <div class="accordion border-bottom mb-2 pb-2">
                                        @php
                                            $statuses = $kom->pengerjaans->pluck('status_pengerjaan');

                                            if ($statuses->every(fn($s) => $s === 'Disetujui Mentor')) {
                                                $statusText = 'Disetujui Mentor';
                                                $statusColor = '#3b82f6'; // biru
                                            } else {
                                                $statusText = 'Menunggu Persetujuan';
                                                $statusColor = '#16a34a'; // hijau
                                            }
                                        @endphp

                                        <button class="accordion-button text-start w-100 d-flex align-items-center"
                                            onclick="toggleAccordion(this)"
                                            style="border: none; background: none; padding: 0;">

                                            <span class="accordion-icon me-2">›</span>
                                            <span class="kompetensi-nama">
                                                {{ $kom->kompetensi->nama_kompetensi }}
                                                <span
                                                    style="
                                                        padding: 3px 8px; 
                                                        border-radius: 12px; 
                                                        color: white;
                                                        font-weight: 600;
                                                        background-color: 
                                                        {{ $statusText == 'Disetujui Mentor' ? '#3b82f6' : '#22c55e' }};">
                                                    {{ $statusText }}
                                                </span>
                                            </span>
                                        </button>
                                        <div class="accordion-content ps-4 mt-2" style="display: none;">
                                            <p><span>{{ $kom->kompetensi->keterangan }}</span></p>
                                            <p><strong>Metode Belajar:</strong>
                                                @foreach ($kom->metodeBelajars as $metode)
                                                    <span class="badge badge-info">{{ $metode->nama_metodeBelajar }}</span>
                                                @endforeach
                                            </p>
                                            <p><strong>Sasaran:</strong> <br>{!! nl2br(e($kom->sasaran)) !!}</p>
                                            <p><strong>Aksi:</strong> <br> {!! nl2br(e($kom->aksi)) !!}</p>

                                            {{-- BAGIAN FORM UPLOAD --}}
                                            <p><strong>Implementasi (Hasil)</strong></p>
                                            <form
                                                action="{{ route('karyawan.IDP.storeImplementasiSoft', ['id_idpKom' => $kom->id_idpKom]) }}"
                                                method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="dashed-border-container p-4 mt-4 mb-4"
                                                    style="border: 2px dashed #ddd; border-radius: 8px;">
                                                    <div class="form-group col-md-12 mb-3">
                                                        <div class="border p-4 text-center"
                                                            style="background-color: #f8f9fa;">
                                                            <div
                                                                class="d-flex justify-content-center align-items-center mb-3">
                                                                <i class="fas fa-cloud-upload-alt fa-2x text-muted"></i>
                                                            </div>
                                                            <p>Choose a file or drag & drop it here</p>
                                                            <p class="text-muted">Format pdf,doc,docx,xlsx,jpg,jpeg,png,csv
                                                                , ukuran file
                                                                5MB</p>
                                                            <input type="file" name="upload_hasil"
                                                                id="fileImplementasiSoft_{{ $kom->id_idpKom }}"
                                                                style="display: none;"
                                                                onchange="displaySelectedFile(this, 'fileNameSoft_{{ $kom->id_idpKom }}', 'fileErrorHard_{{ $kom->id_idpKom }}')">
                                                            <button type="button" class="btn btn-outline-secondary"
                                                                onclick="document.getElementById('fileImplementasiSoft_{{ $kom->id_idpKom }}').click()">
                                                                Browse File
                                                            </button>
                                                            <div id="fileNameSoft_{{ $kom->id_idpKom }}"
                                                                class="mt-2 text-primary" style="display: none;"></div>
                                                            <div id="fileErrorHard_{{ $kom->id_idpKom }}"
                                                                class="mt-2 text-danger" style="display: none;"></div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group col-md-12 mb-3">
                                                        <label
                                                            for="keterangan_hasil_implementasi_soft_{{ $kom->id_idpKom }}">Keterangan</label>
                                                        <textarea class="form-control" id="keterangan_hasil_implementasi_soft_{{ $kom->id_idpKom }}" style="height:6rem;"
                                                            name="keterangan_hasil" rows="3" placeholder="Ketikkan pencapaian atau hasil implementasi..."></textarea>
                                                    </div>

                                                    <div class="d-flex justify-content-end mt-4">
                                                        <button type="submit" class="btn btn-success me-2">
                                                            <i class="fas fa-save me-1"></i> Simpan
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
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
                                                                @php
                                                                    $isRevisi = in_array($peng->status_pengerjaan, [
                                                                        'Ditolak Mentor',
                                                                        'Revisi Mentor',
                                                                    ]);
                                                                    $statusColors = [
                                                                        'Menunggu Persetujuan' => [
                                                                            'bg' => '#d1fae5',
                                                                            'text' => '#065f46',
                                                                        ],
                                                                        'Disetujui Mentor' => [
                                                                            'bg' => '#bfdbfe',
                                                                            'text' => '#1e3a8a',
                                                                        ],
                                                                        'Ditolak Mentor' => [
                                                                            'bg' => '#fecaca',
                                                                            'text' => '#991b1b',
                                                                        ],
                                                                        'Revisi Mentor' => [
                                                                            'bg' => '#fef3c7',
                                                                            'text' => '#92400e',
                                                                        ],
                                                                    ];
                                                                    $bgColor =
                                                                        $statusColors[$peng->status_pengerjaan]['bg'] ??
                                                                        '#e5e7eb';
                                                                    $textColor =
                                                                        $statusColors[$peng->status_pengerjaan][
                                                                            'text'
                                                                        ] ?? '#374151';
                                                                @endphp

                                                                @if ($isRevisi)
                                                                    <button
                                                                        class="btn btn-sm d-flex align-items-center justify-content-center mx-auto"
                                                                        style="background-color: {{ $bgColor }}; color: {{ $textColor }}; border: 1px solid {{ $textColor }};"
                                                                        data-toggle="modal"
                                                                        data-target="#uploadUlangModal{{ $peng->id }}">
                                                                        <i class="bi bi-upload mr-1"></i>
                                                                        {{ $peng->status_pengerjaan }}
                                                                    </button>
                                                                @else
                                                                    <span
                                                                        style="background-color: {{ $bgColor }}; color: {{ $textColor }}; padding: 4px 10px; border-radius: 9999px;">
                                                                        {{ $peng->status_pengerjaan }}
                                                                    </span>
                                                                @endif

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
                                <label>Hard Kompetensi</label>
                                @foreach ($idps->idpKompetensis->where('kompetensi.jenis_kompetensi', 'Hard Kompetensi') as $index => $kom)
                                    <div class="accordion border-bottom mb-2 pb-2">
                                        @php
                                            $statuses = $kom->pengerjaans->pluck('status_pengerjaan');

                                            if ($statuses->every(fn($s) => $s === 'Disetujui Mentor')) {
                                                $statusText = 'Disetujui Mentor';
                                                $statusColor = '#3b82f6'; // biru
                                            } else {
                                                $statusText = 'Menunggu Persetujuan';
                                                $statusColor = '#16a34a'; // hijau
                                            }
                                        @endphp

                                        <button class="accordion-button text-start w-100 d-flex align-items-center"
                                            onclick="toggleAccordion(this)"
                                            style="border: none; background: none; padding: 0;">

                                            <span class="accordion-icon me-2">›</span>
                                            <span class="kompetensi-nama">
                                                {{ $kom->kompetensi->nama_kompetensi }}
                                                <span
                                                    style="
                                                        padding: 3px 8px; 
                                                        border-radius: 12px; 
                                                        color: white;
                                                        font-weight: 600;
                                                        background-color: 
                                                        {{ $statusText == 'Disetujui Mentor' ? '#3b82f6' : '#22c55e' }};">
                                                    {{ $statusText }}
                                                </span>
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
                                            <p><strong>Sasaran:</strong> <br>{!! nl2br(e($kom->sasaran)) !!}</p>
                                            <p><strong>Aksi:</strong> <br> {!! nl2br(e($kom->aksi)) !!}</p>

                                            {{-- BAGIAN FORM UPLOAD --}}
                                            <p><strong>Implementasi (Hasil)</strong></p>
                                            <form
                                                action="{{ route('karyawan.IDP.storeImplementasiHard', ['id_idpKom' => $kom->id_idpKom]) }}"
                                                method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="dashed-border-container p-4 mt-4 mb-4"
                                                    style="border: 2px dashed #ddd; border-radius: 8px;">
                                                    <div class="form-group col-md-12 mb-3">
                                                        <div class="border p-4 text-center"
                                                            style="background-color: #f8f9fa;">
                                                            <div
                                                                class="d-flex justify-content-center align-items-center mb-3">
                                                                <i class="fas fa-cloud-upload-alt fa-2x text-muted"></i>
                                                            </div>
                                                            <p>Choose a file or drag & drop it here</p>
                                                            <p class="text-muted">>Format
                                                                pdf,doc,docx,xlsx,jpg,jpeg,png,csv
                                                                , ukuran file
                                                                5MB</p>
                                                            <input type="file" name="upload_hasil"
                                                                id="fileImplementasiHard_{{ $kom->id_idpKom }}"
                                                                style="display: none;"
                                                                onchange="displaySelectedFile(this, 'fileNameHard_{{ $kom->id_idpKom }}','fileErrorHard_{{ $kom->id_idpKom }}')">
                                                            <button type="button" class="btn btn-outline-secondary"
                                                                onclick="document.getElementById('fileImplementasiHard_{{ $kom->id_idpKom }}').click()">
                                                                Browse File
                                                            </button>
                                                            <div id="fileNameHard_{{ $kom->id_idpKom }}"
                                                                class="mt-2 text-primary" style="display: none;"></div>
                                                            <div id="fileErrorHard_{{ $kom->id_idpKom }}"
                                                                class="mt-2 text-danger" style="display: none;"></div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group col-md-12 mb-3">
                                                        <label
                                                            for="keterangan_hasil_implementasi_hard_{{ $kom->id_idpKom }}">Keterangan</label>
                                                        <textarea class="form-control" id="keterangan_hasil_implementasi_hard_{{ $kom->id_idpKom }}" style="height:6rem;"
                                                            name="keterangan_hasil" rows="3" placeholder="Ketikkan pencapaian atau hasil implementasi..."></textarea>
                                                    </div>

                                                    <div class="d-flex justify-content-end mt-4">
                                                        <button type="submit" class="btn btn-primary me-2">
                                                            <i class="fas fa-save me-1"></i> Simpan
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
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
                                                                @php
                                                                    $isRevisi = in_array($peng->status_pengerjaan, [
                                                                        'Ditolak Mentor',
                                                                        'Revisi Mentor',
                                                                    ]);
                                                                    $statusColors = [
                                                                        'Menunggu Persetujuan' => [
                                                                            'bg' => '#d1fae5',
                                                                            'text' => '#065f46',
                                                                        ],
                                                                        'Disetujui Mentor' => [
                                                                            'bg' => '#bfdbfe',
                                                                            'text' => '#1e3a8a',
                                                                        ],
                                                                        'Ditolak Mentor' => [
                                                                            'bg' => '#fecaca',
                                                                            'text' => '#991b1b',
                                                                        ],
                                                                        'Revisi Mentor' => [
                                                                            'bg' => '#fef3c7',
                                                                            'text' => '#92400e',
                                                                        ],
                                                                    ];
                                                                    $bgColor =
                                                                        $statusColors[$peng->status_pengerjaan]['bg'] ??
                                                                        '#e5e7eb';
                                                                    $textColor =
                                                                        $statusColors[$peng->status_pengerjaan][
                                                                            'text'
                                                                        ] ?? '#374151';
                                                                @endphp

                                                                @if ($isRevisi)
                                                                    <button
                                                                        class="btn btn-sm d-flex align-items-center justify-content-center mx-auto"
                                                                        style="background-color: {{ $bgColor }}; color: {{ $textColor }}; border: 1px solid {{ $textColor }};"
                                                                        data-toggle="modal"
                                                                        data-target="#uploadUlangModal{{ $peng->id }}">
                                                                        <i class="bi bi-upload mr-1"></i>
                                                                        {{ $peng->status_pengerjaan }}
                                                                    </button>
                                                                @else
                                                                    <span
                                                                        style="background-color: {{ $bgColor }}; color: {{ $textColor }}; padding: 4px 10px; border-radius: 9999px;">
                                                                        {{ $peng->status_pengerjaan }}
                                                                    </span>
                                                                @endif

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
                        <a class="btn btn-warning" href="{{ route('karyawan.IDP.indexKaryawan') }}">Kembali</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="modal fade" id="uploadUlangModal{{ $peng->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg"> {{-- modal-lg agar lebar --}}
            <form method="POST" action="#" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Ulang Implementasi</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        {{-- Keterangan dan Saran dari Mentor --}}
                        <div class="alert" style="background-color: #d1fae5; color: #065f46;">
                            <strong>Keterangan Sebelumnya:</strong><br>
                            {{ $peng->keterangan_hasil ?? '-' }}<br><br>

                            <strong>Saran Mentor:</strong><br>
                            {{ $peng->saran ?? 'Tidak ada saran.' }}
                        </div>

                        {{-- Form Upload Baru --}}
                        <div class="form-group">
                            <label>Upload File Baru <span class="text-danger">*</span></label>
                            <input type="file" name="upload_hasil" class="form-control" required>
                            <small class="form-text text-muted">
                                Format yang diizinkan: <strong>pdf, doc, docx, xlsx, jpg, jpeg, png, csv</strong> | Ukuran
                                maksimal: <strong>5MB</strong>
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Keterangan Baru <span class="text-danger">*</span></label>
                            <textarea name="keterangan_hasil" class="form-control" style="height:6rem;" required>{{ old('keterangan_hasil', $peng->keterangan_hasil) }}</textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Kirim Ulang</button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
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

        function displaySelectedFile(input, displayId, errorId) {
            const displayElement = document.getElementById(displayId);
            const errorElement = document.getElementById(errorId);

            // Reset pesan error & nama file
            displayElement.style.display = 'none';
            errorElement.style.display = 'none';
            errorElement.textContent = '';

            if (input.files.length > 0) {
                const file = input.files[0];
                const fileSizeMB = file.size / 1024 / 1024;
                const fileSizeText = fileSizeMB.toFixed(2) + ' MB';

                // Allowed MIME types
                const allowedTypes = [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'image/jpeg',
                    'image/png',
                    'image/jpg',
                    'text/csv'
                ];

                if (!allowedTypes.includes(file.type)) {
                    errorElement.textContent =
                        'Format file tidak sesuai. Hanya pdf, doc, docx, xlsx, jpg, jpeg, png, csv yang diperbolehkan.';
                    errorElement.style.display = 'block';
                    errorElement.classList.remove('text-primary');
                    errorElement.classList.add('text-danger');
                    displayElement.style.display = 'none';
                    return;
                }

                if (fileSizeMB > 5) {
                    errorElement.textContent = `Ukuran file terlalu besar (${fileSizeText}). Maksimal 5MB.`;
                    errorElement.style.display = 'block';
                    errorElement.classList.remove('text-primary');
                    errorElement.classList.add('text-danger');
                    displayElement.style.display = 'none';
                    return;
                }

                // Jika validasi lolos, tampilkan nama file + ukuran dengan warna biru (text-primary)
                displayElement.innerHTML = `
            <div class="d-flex align-items-center justify-content-center">
                <i class="fas fa-file me-2"></i>
                <span>${file.name} (${fileSizeText})</span>
            </div>
        `;
                displayElement.style.display = 'block';

                // Reset error jika ada sebelumnya
                errorElement.textContent = '';
                errorElement.style.display = 'none';
            }
        }


        // Show success message if upload successful
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        // Show error message if upload failed
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
        $(document).on('hidden.bs.modal', function (event) {
        var modal = $(event.target);
        
        // Kosongkan semua input file
        modal.find('input[type="file"]').val('');

        // Kosongkan semua textarea yang memiliki required
        modal.find('textarea[required]').val('');
    });
    </script>
@endpush
