@extends('layouts.app')

@section('title', 'Isi Evaluasi Onboarding Mentor')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Evaluasi Onboarding Sebagai Mentor</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active">
                        <a href="{{ route('mentor.dashboard-mentor') }}">Dashboard</a>
                    </div>
                    <div class="breadcrumb-item active">
                        <a href="{{ route('mentor.EvaluasiIdp.EvaluasiOnBording.indexMentor') }}">Evaluasi Onboarding</a>
                    </div>
                    <div class="breadcrumb-item">Isi Evaluasi</div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>Catatan Evaluasi Onboarding IDP</h4>
                </div>
                @php
                    $idp = \App\Models\IDP::with('user', 'supervisor', 'idpKompetensis.pengerjaans')->find($id_idp);
                    $karyawan = $idp->user;
                    $supervisor = $idp->supervisor;
                    $totalKompetensi = $idp->idpKompetensis->count();
                    $jumlahSelesai = 0;

                    foreach ($idp->idpKompetensis as $kom) {
                        $totalUpload = $kom->pengerjaans->count();
                        $jumlahDisetujui = $kom->pengerjaans->where('status_pengerjaan', 'Disetujui Mentor')->count();

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
                    $totalHari = \Carbon\Carbon::parse($idp->waktu_mulai)->diffInDays($idp->waktu_selesai);
                    $hariBerjalan = \Carbon\Carbon::parse($idp->waktu_mulai)->diffInDays(now());
                    $sisaHari = \Carbon\Carbon::parse(now())->diffInDays($idp->waktu_selesai, false);
                    $mingguBerjalan = floor($hariBerjalan / 7);
                    $mingguTotal = ceil($totalHari / 7);
                    $mingguSisa = max(0, ceil($sisaHari / 7));

                    // Target minimal default 50%
                    $estimasiTarget = 50;
                    $targetNote = 'Target Minimal Saat Ini (karena waktu sudah setengah jalan)';

                    // Jika sudah H-1 atau kurang, target minimal naik ke 80%
                    if ($sisaHari <= 1) {
                        $estimasiTarget = 80;
                        $targetNote = 'Target Minimal Saat Ini (H-1 penutupan)';
                    }

                    $harusnyaSudah = $persen >= $estimasiTarget ? 'Ya' : 'Belum';

                @endphp

                <div class="card-body border-bottom">
                    <h6 class="mb-3"><strong>📋 Informasi IDP</strong></h6>
                    <ul class="list-group list-group-flush" style="font-size: 14px;">
                        <li class="list-group-item px-0"><i class="fas fa-user mr-2 text-primary"></i> <strong>Nama
                                Karyawan:</strong> {{ $karyawan->name }}</li>
                        <li class="list-group-item px-0"><i class="fas fa-user-tie mr-2 text-primary"></i>
                            <strong>Supervisor:</strong> {{ $supervisor->name }}
                        </li>
                        <li class="list-group-item px-0"><i class="fas fa-briefcase mr-2 text-primary"></i> <strong>Proyeksi
                                Karir:</strong> {{ $idp->proyeksi_karir }}</li>
                        <li class="list-group-item px-0"><i class="fas fa-calendar-alt mr-2 text-primary"></i>
                            <strong>Durasi:</strong>
                            {{ \Carbon\Carbon::parse($idp->waktu_mulai)->format('d M Y') }} –
                            {{ \Carbon\Carbon::parse($idp->waktu_selesai)->format('d M Y') }}
                        </li>
                    </ul>

                    <h6 class="mt-4 mb-2"><strong>🕒 Waktu & Target</strong></h6>
                    <div class="row" style="font-size: 13px;">
                        <div class="col-md-6 mb-2">
                            <span class="badge badge-info">Durasi: {{ $mingguTotal }} minggu ({{ $totalHari }}
                                hari)</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <span class="badge badge-secondary">Minggu Berjalan: {{ $mingguBerjalan }} /
                                {{ $mingguTotal }}</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <span class="badge badge-warning">Sisa: {{ $mingguSisa }} minggu ({{ intval($sisaHari) }}
                                hari)</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <span class="badge badge-light">
                                {{ $targetNote }}:
                                <strong>{{ $estimasiTarget }}%</strong>
                            </span>

                        </div>
                        <div class="col-md-6">
                            <span class="badge badge-{{ $harusnyaSudah === 'Ya' ? 'success' : 'danger' }}">
                                {{ $harusnyaSudah === 'Ya' ? 'Sudah Setengah Jalan' : 'Masih di bawah setengah' }}
                            </span>
                        </div>
                    </div>

                    @if ($harusnyaSudah === 'Ya' && $persen < $estimasiTarget)
                        <div class="alert alert-danger mt-3" role="alert">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Progres belum mencapai <strong>target minimal</strong> ({{ $estimasiTarget }}%) meskipun waktu
                            sudah setengah jalan.
                        </div>
                    @endif
                </div>

                <div class="card-body">
                    <h6 class="mb-2">📊 <strong>Progres Kompetensi</strong></h6>
                    <div class="mb-1 text-muted" style="font-size: 12px;">
                        <i class="fas fa-check-circle mr-1 text-success"></i>
                        {{ $jumlahSelesai }}/{{ $totalKompetensi }} Kompetensi Diselesaikan ({{ $persen }}%)
                    </div>
                    <div class="progress mb-3" style="height: 10px; border-radius: 999px;">
                        <div class="progress-bar {{ $warna }}" role="progressbar"
                            style="width: {{ $persen }}%; border-radius: 999px;" aria-valuenow="{{ $persen }}"
                            aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>

                    @if ($persen >= $estimasiTarget)
                        <div class="alert alert-success mt-3" role="alert" style="font-size: 14px;">
                            <i class="fas fa-check-circle mr-1"></i>
                            Progres <strong>sudah mencapai target minimal</strong> ({{ $estimasiTarget }}%). Good job!
                        </div>
                    @elseif ($persen < $estimasiTarget && $harusnyaSudah === 'Ya')
                        <div class="alert alert-danger mt-3" role="alert" style="font-size: 14px;">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Progres <strong>belum mencapai target minimal</strong> ({{ $estimasiTarget }}%). Mohon segera
                            ditingkatkan.
                        </div>
                    @endif
                </div>
                <form action="{{ route('mentor.EvaluasiIdp.EvaluasiOnBording.store') }}" method="POST">
                    @csrf
                    {{-- Data IDP dan User --}}
                    <input type="hidden" name="id_idp" value="{{ $id_idp }}">
                    <input type="hidden" name="id_user" value="{{ $id_user }}">
                    <input type="hidden" name="jenis_evaluasi" value="onboarding">

                    <div class="card-body">
                        <div class="form-group">
                            <label for="catatan">Catatan Evaluasi</label>
                            <textarea name="catatan" id="catatan" class="form-control" required
                                placeholder="Tulis catatan perkembangan, saran, atau evaluasi onboarding karyawan di sini..." style="height:6rem;"
                                rows="7"></textarea>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane mr-1"></i> Kirim Evaluasi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
