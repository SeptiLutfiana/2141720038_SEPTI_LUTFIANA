@extends('layouts.app')

@section('title', 'Dashboard Karyawan')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Dashboard Karyawan</h1>
            </div>
            <div class="section-body">
            </div>
            @include('components.alert') {{-- Tampilkan notifikasi jika ada --}}
            <div class="alert border border-warning shadow-sm d-flex align-items-center" role="alert">
                <i class="fas fa-smile-beam text-warning fa-lg mr-3"></i>
                <div class="text-dark">
                    <h5 class="mb-1 font-weight-bold">Hai, {{ Auth::user()->name }} 👋</h5>
                    <small>Selamat datang kembali di Dashboard Karyawan</small>
                </div>
            </div>
            @if ($evaluasiOnboarding)
                <div class="alert alert-light border border-info shadow-sm d-flex align-items-center">
                    <i class="fas fa-info-circle fa-lg text-info mr-3"></i>
                    <div>
                        <strong>Evaluasi Onboarding Tersedia!</strong><br>
                        Evaluasi IDP Anda telah dikirim oleh mentor.<br>
                        <a href="{{ route('karyawan.EvaluasiIdp.EvaluasiOnboarding.detail', $evaluasiOnboarding->id_idp) }}"
                            class="btn btn-sm btn-outline-primary mt-2">
                            Lihat Evaluasi
                        </a>
                    </div>
                </div>
            @endif

            {{-- Progres Behavior IDP --}}
            <div class="row">
                {{-- KIRI: Informasi Bank IDP --}}
                <div class="col-md-8 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-chart-line text-primary mr-2"></i>
                            <h4 class="mb-0">Progres Behavior IDP</h4>
                        </div>
                        <hr class="m-0">
                        <div class="card-body">
                            <div class="row">
                                @php
                                    $dataProgres = [
                                        [
                                            'title' => 'Individual Development Plan',
                                            'count' => $jumlahIDPGiven,
                                            'icon' => 'fa-user',
                                            'bg' => 'bg-primary',
                                            'border' => 'border-primary',
                                        ],
                                        [
                                            'title' => 'IDP Menunggu Persetujuan Supervisor',
                                            'count' => $jumlahRekomendasiBelumMuncul,
                                            'icon' => 'fa-hourglass-half',
                                            'bg' => 'bg-warning',
                                            'border' => 'border-warning',
                                        ],
                                    ];
                                @endphp
                                @foreach ($dataProgres as $item)
                                    <div class="col-12 mb-3">
                                        <div class="card card-statistic-1 {{ $item['border'] }}">
                                            <div class="card-icon {{ $item['bg'] }}">
                                                <i class="fas {{ $item['icon'] }}"></i>
                                            </div>
                                            <div class="card-wrap">
                                                <div class="card-header">
                                                    <h4>{{ $item['title'] }}</h4>
                                                </div>
                                                <div class="card-body">
                                                    {{ $item['count'] }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-book text-primary mr-2"></i>
                            <h4 class="mb-0 font-weight-bold">Total Evaluasi Pasca IDP</h4>
                        </div>
                        <hr class="m-0">
                        <div class="card-body">

                            <div class="card card-statistic-1 border-info text-center py-4">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                    <div class="card-icon bg-info mb-3"
                                        style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                        <i class="fas fa-chart-bar text-white fa-lg"></i>
                                    </div>
                                    <div class="card-wrap">
                                        <div class="card-header">
                                            <h4>Total Evaluasi Belum Dikerjakan</h4>
                                        </div>
                                        <div class="card-body p-0">
                                            {{ $totalBelumEvaluasiPasca }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-clipboard-list text-primary mr-2"></i>
                    <h4 class="mb-0">Status Perencanaan IDP</h4>
                </div>
                <hr class="m-0">
                <div class="card-body">
                    <div class="row">
                        @php
                            $dataProgres = [
                                [
                                    'title' => 'Persetujuan Mentor',
                                    'count' => $jumlahMenungguPersetujuan,
                                    'icon' => 'fa-book', // ikon buku atau dokumen
                                    'bg' => 'bg-info', // biru muda, kesan informatif
                                    'border' => 'border-info',
                                ],
                                [
                                    'title' => 'Pengajuan IDP Revisi',
                                    'count' => $jumlahIDPRevisi,
                                    'icon' => 'fa-edit',
                                    'bg' => 'bg-warning',
                                    'border' => 'border-warning',
                                ],
                                [
                                    'title' => 'Pengajuan IDP Tidak Disetujui',
                                    'count' => $jumlahIdpTidakDisetujui,
                                    'icon' => 'fa-times-circle',
                                    'bg' => 'bg-danger',
                                    'border' => 'border-danger',
                                ],
                            ];
                        @endphp
                        @foreach ($dataProgres as $item)
                            <div class="col-lg-4 col-md-6 col-sm-6 col-12 mb-3">
                                <div class="card card-statistic-1 {{ $item['border'] }}">
                                    <div class="card-icon {{ $item['bg'] }}">
                                        <i class="fas {{ $item['icon'] }}"></i>
                                    </div>
                                    <div class="card-wrap">
                                        <div class="card-header">
                                            <h4>{{ $item['title'] }}</h4>
                                        </div>
                                        <div class="card-body">
                                            {{ $item['count'] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-history text-primary mr-2"></i>
                    <h4 class="mb-0">Riwayat Perencanaan IDP</h4>
                </div>
                <hr class="m-0">
                <div class="card-body">
                    <div class="row">
                        @php
                            $dataProgres = [
                                [
                                    'title' => 'Disarankan',
                                    'count' => $jumlahDisarankan,
                                    'icon' => 'fa-check',
                                    'bg' => 'bg-success',
                                    'border' => 'border-success',
                                ],
                                [
                                    'title' => 'Disarankan Dengan Pengembangan',
                                    'count' => $jumlahDisarankanDenganPengembangan,
                                    'icon' => 'fa-tools',
                                    'bg' => 'bg-secondary',
                                    'border' => 'border-secondary',
                                ],
                                [
                                    'title' => 'Tidak Disarankan',
                                    'count' => $jumlahTidakDisarankan,
                                    'icon' => 'fa-ban',
                                    'bg' => 'bg-dark',
                                    'border' => 'border-dark',
                                ],
                            ];
                        @endphp
                        @foreach ($dataProgres as $item)
                            <div class="col-lg-4 col-md-6 col-sm-6 col-12 mb-3">
                                <div class="card card-statistic-1 {{ $item['border'] }}">
                                    <div class="card-icon {{ $item['bg'] }}">
                                        <i class="fas {{ $item['icon'] }}"></i>
                                    </div>
                                    <div class="card-wrap">
                                        <div class="card-header">
                                            <h4>{{ $item['title'] }}</h4>
                                        </div>
                                        <div class="card-body">
                                            {{ $item['count'] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-bar text-primary mr-2"></i>
                    <h4>Grafik Nilai Perencanaan Individual Development Plan (Hard vs Soft)</h4>
                </div>
                <div class="card-body">
                    <canvas id="chartKaryawan" height="100"></canvas>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-award text-primary mr-2"></i>
                    <h4>Top 5 Perencanaan IDP (Hasil Rekomendasi: Disarankan)</h4>
                </div>
                <div class="card-body">
                    @if ($topKaryawan->isEmpty())
                        <p>Tidak ada data yang memenuhi kriteria.</p>
                    @else
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nama Karyawan</th>
                                    <th>Proyeksi Karir</th>
                                    <th>Nilai Soft</th>
                                    <th>Nilai Hard</th>
                                    <th>Hasil Rekomendasi</th>
                                    <th>Progres IDP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topKaryawan as $rek)
                                    @php
                                        $idp = $rek->idp;
                                        $idpKompetensis = $idp->idpKompetensis;
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

                                        $persen =
                                            $totalKompetensi > 0 ? round(($jumlahSelesai / $totalKompetensi) * 100) : 0;

                                        $warna = 'bg-danger';
                                        if ($persen >= 80) {
                                            $warna = 'bg-success';
                                        } elseif ($persen >= 50) {
                                            $warna = 'bg-warning';
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $idp->karyawan->name ?? 'Tidak diketahui' }}</td>
                                        <td>{{ $idp->proyeksi_karir ?? '-' }}</td>
                                        <td>{{ $rek->nilai_akhir_soft }}</td>
                                        <td>{{ $rek->nilai_akhir_hard }}</td>
                                        <td>{{ $rek->hasil_rekomendasi }}</td>
                                        <td>
                                            <div style="font-size: 10px;" class="text-muted mb-1">
                                                {{ $jumlahSelesai }}/{{ $totalKompetensi }} |
                                                {{ $persen }}%
                                            </div>
                                            <div class="progress" style="height: 6px; border-radius: 999px;">
                                                <div class="progress-bar {{ $warna }}" role="progressbar"
                                                    style="width: {{ $persen }}%; border-radius: 999px;"
                                                    aria-valuenow="{{ $persen }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/page/index-0.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Inject dataPoints dari PHP ke JS
        const dataPoints = @json($dataPoints);

        const ctx = document.getElementById('chartKaryawan').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'Karyawan',
                    data: dataPoints,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Nilai Kompetensi Hard vs Soft Karyawan'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const point = context.raw;
                                return `${point.label}\nHard: ${point.x}, Soft: ${point.y}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Nilai Hard'
                        },
                        min: 0,
                        max: 5
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Nilai Soft'
                        },
                        min: 0,
                        max: 5
                    }
                }
            }
        });
    </script>
@endpush
