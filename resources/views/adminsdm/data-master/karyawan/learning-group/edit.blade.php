@extends('layouts.app')

@section('title', 'Halaman Edit Learning Group')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Data Direktorat</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.data-master.karyawan.learning-group.index') }}">Data Direktorat</a></div>
                    <div class="breadcrumb-item">Edit Data Direktorat</div>
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
                        <h4>Edit Data Direktorat/h4>

                    </div>
                    <form action="{{ route('adminsdm.data-master.karyawan.learning-group.update', $LG->id_LG) }}" method="POST">
                        <div class="card-body">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label>Nama Direktorat</label>
                                <input type="text" name="nama_LG"
                                    class="form-control @if (old('nama_LG')) is-valid @endif
                                @error('nama_LG') is-invalid @enderror"
                                    value="{{ old('nama_LG', $LG->nama_LG) }}">
                            </div>
                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea name="keterangan"
                                    class="form-control @if (old('keterangan')) is-valid @endif
                                @error('keterangan') is-invalid @enderror"
                                    class="form-control" style="height:8rem;">{{ old('keterangan', $LG->keterangan) }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary">Submit</button>

                        </div>
                    </form>
                </div>
            </div>
        </section>

    </div>
@endsection
