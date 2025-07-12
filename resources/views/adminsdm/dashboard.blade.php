@extends('layouts.app')

@section('title', 'Dashboard Admin')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Dashboard Admin</h1>
            </div>
            <div class="section-body">
            </div>
            @include('components.alert') {{-- Tampilkan notifikasi jika ada --}}
            <div class="alert alert-light border border-info shadow-sm d-flex align-items-center" role="alert">
                <i class="fas fa-smile-beam text-info fa-lg mr-3"></i>
                <div>
                    <h5 class="mb-1 font-weight-bold">Hai, {{ Auth::user()->name }} 👋</h5>
                    <small>Selamat datang kembali di Dashboard Administrator. Terima kasih telah menjaga sistem tetap
                        berjalan baik!</small>
                </div>
            </div>
            <div style="position: relative;">
                <form method="GET" action="{{ route('adminsdm.dashboard') }}" class="form-inline mb-4">
                    <label for="filterTahun" class="mr-2 font-weight-bold">Tahun Periode:</label>
                    <select name="tahun" id="filterTahun" class="form-control mr-2" style="width: 100px;"
                        onchange="this.form.submit()">
                        @foreach ($listTahun as $th)
                            <option value="{{ $th }}"
                                {{ request('tahun', $tahunDipilih) == $th ? 'selected' : '' }}>
                                {{ $th }}
                            </option>
                        @endforeach
                    </select>
                    <noscript><button type="submit" class="btn btn-primary">Terapkan</button></noscript>
                </form>
            </div>
            {{-- Total Bank IDP --}}
            <div class="row">
                {{-- KIRI: Informasi Bank IDP --}}
                <div class="col-md-8 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-chart-line text-primary mr-2"></i>
                            <h4 class="mb-0 font-weight-bold">Informasi Mapping IDP</h4>
                        </div>
                        <hr class="m-0">
                        <div class="card-body">
                            <div class="row">
                                @php
                                    $dataProgres = [
                                        [
                                            'title' => 'Total Mapping IDP',
                                            'count' => $jumlahIDPBank,
                                            'icon' => 'fa-database',
                                            'bg' => 'bg-danger',
                                            'border' => 'border-danger',
                                        ],
                                        [
                                            'title' => 'Total Apply Mapping IDP',
                                            'count' => $jumlahApplyBankIdp,
                                            'icon' => 'fa-copy',
                                            'bg' => 'bg-info',
                                            'border' => 'border-info',
                                        ],
                                    ];
                                @endphp
                                @foreach ($dataProgres as $item)
                                    <div class="col-md-6 col-sm-6 col-12 mb-3">
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

                {{-- KANAN: Panduan IDP --}}
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-book text-primary mr-2"></i>
                            <h4 class="mb-0 font-weight-bold">Panduan IDP</h4>
                        </div>
                        <hr class="m-0">
                        <div class="card-body">

                            <div class="col-md-12 col-sm-6 col-12 mb-4">
                                <div class="card card-statistic-1 border-info">
                                    <div class="card-icon bg-warning">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div class="card-wrap">
                                        <div class="card-header">
                                            <h4>Panduan IDP</h4>
                                        </div>
                                        <div class="card-body">
                                            {{ $totalPanduan }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Progres Behavior IDP --}}
            <div class="row">
                {{-- KIRI: Informasi Bank IDP --}}
                <div class="col-md-8 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-chart-line text-primary mr-2"></i>
                            <h4 class="mb-0 font-weight-bold">Progres Behavior IDP</h4>
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
                                            'title' => 'IDP Menunggu Persetujuan',
                                            'count' => $jumlahRekomendasiBelumMuncul,
                                            'icon' => 'fa-hourglass-half',
                                            'bg' => 'bg-warning',
                                            'border' => 'border-warning',
                                        ],
                                    ];
                                @endphp
                                @foreach ($dataProgres as $item)
                                    <div class="col-md-6 col-sm-6 col-12 mb-3">
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

                {{-- KANAN: Panduan IDP --}}
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-book text-primary mr-2"></i>
                            <h4 class="mb-0 font-weight-bold">Total Evaluasi Pasca IDP</h4>
                        </div>
                        <hr class="m-0">
                        <div class="card-body">

                            <div class="col-md-12 col-sm-6 col-12 mb-4">
                                <div class="card card-statistic-1 border-info">
                                    <div class="card-icon bg-info">
                                        <i class="fas fa-chart-bar"></i>
                                    </div>
                                    <div class="card-wrap">
                                        <div class="card-header">
                                            <h4>Total Evaluasi Pasca IDP</h4>
                                        </div>
                                        <div class="card-body">
                                            {{ $totalEvaluasiPasca }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Progres Behavior IDP --}}
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-chart-line text-primary mr-2"></i>
                    <h4 class="mb-0 font-weight-bold">Riwayat Perencanaan IDP</h4>
                </div>
                <hr class="m-0">
                <div class="card-body">
                    <div class="row">
                        @php
                            $dataProgres = [
                                [
                                    'title' => 'IDP Disarankan',
                                    'count' => $jumlahDisarankan,
                                    'icon' => 'fa-check',
                                    'bg' => 'bg-success',
                                    'border' => 'border-success',
                                ],
                                [
                                    'title' => 'IDP Disarankan Dengan Pengembangan',
                                    'count' => $jumlahDisarankanDenganPengembangan,
                                    'icon' => 'fa-tools',
                                    'bg' => 'bg-secondary',
                                    'border' => 'border-secondary',
                                ],
                                [
                                    'title' => 'IDP Tidak Disarankan',
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

            {{-- Data Master --}}
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-chart-line text-primary mr-2"></i>
                    <h4 class="mb-0 font-weight-bold">Data Master</h4>
                </div>
                <hr class="m-0">
                <div class="card-body">
                    <div class="row">
                        @php
                            $dataMaster = [
                                [
                                    'title' => 'Total Karyawan',
                                    'count' => $jumlahKaryawan . ' Karyawan',
                                    'icon' => 'fa-users',
                                    'bg' => 'bg-primary',
                                    'border' => 'border-primary',
                                ],
                                [
                                    'title' => 'Jumlah Supervisor',
                                    'count' => $jumlahSpv . ' Supervisor',
                                    'icon' => 'fa-user-shield',
                                    'bg' => 'bg-danger',
                                    'border' => 'border-danger',
                                ],
                                [
                                    'title' => 'Jumlah Mentor',
                                    'count' => $jumlahMentor . ' Mentor',
                                    'icon' => 'fa-user-graduate',
                                    'bg' => 'bg-info',
                                    'border' => 'border-info',
                                ],
                            ];
                        @endphp
                        @foreach ($dataMaster as $item)
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
            <div class="row">
                {{-- Chart IDP per Jenjang --}}
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-layer-group text-primary mr-2"></i>
                            <h4 class="mb-0 font-weight-bold">Jenjang</h4>
                        </div>
                        <hr class="m-0">
                        <div class="card-body">
                            <div style="position: relative; height: 300px;">
                                <canvas id="chartJenjang"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Chart IDP per Learning Group --}}
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-layer-group text-primary mr-2"></i>
                            <h4 class="mb-0 font-weight-bold">Direktorat</h4>
                        </div>
                        <hr class="m-0">
                        <div class="card-body">
                            <div style="position: relative; height: 300px;">
                                <canvas id="chartLG"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="font-weight-bold">Grafik Nilai Karyawan (Hard vs Soft)</h4>
                </div>
                <div style="position: relative; height: 500px;">
                    <canvas id="chartKaryawan"></canvas>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="font-weight-bold">Top 5 Karyawan (Hasil Rekomendasi: Disarankan)</h4>
                </div>
                <div class="card-body">
                    @if ($topKaryawan->isEmpty())
                        <p class="text-center text-muted">Tidak ada data yang memenuhi kriteria.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-light">
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
                                                $totalKompetensi > 0
                                                    ? round(($jumlahSelesai / $totalKompetensi) * 100)
                                                    : 0;

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
                                                    {{ $jumlahSelesai }}/{{ $totalKompetensi }} | {{ $persen }}%
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
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil data dari PHP
            const jenjangLabels = @json($jenjangLabels ?? []);
            const jenjangTotals = @json($jenjangTotals ?? []);

            // Debug di console
            console.log('Labels:', jenjangLabels);
            console.log('Totals:', jenjangTotals);

            // Cek apakah data ada
            if (!jenjangLabels || !jenjangTotals || jenjangLabels.length === 0) {
                console.error('Data chart kosong atau tidak valid');
                document.getElementById('chartJenjang').parentElement.innerHTML =
                    '<p class="text-center text-muted">Tidak ada data untuk ditampilkan</p>';
                return;
            }

            // Buat chart
            const ctxJenjang = document.getElementById('chartJenjang').getContext('2d');
            const chartJenjang = new Chart(ctxJenjang, {
                type: 'doughnut',
                data: {
                    labels: jenjangLabels,
                    datasets: [{
                        label: 'Jenjang',
                        data: jenjangTotals,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 205, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(153, 102, 255, 0.6)',
                            'rgba(255, 159, 64, 0.6)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 205, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Jenjang'
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {}
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil data dari PHP
            const LGLabels = @json($LGLabels ?? []);
            const LGTotals = @json($LGTotals ?? []);

            // Debug di console
            console.log('Labels:', LGLabels);
            console.log('Totals:', LGTotals);

            // Cek apakah data ada
            if (!LGLabels || !LGTotals || LGLabels.length === 0) {
                console.error('Data chart kosong atau tidak valid');
                document.getElementById('chartLG').parentElement.innerHTML =
                    '<p class="text-center text-muted">Tidak ada data untuk ditampilkan</p>';
                return;
            }

            // Buat chart
            const ctxJenjang = document.getElementById('chartLG').getContext('2d');
            const chartJenjang = new Chart(ctxJenjang, {
                type: 'doughnut',
                data: {
                    labels: LGLabels,
                    datasets: [{
                        label: 'Direktorat',
                        data: LGTotals,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 205, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(153, 102, 255, 0.6)',
                            'rgba(255, 159, 64, 0.6)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 205, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Direktorat'
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {}
                }
            });
        });
        const dataPoints = {!! json_encode($dataPoints) !!};

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
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const point = context.raw;
                                return `${point.label}\nHard: ${point.x}, Soft: ${point.y}`;
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush
