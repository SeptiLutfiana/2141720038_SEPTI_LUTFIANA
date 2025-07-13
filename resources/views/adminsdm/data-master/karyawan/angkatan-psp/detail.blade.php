@extends('layouts.app')

@section('title', 'Halaman Detail Angkatan PSP')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Detail Angkatan PSP</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.data-master.karyawan.angkatan-psp.index') }}">Data Angkatan PSP</a></div>
                    <div class="breadcrumb-item">Detail Angkatan PSP</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row mt-sm-4">
                    <div class="col-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group col-12">
                                    <label>Bulan</label>
                                    <input readonly type="text" class="form-control" value="{{ $angkatanpsp->bulan }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Tahun</label>
                                    <textarea readonly class="form-control" style="height:8rem;">{{ $angkatanpsp->tahun }}</textarea>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a class="btn btn-primary float-right" href="{{ route('adminsdm.data-master.karyawan.angkatan-psp.index') }}">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </section>

    </div>
@endsection
