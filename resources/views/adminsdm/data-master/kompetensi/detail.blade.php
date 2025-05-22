@extends('layouts.app')

@section('title', 'Halaman Detail Kompetensi')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Detail Kompetensi</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.data-master.kompetensi.index') }}">Data Kompetensi</a></div>
                    <div class="breadcrumb-item">Detail Kompetensi</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row mt-sm-4">
                    <div class="col-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group col-12">
                                    <label>Id Kompetensi</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $kompetensi->id_kompetensi }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Nama Kompetensi</label>
                                    <input readonly type="text" class="form-control" value="{{ $kompetensi->nama_kompetensi }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Jenis Kompetensi</label>
                                    <input readonly type="text" class="form-control" value="{{ $kompetensi->jenis_kompetensi}}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Keterangan</label>
                                    <textarea readonly class="form-control" style="height:8rem;">{{ $kompetensi->keterangan }}</textarea>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a class="btn btn-primary float-right" href="{{ route('adminsdm.data-master.kompetensi.index') }}">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </section>

    </div>
@endsection
