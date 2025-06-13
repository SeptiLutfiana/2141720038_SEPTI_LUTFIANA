@extends('layouts.app')

@section('title', 'Detail Pertanyaan Evaluasi')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Detail Pertanyaan Evaluasi</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.BankEvaluasi.index') }}">Data Evaluasi</a>
                    </div>
                    <div class="breadcrumb-item">Detail Pertanyaan Evaluasi</div>
                </div>
            </div>

            <div class="section-body">
                <div class="card">
                    <div class="card-header">
                        <h4>Detail Pertanyaan Evaluasi</h4>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label>Jenis Evaluasi</label>
                            <input type="text" class="form-control" value="{{ ucfirst($bankEvaluasi->jenis_evaluasi) }}"
                                readonly>
                        </div>

                        <div class="form-group">
                            <label>Pertanyaan Untuk Role</label>
                            <input type="text" class="form-control" value="{{ ucfirst($bankEvaluasi->untuk_role) }}"
                                readonly>
                        </div>

                        <div class="form-group">
                            <label>Tipe Pertanyaan</label>
                            <input type="text" class="form-control" value="{{ ucfirst($bankEvaluasi->tipe_pertanyaan) }}"
                                readonly>
                        </div>

                        <div class="form-group">
                            <label>Isi Pertanyaan</label>
                            <textarea class="form-control" rows="3" readonly>{{ $bankEvaluasi->pertanyaan }}</textarea>
                        </div>

                    </div>
                    <div class="card-footer text-right">
                        <a href="{{ route('adminsdm.BankEvaluasi.index') }}" class="btn btn-primary">Kembali</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
