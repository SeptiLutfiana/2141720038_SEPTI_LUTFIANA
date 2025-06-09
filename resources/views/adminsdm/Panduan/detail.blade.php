@extends('layouts.app')

@section('title', 'Halaman Detail Panduan')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Panduan IDP</h1>
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
                                    <label>Isi Panduan IDP</label>
                                    <div class="border p-3" style="background-color: #f9f9f9;">
                                        {!! $panduan->isi !!}
                                    </div>
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
