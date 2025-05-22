@extends('layouts.app')

@section('title', 'Halaman Detail Jenjang')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Detail Jenjang</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.data-master.karyawan.jenjang.index') }}">Data Jenjang</a></div>
                    <div class="breadcrumb-item">Detail Jenjang</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row mt-sm-4">
                    <div class="col-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group col-12">
                                    <label>Id Jenjang</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $jenjang->id_jenjang }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Nama</label>
                                    <input readonly type="text" class="form-control" value="{{ $jenjang->nama_jenjang }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Keterangan</label>
                                    <textarea readonly class="form-control" style="height:8rem;">{{ $jenjang->keterangan }}</textarea>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a class="btn btn-primary float-right" href="{{ route('adminsdm.data-master.karyawan.jenjang.index') }}">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </section>

    </div>
@endsection
