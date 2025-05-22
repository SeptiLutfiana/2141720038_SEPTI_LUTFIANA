@extends('layouts.app')

@section('title', 'Halaman Tambah Data Supervisor')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Tambah Data Supervisor</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.data-master.supervisor.index') }}">Data Supervisor</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('adminsdm.data-master.supervisor.create') }}">Tambah Data Supervisor</a></div>
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
                        <h4>Tambah Data Supervisor</h4>
                    </div>
                    <form action="{{ route('adminsdm.data-master.supervisor.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                        <div class="form-group">
                            <label for="id_user">Pilih Karyawan Sebagai Supervisor:</label>
                            <select name="id_user" id="id_user" class="form-control" required>
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach($supervisor as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} (NPK: {{ $user->npk }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="card-footer text-right">
                        <button type="submit" class="btn btn-success">Tambah Supervisor</button>
                        </div>
                    </form> 
                </div>                   
                </div>
            </div>
        </section>
    </div>
@endsection
