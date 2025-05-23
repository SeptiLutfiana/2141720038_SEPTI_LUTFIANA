@extends('layouts.app')

@section('title', 'Halaman Data Behavior IDP')
@push('style')
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/select.bootstrap4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Behavior Individual Development Plan</h1>

                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('karyawan.dashboard-karyawan') }}">Dashboard</a>
                    </div>
                    <div class="breadcrumb-item">Data IDP</div>
                </div>
            </div>

            <div class="section-body">
                @if (session('msg-success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
                        <div class="alert-body">
                            <div class="alert-title">Sukses</div>
                            {{ session('msg-success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Data Individual Development Plan</h4>
                                <div class="card-header-action">
                                    <a href="#" class="btn btn-icon btn-danger icon-left" target="_blank">
                                        <i class="fas fa-print"></i> Print PDF
                                    </a>
                                    <a href="#" class="btn btn-icon btn-primary icon-left">
                                        <i class="fas fa-plus"></i> Tambah
                                    </a>
                                </div>
                            </div>

                            <div class="card-body table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Proyeksi Karir</th>
                                            <th>Persetujuan Mentor</th>
                                            <th>Status Pengajuan IDP</th>
                                            <th>Progres IDP</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($idps as $i => $item)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $item->proyeksi_karir }}</td>
                                                <td>{{ $item->status_approval_mentor }}</td>
                                                <td>{{ $item->status_pengajuan_idp }}</td>
                                                <td>
                                                    @php
                                                        // Dummy data
                                                        $selesai = 2;
                                                        $total = 2;
                                                        $persen = $total > 0 ? round(($selesai / $total) * 100) : 0;

                                                        // Warna progress bar
                                                        $warna = 'bg-danger';
                                                        if ($persen >= 80) {
                                                            $warna = 'bg-success';
                                                        } elseif ($persen >= 50) {
                                                            $warna = 'bg-warning';
                                                        }
                                                    @endphp

                                                    <div style="font-size: 10px;" class="text-muted mb-1">
                                                        {{ $selesai }}/{{ $total }} | {{ $persen }}%
                                                    </div>
                                                    <div class="progress" style="height: 6px; border-radius: 999px;">
                                                        <div class="progress-bar {{ $warna }}" role="progressbar"
                                                            style="width: {{ $persen }}%; border-radius: 999px;"
                                                            aria-valuenow="{{ $persen }}" aria-valuemin="0"
                                                            aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="text-left" style="width: 120px;">
                                                    <a href="#" class="btn btn-warning btn-sm mb-1"> <i
                                                            class="fas fa-edit"></i>
                                                        Edit</a>
                                                    <br>
                                                    <a href="{{ route('karyawan.IDP.showKaryawan', $item->id_idp) }}"
                                                        class="btn btn-primary btn-sm mb-1"> <i
                                                            class="fas fa-info-circle"></i> Detail</a>
                                                    <br>
                                                    <form action="#" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm rounded mb-1">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                    <br>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Belum ada data.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
