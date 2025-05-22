@extends('layouts.app')

@section('title', 'Halaman Detail Karyawan')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Detail Karyawan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.data-master.karyawan.data-karyawan.index') }}">Data Karyawan</a></div>
                    <div class="breadcrumb-item">Detail Karyawan</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row mt-sm-4">
                    <div class="col-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group col-12">
                                    <label>Nomor Induk Pegawai</label>
                                    <input readonly type="text" class="form-control" value="{{ $user->npk }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Nama Lengkap</label>
                                    <input readonly type="text" class="form-control" value="{{ $user->name}}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Role User</label>
                                    <input readonly type="text" class="form-control" value="{{ $user->roles->pluck('nama_role')->join(', ') }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Jenjang</label>
                                    <input readonly type="text" class="form-control" value="{{ $user->jenjang->nama_jenjang}}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Jabatan</label>
                                    <input readonly type="text" class="form-control" value="{{ $user->jabatan->nama_jabatan }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Angkatan PSP</label>
                                    <input readonly type="text" class="form-control" value="{{ $user->angkatanpsp->bulan }} - {{ $user->angkatanPsp->tahun }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Divisi</label>
                                    <input readonly type="text" class="form-control" value="{{ $user->divisi->nama_divisi}}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Penempatan</label>
                                    <input readonly type="text" class="form-control" value="{{ $user->penempatan->nama_penempatan}}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Learning Group</label>
                                    <input readonly type="text" class="form-control" value="{{ $user->learninggroup->nama_LG}}">
                                </div>
                                 <div class="form-group col-12">
                                    <label>Semester</label>
                                    <input readonly type="text" class="form-control" value="{{ $user->semester->nama_semester}}">
                                </div>
                                <div class="form-group col-12">
                                    <label>No HP</label>
                                    <input readonly type="text" class="form-control" value="{{ $user->no_hp}}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Email</label>
                                    <input readonly type="text" class="form-control" value="{{ $user->email}}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Status</label>
                                    <input readonly type="text" class="form-control" value="{{ $user->status}}">
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a class="btn btn-primary float-right" href="{{ route('adminsdm.data-master.karyawan.data-karyawan.index') }}">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </section>

    </div>
@endsection
