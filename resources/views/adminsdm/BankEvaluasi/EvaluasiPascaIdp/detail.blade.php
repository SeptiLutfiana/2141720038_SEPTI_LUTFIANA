@extends('layouts.app')

@section('title', 'Detail Jawaban Evaluasi')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Detail Jawaban Evaluasi</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active">
                        <a href="{{ route('adminsdm.dashboard') }}">Dashboard</a>
                    </div>
                    <div class="breadcrumb-item active">
                        <a href="{{ route('adminsdm.BankEvaluasi.EvaluasiPascaIdp.index') }}">Data Evaluasi</a>
                    </div>
                    <div class="breadcrumb-item">Detail Jawaban Evaluasi</div>
                </div>
            </div>

            <div class="section-body">
                <div class="card shadow">
                    <div class="card-header d-block">
                        <h4 class="mb-3 font-weight-bold">Informasi Umum</h4>
                        <hr>
                        <ul class="list-unstyled mb-0">
                            <li><strong>Nama Pengisi:</strong> {{ $evaluasi->user->name }}</li>
                            <li><strong>Tanggal Pengisian:</strong>
                                {{ $evaluasi->created_at->translatedFormat('d M Y H:i') }}</li>
                            <li><strong>Jenis Evaluasi:</strong> {{ ucfirst($evaluasi->jenis_evaluasi) }}</li>
                            <li><strong>Sebagai:</strong> {{ ucfirst($evaluasi->sebagai_role ?? '-') }}</li>
                            @if ($evaluasi->idps)
                                <li><strong>Nama Mantee (Karyawan):</strong> {{ $evaluasi->idps->karyawan->name ?? '-' }}
                                </li>
                                <li><strong>Proyeksi Karir:</strong> {{ $evaluasi->idps->proyeksi_karir ?? '-' }}</li>
                            @endif
                        </ul>
                    </div>

                    <div class="card-body">
                        <h5 class="mb-3 font-weight-bold">Jawaban Evaluasi Pasca IDP</h5>

                        @if ($evaluasi->jawaban->isEmpty())
                            <div class="alert alert-warning text-center">
                                Belum ada jawaban tersimpan untuk evaluasi ini.
                            </div>
                        @else
                            @foreach ($evaluasi->jawaban as $jawaban)
                                <div class="card mb-3 border-left-info">
                                    <div class="card-body">
                                        <p class="mb-1 font-weight-bold">{{ $jawaban->bankEvaluasi->pertanyaan }}</p>
                                        @if ($jawaban->jawaban_likert)
                                            <span class="badge badge-primary">Skor: {{ $jawaban->jawaban_likert }}</span>
                                        @elseif ($jawaban->jawaban_esai)
                                            <p class="mt-2">Jawaban: {{ $jawaban->jawaban_esai }}</p>
                                        @else
                                            <em class="text-muted">Belum diisi</em>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="card-footer text-right">
                        <a href="{{ route('adminsdm.BankEvaluasi.EvaluasiPascaIdp.index') }}"
                            class="btn btn-primary"><i class="fas fa-arrow-left"></i> Kembali</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
