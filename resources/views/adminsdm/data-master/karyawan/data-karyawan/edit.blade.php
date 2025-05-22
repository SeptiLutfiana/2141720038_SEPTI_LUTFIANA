@extends('layouts.app')

@section('title', 'Halaman Edit Karyawan')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Data Karyawan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a
                            href="{{ route('adminsdm.data-master.karyawan.data-karyawan.index') }}">Data Karyawan</a></div>
                    <div class="breadcrumb-item">Edit Data Karyawan</div>
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
                        <h4>Edit Data Karyawan</h4>

                    </div>
                    <form action="{{ route('adminsdm.data-master.karyawan.data-karyawan.update', $user->id) }}"
                        method="POST">
                        <div class="card-body">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label>Role User</label>
                                <select name="id_role_display" class="form-control" disabled>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id_role }}"
                                            {{ old('id_role', $user->id_role) == $role->id_role ? 'selected' : '' }}>
                                            {{ $role->nama_role }}
                                        </option>
                                    @endforeach
                                </select>
                                <!-- input hidden agar value tetap terkirim -->
                                <input type="hidden" name="id_role" value="{{ old('id_role', $user->id_role) }}">
                            </div>

                            <div class="form-group">
                                <label>Jenjang</label>
                                <select name="id_jenjang" class="form-control">
                                    @foreach ($jenjang as $item)
                                        <option value="{{ $item->id_jenjang }}"
                                            {{ old('id_jenjang', $user->id_jenjang) == $item->id_jenjang ? 'selected' : '' }}>
                                            {{ $item->nama_jenjang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Jabatan</label>
                                <select name="id_jabatan" class="form-control">
                                    @foreach ($jabatan as $item)
                                        <option value="{{ $item->id_jabatan }}"
                                            {{ old('id_jabatan', $user->id_jabatan) == $item->id_jabatan ? 'selected' : '' }}>
                                            {{ $item->nama_jabatan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Angkatan PSP</label>
                                <select name="id_angkatanpsp" class="form-control">
                                    @foreach ($angkatanpsp as $item)
                                        <option value="{{ $item->id_angkatanpsp }}"
                                            {{ old('id_angkatanpsp', $user->id_angkatanpsp) == $item->id_angkatanpsp ? 'selected' : '' }}>
                                            {{ $item->bulan }} - {{ $item->tahun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Divisi</label>
                                <select name="id_divisi" class="form-control">
                                    @foreach ($divisi as $item)
                                        <option value="{{ $item->id_divisi }}"
                                            {{ old('id_divisi', $user->id_divisi) == $item->id_divisi ? 'selected' : '' }}>
                                            {{ $item->nama_divisi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Penempatan</label>
                                <select name="id_penempatan" class="form-control">
                                    @foreach ($penempatan as $item)
                                        <option value="{{ $item->id_penempatan }}"
                                            {{ old('id_penempatan', $user->id_penempatan) == $item->id_penempatan ? 'selected' : '' }}>
                                            {{ $item->nama_penempatan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Learning Group</label>
                                <select name="id_LG" class="form-control">
                                    @foreach ($LG as $item)
                                        <option value="{{ $item->id_LG }}"
                                            {{ old('id_LG', $user->id_LG) == $item->id_LG ? 'selected' : '' }}>
                                            {{ $item->nama_LG }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Semester</label>
                                <select name="id_semester" class="form-control">
                                    @foreach ($semester as $item)
                                        <option value="{{ $item->id_semester }}"
                                            {{ old('id_semester', $user->id_semester) == $item->id_semester ? 'selected' : '' }}>
                                            {{ $item->nama_semester }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nomor Induk Pegawai</label>
                                <input type="text" name="npk"
                                    class="form-control @if (old('npk')) is-valid @endif
                                @error('npk') is-invalid @enderror"
                                    value="{{ old('npk', $user->npk) }}">
                            </div>
                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" name="name"
                                    class="form-control @if (old('name')) is-valid @endif
                                @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}">
                            </div>
                            <div class="form-group">
                                <label>No Hp</label>
                                <input type="text" name="no_Hp"
                                    class="form-control @if (old('no_hp')) is-valid @endif
                                @error('no_hp') is-invalid @enderror"
                                    value="{{ old('no_hp', $user->no_hp) }}">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" name="email"
                                    class="form-control @if (old('email')) is-valid @endif
                                @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="aktif" {{ old('status', $user->status) == 'aktif' ? 'selected' : '' }}>
                                        Aktif</option>
                                    <option value="verify"
                                        {{ old('status', $user->status) == 'verify' ? 'selected' : '' }}>Verify</option>
                                    <option value="banned"
                                        {{ old('status', $user->status) == 'banned' ? 'selected' : '' }}>Banned</option>
                                </select>
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
