@extends('layouts.app')

@section('title', 'Halaman Tambah Data Mentor')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Tambah Data Mentor</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.data-master.mentor.index') }}">Data Mentor</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('adminsdm.data-master.mentor.create') }}">Tambah Data Mentor</a></div>
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
                        <h4>Tambah Data Mentor</h4>
                    </div>
                    <form action="{{ route('adminsdm.data-master.mentor.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                        <div class="form-group">
                            <label for="id_user">Pilih Karyawan Sebagai Mentor:</label>
                            <select name="id_user" id="id_user" class="form-control" required>
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach($mentor as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} (NPK: {{ $user->npk }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="card-footer text-right">
                        <button type="submit" class="btn btn-success">Tambah Mentor</button>
                        </div>
                    </form> 
                </div>                   
                </div>
            </div>
        </section>
    </div>
@endsection
