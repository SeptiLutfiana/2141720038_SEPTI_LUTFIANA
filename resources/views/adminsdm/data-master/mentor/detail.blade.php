@extends('layouts.app')

@section('title', 'Halaman Detail Mentor')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Detail Mentor</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.data-master.mentor.index') }}">Data Mentor</a></div>
                    <div class="breadcrumb-item">Detail Mentor</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row mt-sm-4">
                    <div class="col-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                 <div class="form-group col-12">
                                    <label>Nomor Induk Pegawai</label>
                                    <input readonly type="text" class="form-control" value="{{ $mentor->user->npk }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Nama Mentor</label>
                                    <input readonly type="text" class="form-control" value="{{ $mentor->user->name }}">
                                </div>
                                 <div class="form-group col-12">
                                    <label>Nama Telepon</label>
                                    <input readonly type="text" class="form-control" value="{{ $mentor->user->no_hp }}">
                                </div>
                                 <div class="form-group col-12">
                                    <label>Email</label>
                                    <input readonly type="text" class="form-control" value="{{ $mentor->user->email }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Learning Group</label>
                                    <input readonly type="text" class="form-control" value="{{ $mentor->user->learningGroup->nama_LG}}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Jabatan</label>
                                    <input readonly type="text" class="form-control" value="{{ $mentor->user->jabatan->nama_jabatan}}">
                                </div>
                                 <div class="form-group col-12">
                                    <label>Jenjang</label>
                                    <input readonly type="text" class="form-control" value="{{ $mentor->user->jenjang->nama_jenjang}}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Divisi</label>
                                    <input readonly type="text" class="form-control" value="{{ $mentor->user->divisi->nama_divisi}}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Penempatan</label>
                                    <input readonly type="text" class="form-control" value="{{ $mentor->user->penempatan->nama_penempatan}}">
                                </div>
                                <div class="form-group col-12">
                                    <label>Role</label>
                                    <input readonly type="text" class="form-control" value="{{ $mentor->role->nama_role }}">
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a class="btn btn-primary float-right" href="{{ route('adminsdm.data-master.mentor.index') }}">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </section>

    </div>
@endsection
