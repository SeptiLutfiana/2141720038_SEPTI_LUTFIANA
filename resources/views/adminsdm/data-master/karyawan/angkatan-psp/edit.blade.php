@extends('layouts.app')

@section('title', 'Halaman Edit Angkatan PSP')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Data Angkatan PSP</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.data-master.karyawan.angkatan-psp.index') }}">Data Angkatan PSP</a></div>
                    <div class="breadcrumb-item">Edit Data Angkatan PSP</div>
                </div>
            </div>

            <div class="section-body">
                @if ($errors->any())
                    <div class="pt-3">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul>
                                @foreach ($errors->all() as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Data Angkatan PSP</h4>

                    </div>
                    <form action="{{ route('adminsdm.data-master.karyawan.angkatan-psp.update', $angkatanpsp->id_angkatanpsp) }}" method="POST">
                        <div class="card-body">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label>Bulan</label>
                                <select name="bulan" class="form-control">
                                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $bulan)
                                    <option value="{{ $bulan }}" {{ old('bulan', $angkatanpsp->bulan) == $bulan ? 'selected' : '' }}>{{ $bulan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tahun</label>
                                <input type="number" name="tahun" class="form-control" value="{{ old('tahun', $angkatanpsp->tahun) }}">
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

    </div>
@endsection
