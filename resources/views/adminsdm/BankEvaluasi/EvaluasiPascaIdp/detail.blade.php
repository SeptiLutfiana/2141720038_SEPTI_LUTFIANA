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
                <div class="card">
                    <div class="card-header d-block">
                        <h5 class="mb-2">Nama Pengisi: <strong>{{ $evaluasi->user->name }}</strong></h5>
                        <p class="mb-0 text-muted">Tanggal Evaluasi:
                            {{ $evaluasi->created_at->translatedFormat('d M Y H:i') }}
                        </p>
                    </div>
                    <div class="card-body">

                        @if ($evaluasi->jawaban->isEmpty())
                            <div class="text-center text-muted">
                                <p>Belum ada jawaban tersimpan untuk evaluasi ini.</p>
                            </div>
                        @else
                            @foreach ($evaluasi->jawaban as $jawaban)
                                <div class="card mb-3 border">
                                    <div class="card-body">
                                        <p><strong>{{ $jawaban->bankEvaluasi->pertanyaan }}</strong></p>
                                        <p class="text">
                                            @if ($jawaban->jawaban_likert)
                                                Skor: <strong>{{ $jawaban->jawaban_likert }}</strong>
                                            @elseif ($jawaban->jawaban_esai)
                                                Jawaban: {{ $jawaban->jawaban_esai }}
                                            @else
                                                <em class="text-muted">Belum diisi</em>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                    </div>
                    <div class="card-footer text-right">
                        <a href="{{ route('adminsdm.BankEvaluasi.EvaluasiPascaIdp.index') }}"
                            class="btn btn-primary">Kembali</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
