@extends('layouts.app')

@section('title', 'Halaman Detail Panduan IDP')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Panduan IDP</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('karyawan.dashboard-karyawan') }}">Dashboard</a>
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
                                    <div class="border p-3" style="background-color: #f9f9f9;">
                                        @if (!empty($panduan) && !empty($panduan->isi))
                                            {!! $panduan->isi !!}
                                        @else
                                            <div class="text-center text-muted" style="padding: 40px 0;">
                                                <em>Panduan belum tersedia untuk role Anda.</em>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a class="btn btn-primary float-right"
                                    href="{{ route('karyawan.dashboard-karyawan') }}">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </section>

    </div>
@endsection
