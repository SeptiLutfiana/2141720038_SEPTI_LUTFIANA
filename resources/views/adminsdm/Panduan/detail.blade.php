@extends('layouts.app')

@section('title', 'Halaman Detail Panduan')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1> Detail Panduan IDP</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.Panduan.index') }}">Data Panduan IDP</a>
                    </div>
                    <div class="breadcrumb-item">Detail Panduan</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row mt-sm-4">
                    <div class="col-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group col-12">
                                    <label>Judul Panduan</label>
                                    <input readonly type="text" class="form-control" value="{{ $panduan->judul }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Tujuan Panduan IDP</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $panduan->roles->pluck('nama_role')->implode(', ') ?: '-' }}">
                                </div>
                                <div class="form-group col-12">
                                    <label class="font-weight-bold">Isi Panduan IDP</label>

                                    @php
                                        use Illuminate\Support\Str;
                                        $isi = $panduan->isi;
                                        $isFile = Str::contains($isi, ['.pdf', '.doc', '.docx', '.xls', '.xlsx']);
                                        $fileName = $isFile ? basename($isi) : null;
                                    @endphp

                                    @if ($isFile)
                                        <div class="p-4 rounded border border-danger bg-light">
                                            <p class="text-dark mb-2">
                                                <i class="fas fa-file-pdf text-danger"></i>
                                                <strong> Panduan tersedia dalam bentuk file dokumen (PDF/DOC).</strong><br>
                                                Silakan unduh melalui tautan berikut:
                                            </p>
                                            <div class="border p-3 rounded bg-white">
                                                {!! $panduan->isi !!}
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-light border shadow-sm" style="background-color: #fcfcfc;">
                                            {!! $isi !!}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a class="btn btn-primary float-right"
                                    href="{{ route('adminsdm.Panduan.index') }}">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </section>

    </div>
@endsection
