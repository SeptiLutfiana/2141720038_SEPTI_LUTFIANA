@extends('layouts.app')

@section('title', 'Halaman Edit Jenjang')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Data Jenjang</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.data-master.karyawan.jenjang.index') }}">Data Jenjang</a></div>
                    <div class="breadcrumb-item">Edit Data Jenjang</div>
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
                        <h4>Edit Data Jenjang</h4>

                    </div>
                    <form action="{{ route('adminsdm.data-master.karyawan.jenjang.update', $jenjang->id_jenjang) }}" method="POST">
                        <div class="card-body">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label>Nama Jenjang</label>
                                <input type="text" name="nama_jenjang"
                                    class="form-control @if (old('nama_jenjang')) is-valid @endif
                                @error('nama_jenjang') is-invalid @enderror"
                                    value="{{ old('nama_jenjang', $jenjang->nama_jenjang) }}">
                            </div>
                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea name="keterangan"
                                    class="form-control @if (old('keterangan')) is-valid @endif
                                @error('keterangan') is-invalid @enderror"
                                    class="form-control" style="height:8rem;">{{ old('keterangan', $jenjang->keterangan) }}</textarea>
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
