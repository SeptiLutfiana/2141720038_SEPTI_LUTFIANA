@extends('layouts.app')

@section('title', 'Detail IDP Karyawan')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Detail IDP Karyawan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('karyawan.dashboard-karyawan') }}">Dashboard</a>
                    </div>
                    <div class="breadcrumb-item"><a href="{{ route('karyawan.IDP.RiwayatIDP.indexRiwayatIdp') }}">Data
                            IDP</a></div>
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
                            {{ $idps->angkatanpsp->bulan ?? '-' }} {{ $idps->angkatanpsp->tahun ?? '-' }}
                        </small>
                        <br>
                        <table class="table table-bordered">
                            {{-- <tr class="text-center">
                                <th style="width: 250px;">Indikator</th>
                                <th colspan="4">Keterangan</th>
                            </tr> --}}
                            <tr>
                                <td style="width: 180px;">Proyeksi Karir</td>
                                <td colspan="4">{{ $idps->proyeksi_karir }}</td>
                            </tr>
                            <tr>
                                <td>Deskripsi IDP</td>
                                <td colspan="4">{{ $idps->deskripsi_idp }}</td>
                            </tr>
                            <tr>
                                <td>Mentor</td>
                                <td colspan="4">{{ $idps->mentor->name }}</td>
                            </tr>
                            <tr>
                                <td>Supervisor</td>
                                <td colspan="4">{{ $idps->supervisor->name }}</td>
                            </tr>
                            <tr>
                                <td>Waktu Mulai</td>
                                <td colspan="4">{{ \Carbon\Carbon::parse($idps->waktu_mulai)->format('d-m-Y') }}</td>
                            </tr>
                            <tr>
                                <td>Waktu Selesai</td>
                                <td colspan="4">{{ \Carbon\Carbon::parse($idps->waktu_selesai)->format('d-m-Y') }}</td>
                            </tr>
                            <tr>
                                <td>Hasil Rekomendasi</td>
                                <td colspan="4">
                                    <div style="font-weight: bold;">
                                        {{ $idps->rekomendasis->first()->hasil_rekomendasi }}
                                    </div>
                                    <div>
                                        {{ $idps->rekomendasis->first()->deskripsi_rekomendasi }}
                                    </div>
                                </td>
                            </tr>
                            <tr class="text-center">
                                <th colspan="4" style="background-color: #1d6c0e; color: #fff; font-weight: bold;">
                                    Development Area</th>
                            </tr>
                            <tr>
                                <th colspan="4" style="background-color: #a6c73a; color: #fff; font-weight: bold;">Soft
                                    Kompetensi</th>
                            </tr>
                            @foreach ($idps->idpKompetensis->where('kompetensi.jenis_kompetensi', 'Soft Kompetensi') as $kom)
                                <tr>
                                    <th colspan="4">{{ $kom->kompetensi->nama_kompetensi }}</th>
                                </tr>
                                <tr>
                                    <td colspan="4">{{ $kom->kompetensi->keterangan }}</td>
                                </tr>
                                <tr>
                                    <td>Sasaran</td>
                                    <td colspan="4">{!! nl2br(e($kom->sasaran)) !!}</td>
                                </tr>
                                <tr>
                                    <td>Metode Belajar</td>
                                    <td colspan="4">
                                        {{ $kom->metodeBelajars->pluck('nama_metodeBelajar')->implode(', ') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Aksi</td>
                                    <td colspan="3">{!! nl2br(e($kom->aksi)) !!}</td>
                                </tr>
                                <tr>
                                    <th colspan="4">Aksi Implementasi</th>
                                </tr>
                                <tr class="text-center">
                                    <th>File</th>
                                    <th colspan="2">Keterangan</th>
                                    <th style="width: 250px;">Rating</th>
                                </tr>
                                @foreach ($kom->pengerjaans as $index => $peng)
                                    @php
                                        $ext = strtolower(pathinfo($peng->upload_hasil, PATHINFO_EXTENSION));
                                        $icon = match ($ext) {
                                            'pdf' => 'bi bi-file-earmark-pdf-fill text-danger',
                                            'doc', 'docx' => 'bi bi-file-earmark-word-fill text-primary',
                                            'xls', 'xlsx' => 'bi bi-file-earmark-excel-fill text-success',
                                            'jpg', 'jpeg', 'png' => 'bi bi-file-earmark-image-fill text-warning',
                                            'mp4' => 'bi bi-file-earmark-play-fill text-dark',
                                            default => 'bi bi-file-earmark-fill',
                                        };
                                        $fileUrl = asset('storage/' . $peng->upload_hasil);
                                        $isPreviewable = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'mp4']);
                                    @endphp
                                    <tr>
                                        <td class="text-center" style="width:20px;">
                                            @if ($isPreviewable)
                                                {{-- File bisa dibuka langsung --}}
                                                <a href="{{ $fileUrl }}" target="_blank" title="Lihat file">
                                                    <i class="{{ $icon }}" style="font-size: 1.5rem;"></i>
                                                </a>
                                            @else
                                                {{-- File harus didownload --}}
                                                <a href="{{ $fileUrl }}" download title="Download file">
                                                    <i class="{{ $icon }}" style="font-size: 1.5rem;"></i>
                                                </a>
                                            @endif
                                        </td>
                                        <td colspan="2" class="text-left">{{ $peng->keterangan_hasil }}</td>
                                        <td class="text-center">{{ $peng->nilaiPengerjaanIdp->rating }}</td>
                                @endforeach
                                </tr>
                            @endforeach
                            <tr>
                                <th colspan="3" style="background-color: #688509; color: #fff; font-weight: bold;">Nilai
                                    Soft Kompetensi</th>
                                <th class="text-center" style="background-color: #688509; color: #fff; font-weight: bold;">
                                    {{ $idps->rekomendasis->first()->nilai_akhir_soft }}</th>
                            </tr>
                            <tr>
                                <th colspan="4" style="background-color: #a6c73a; color: #fff; font-weight: bold;">Hard
                                    Kompetensi</th>
                            </tr>
                            @foreach ($idps->idpKompetensis->where('kompetensi.jenis_kompetensi', 'Hard Kompetensi') as $kom)
                                <tr>
                                    <th colspan="4">{{ $kom->kompetensi->nama_kompetensi }}</th>
                                </tr>
                                <tr>
                                    <td colspan="4">{{ $kom->kompetensi->keterangan }}</td>
                                </tr>
                                <tr>
                                    <td>Sasaran</td>
                                    <td colspan="4">{!! nl2br(e($kom->sasaran)) !!}</td>
                                </tr>
                                <tr>
                                    <td>Metode Belajar</td>
                                    <td colspan="4">
                                        {{ $kom->metodeBelajars->pluck('nama_metodeBelajar')->implode(', ') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Aksi</td>
                                    <td colspan="3">{!! nl2br(e($kom->aksi)) !!}</td>
                                </tr>
                                <tr>
                                    <th colspan="4">Aksi Implementasi</th>
                                </tr>
                                <tr class="text-center">
                                    <th>File</th>
                                    <th colspan="2">Keterangan</th>
                                    <th style="width: 250px;">Rating</th>
                                </tr>
                                @foreach ($kom->pengerjaans as $index => $peng)
                                    @php
                                        $ext = strtolower(pathinfo($peng->upload_hasil, PATHINFO_EXTENSION));
                                        $icon = match ($ext) {
                                            'pdf' => 'bi bi-file-earmark-pdf-fill text-danger',
                                            'doc', 'docx' => 'bi bi-file-earmark-word-fill text-primary',
                                            'xls', 'xlsx' => 'bi bi-file-earmark-excel-fill text-success',
                                            'jpg', 'jpeg', 'png' => 'bi bi-file-earmark-image-fill text-warning',
                                            'mp4' => 'bi bi-file-earmark-play-fill text-dark',
                                            default => 'bi bi-file-earmark-fill',
                                        };
                                        $fileUrl = asset('storage/' . $peng->upload_hasil);
                                        $isPreviewable = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'mp4']);
                                    @endphp
                                    <tr>
                                        <td class="text-center" style="width:20px;">
                                            @if ($isPreviewable)
                                                {{-- File bisa dibuka langsung --}}
                                                <a href="{{ $fileUrl }}" target="_blank" title="Lihat file">
                                                    <i class="{{ $icon }}" style="font-size: 1.5rem;"></i>
                                                </a>
                                            @else
                                                {{-- File harus didownload --}}
                                                <a href="{{ $fileUrl }}" download title="Download file">
                                                    <i class="{{ $icon }}" style="font-size: 1.5rem;"></i>
                                                </a>
                                            @endif
                                        </td>
                                        <td colspan="2" class="text-left">{{ $peng->keterangan_hasil }}</td>
                                        <td class="text-center">{{ $peng->nilaiPengerjaanIdp->rating }}</td>
                                @endforeach
                                </tr>
                            @endforeach
                            <tr>
                                <th colspan="3" style="background-color: #688509; color: #fff; font-weight: bold;">Nilai
                                    Hard Kompetensi</th>
                                <th class="text-center" style="background-color: #688509; color: #fff; font-weight: bold;">
                                    {{ $idps->rekomendasis->first()->nilai_akhir_hard }}</th>
                            </tr>
                        </table>

                    </div>
                    <div class="card-footer text-right">
                        <a class="btn btn-primary"
                            href="{{ route('karyawan.IDP.RiwayatIDP.indexRiwayatIdp') }}">Kembali</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
