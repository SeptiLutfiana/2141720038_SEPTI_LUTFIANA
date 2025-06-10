@extends('layouts.app')

@section('title', 'Dashboard Mentor')

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
            {{-- Progres Behavior IDP --}}
            <div class="card">
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
                                [
                                    'title' => 'Persetujuan Mentor',
                                    'count' => $jumlahMenungguPersetujuan,
                                    'icon' => 'fa-book', // ikon buku atau dokumen
                                    'bg' => 'bg-info', // biru muda, kesan informatif
                                    'border' => 'border-info',
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
            <div class="row">
                {{-- Chart IDP per Jenjang --}}
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-layer-group text-primary mr-2"></i>
                            <h4 class="mb-0">Jenjang</h4>
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
                            <h4 class="mb-0">Learning Group</h4>
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
                    <h4>Grafik Nilai Karyawan (Hard vs Soft)</h4>
                </div>
                <div class="card-body">
                    <canvas id="chartKaryawan" height="100"></canvas>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
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
                        label: 'Learning Group',
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
                            text: 'Learning Group'
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
