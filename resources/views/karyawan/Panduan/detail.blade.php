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
                    <div class="breadcrumb-item active">
                        <a href="{{ route('karyawan.dashboard-karyawan') }}">Dashboard</a>
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
                                        @if ($panduan)
                                            @php
                                                $isi = $panduan->isi ?? '';
                                                $isFile =
                                                    Str::contains($isi, ['.pdf', '.doc', '.docx', '.xls', '.xlsx']) &&
                                                    Str::contains($isi, 'href=');
                                            @endphp
                                            @if ($isFile)
                                                <p class="mb-2 text-dark">
                                                    <i class="fas fa-file-pdf text-danger"></i>
                                                    <strong>Panduan tersedia dalam bentuk file dokumen.</strong><br>
                                                    Silakan unduh melalui tautan berikut:
                                                </p>
                                                <div class="border rounded p-2 bg-white text-danger">
                                                    {!! $panduan->isi !!}
                                                </div>
                                            @else
                                                {!! $panduan->isi !!}
                                            @endif

                                            <div class="text-right text-muted mt-3">
                                                <small>Diunggah pada
                                                    {{ \Carbon\Carbon::parse($panduan->created_at)->translatedFormat('d F Y') }}</small>
                                            </div>
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
        </section>
    </div>
@endsection
