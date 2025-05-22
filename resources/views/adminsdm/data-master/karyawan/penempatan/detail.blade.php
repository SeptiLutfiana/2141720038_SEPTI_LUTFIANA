@extends('layouts.app')

@section('title', 'Halaman Detail Penempatan')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Detail Penempatan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.data-master.karyawan.penempatan.index') }}">Data Penempatan</a></div>
                    <div class="breadcrumb-item">Detail Penempatan</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row mt-sm-4">
                    <div class="col-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group col-12">
                                    <label>Id Penempatan</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $penempatan->id_penempatan }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Nama</label>
                                    <input readonly type="text" class="form-control" value="{{ $penempatan->nama_penempatan }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Keterangan</label>
                                    <textarea readonly class="form-control" style="height:8rem;">{{ $penempatan->keterangan }}</textarea>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a class="btn btn-primary float-right" href="{{ route('adminsdm.data-master.karyawan.penempatan.index') }}">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </section>

    </div>
@endsection
