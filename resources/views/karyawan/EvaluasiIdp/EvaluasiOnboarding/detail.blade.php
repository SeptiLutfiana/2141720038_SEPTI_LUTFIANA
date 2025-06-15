@extends('layouts.app')

@section('title', 'Detail Evaluasi Onboarding')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Detail Evaluasi Onboarding</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="{{ route('karyawan.dashboard-karyawan') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a
                            href="{{ route('karyawan.EvaluasiIdp.EvaluasiOnboarding.indexKaryawan') }}">Evaluasi
                            Onboarding</a></div>
                    <div class="breadcrumb-item">Detail Evaluasi</div>
                </div>
            </div>

            <div class="section-body">
                <div class="card">
                    <div class="card-header">
                        <h4>Informasi IDP</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Nama Mentor:</strong> {{ $idp->mentor->name ?? '-' }}</li>
                            <li class="list-group-item"><strong>Proyeksi Karir:</strong> {{ $idp->proyeksi_karir }}</li>
                            <li class="list-group-item"><strong>Waktu:</strong>
                                {{ \Carbon\Carbon::parse($idp->waktu_mulai)->format('d M Y') }} -
                                {{ \Carbon\Carbon::parse($idp->waktu_selesai)->format('d M Y') }}</li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4>Riwayat Evaluasi Onboarding</h4>
                    </div>
                    <div class="card-body">
                        @forelse ($idp->evaluasiIdp as $eval)
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    Evaluator: {{ $eval->user->name ?? '-' }}
                                    <span>{{ \Carbon\Carbon::parse($eval->tanggal_evaluasi)->format('d M Y') }}</span>
                                </div>
                                <p style="white-space: pre-line;">{{ $eval->catatan }}</p>
                                <hr>
                            </div>
                        @empty
                            <p class="text-muted">Belum ada evaluasi onboarding.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
