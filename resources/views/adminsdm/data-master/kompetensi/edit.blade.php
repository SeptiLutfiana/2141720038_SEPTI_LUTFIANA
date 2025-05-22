@extends('layouts.app')

@section('title', 'Halaman Edit Kompetensi')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Data Kompetensi</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.data-master.kompetensi.index') }}">Data Kompetensi</a></div>
                    <div class="breadcrumb-item">Edit Data Kompetensi</div>
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
                        <h4>Edit Data Kompetensi</h4>

                    </div>
                    <form action="{{ route('adminsdm.data-master.kompetensi.update', $kompetensi->id_kompetensi) }}" method="POST">
                        <div class="card-body">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label>Nama Kompetensi</label>
                                <input type="text" name="nama_kompetensi"
                                    class="form-control @if (old('nama_kompetensi')) is-valid @endif
                                @error('nama_kompetensi') is-invalid @enderror"
                                    value="{{ old('nama_kompetensi', $kompetensi->nama_kompetensi) }}">
                            </div>
                            <div class="form-group">
                                <label>Jenis Kompetensi</label>
                                <select name="jenis_kompetensi" class="form-control">
                                    <option value="Hard Kompetensi" {{ old('jenis_kompetensi', $kompetensi->jenis_kompetensi) == 'Hard Kompetensi' ? 'selected' : '' }}>Hard Kompetensi</option>
                                    <option value="Soft Kompetensi" {{ old('jenis_kompetensi', $kompetensi->jenis_kompetensi) == 'Soft Kompetensi' ? 'selected' : '' }}>Soft Kompetensi</option>
                                </select>
                            </div> 
                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea name="keterangan"
                                    class="form-control @if (old('keterangan')) is-valid @endif
                                @error('keterangan') is-invalid @enderror"
                                    class="form-control" style="height:8rem;">{{ old('keterangan', $kompetensi->keterangan) }}</textarea>
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
