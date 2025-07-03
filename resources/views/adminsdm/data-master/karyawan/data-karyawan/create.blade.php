@extends('layouts.app')

@section('title', 'Halaman Tambah Data Karyawan')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Tambah Data Karyawan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a
                            href="{{ route('adminsdm.data-master.karyawan.data-karyawan.index') }}">Data Karyawan</a></div>
                    <div class="breadcrumb-item"><a
                            href="{{ route('adminsdm.data-master.karyawan.data-karyawan.create') }}">Tambah Data Karyawan</a>
                    </div>
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
                        <h4>Tambah Data Karyawan</h4>
                    </div>
                    <form action="{{ route('adminsdm.data-master.karyawan.data-karyawan.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label>Metode Input</label><br>
                                <span class="input-option active" id="manual-option"
                                    onclick="toggleInputMethod('manual')">Input Manual</span>
                                <span class="input-option" id="upload-option" onclick="toggleInputMethod('upload')">Upload
                                    File</span>
                            </div>
                            <input type="hidden" id="input-method" name="input_manual" value="1">
                            {{-- Default: manual --}}
                            <!-- Input Manual -->
                            <div id="input-manual">
                                <div class="form-group">
                                    <label>Role</label>
                                    <select name="id_role" class="form-control @error('id_role') is-invalid @enderror">
                                        <option value="">-- Pilih Role --</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id_role }}"
                                                {{ old('id_role') == $role->id_role ? 'selected' : '' }}>
                                                {{ $role->nama_role }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Jenjang</label>
                                    <select name="id_jenjang" id="select-jenjang"
                                        class="form-control @error('id_jenjang') is-invalid @enderror">
                                        <option value="">-- Pilih Jenjang --</option>
                                        @foreach ($jenjang as $item)
                                            <option value="{{ $item->id_jenjang }}"
                                                {{ old('id_jenjang') == $item->id_jenjang ? 'selected' : '' }}>
                                                {{ $item->nama_jenjang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Jabatan</label>
                                    <select name="id_jabatan" id="select-jabatan"
                                        class="form-control @error('id_jabatan') is-invalid @enderror">
                                        <option value="">-- Pilih Jabatan --</option>
                                        {{-- Data jabatan akan diisi oleh JavaScript --}}
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Name </label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}">
                                </div>
                                <div class="form-group">
                                    <label>Nomor Induk Karyawan</label>
                                    <input type="text" name="npk"
                                        class="form-control @error('npk') is-invalid @enderror"
                                        value="{{ old('npk') }}">
                                </div>
                                <div class="form-group">
                                    <label>No Hp</label>
                                    <input type="text" name="no_hp"
                                        class="form-control @error('no_hp') is-invalid @enderror"
                                        value=" {{ old('no_hp') }}">
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="text" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value=" {{ old('email') }}">
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="text" name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        value=" {{ old('password') }}">
                                </div>
                                <div class="form-group">
                                    <label>Learning Group</label>
                                    <select name="id_LG" class="form-control @error('id_LG') is-invalid @enderror">
                                        <option value="">-- Pilih Learning Group --</option>
                                        @foreach ($LG as $item)
                                            <option value="{{ $item->id_LG }}"
                                                {{ old('id_LG') == $item->id_LG ? 'selected' : '' }}>
                                                {{ $item->nama_LG }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Semester</label>
                                    <select name="id_semester"
                                        class="form-control @error('id_semester') is-invalid @enderror">
                                        <option value="">-- Pilih Semester --</option>
                                        @foreach ($semester as $item)
                                            <option value="{{ $item->id_semester }}"
                                                {{ old('id_semester') == $item->id_semester ? 'selected' : '' }}>
                                                {{ $item->nama_semester }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Angkatan PSP</label>
                                    <select name="id_angkatanpsp"
                                        class="form-control @error('id_angkatanpsp') is-invalid @enderror">
                                        <option value="">-- Pilih Angkatan PSP --</option>
                                        @foreach ($angkatanpsp as $item)
                                            <option value="{{ $item->id_angkatanpsp }}"
                                                {{ old('id_angkatanpsp') == $item->id_angkatanpsp ? 'selected' : '' }}>
                                                {{ $item->bulan }} - {{ $item->tahun }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Divisi</label>
                                    <select name="id_divisi" class="form-control @error('id_divisi') is-invalid @enderror">
                                        <option value="">-- Pilih Divisi --</option>
                                        @foreach ($divisi as $item)
                                            <option value="{{ $item->id_divisi }}"
                                                {{ old('id_divisi') == $item->id_divisi ? 'selected' : '' }}>
                                                {{ $item->nama_divisi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Penempatan</label>
                                    <select name="id_penempatan"
                                        class="form-control @error('id_penempatan') is-invalid @enderror">
                                        <option value="">-- Pilih Penempatan --</option>
                                        @foreach ($penempatan as $item)
                                            <option value="{{ $item->id_penempatan }}"
                                                {{ old('id_penempatan') == $item->id_penempatan ? 'selected' : '' }}>
                                                {{ $item->nama_penempatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                                        <option value="">-- Pilih Status --</option>
                                        <option value="aktif"
                                            {{ old('status', $user->status ?? '') == 'aktif' ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value="verify"
                                            {{ old('status', $user->status ?? '') == 'verify' ? 'selected' : '' }}>Verify
                                        </option>
                                        <option value="banned"
                                            {{ old('status', $user->status ?? '') == 'banned' ? 'selected' : '' }}>Banned
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            <!-- Upload File -->
                            <div id="input-upload" style="display: none;">
                                <div class="form-group">
                                    <label>Upload File (CSV/XLSX)</label>
                                    <input type="file" name="file_import"
                                        class="form-control @error('file_import') is-invalid @enderror"
                                        accept=".xlsx,.csv">
                                    <small class="form-text text-muted">
                                        Jenis file yang diperbolehkan: <strong>.xlsx</strong>, <strong>.csv</strong>. Ukuran
                                        maksimal: <strong>10MB</strong>.
                                        <br>
                                        Format Tabel: <strong>no</strong>, <strong>role</strong>, <strong>jenjang</strong>,
                                        <strong>jabatan</strong>, <strong>angkatanpsp</strong>, <strong>divisi</strong>,
                                        <strong>penempatan</strong>, <strong>lg</strong>,
                                        <strong>semester</strong>,<strong>npk</strong>,
                                        <strong>no_hp</strong>, <strong>name</strong>, <strong>email</strong>,
                                        <strong>password</strong>, <strong>status</strong>.
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="reset" class="btn btn-warning">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            function toggleInputMethod(method) {
                document.getElementById('manual-option').classList.remove('active');
                document.getElementById('upload-option').classList.remove('active');
                if (method === 'manual') {
                    document.getElementById('manual-option').classList.add('active');
                    document.getElementById('input-manual').style.display = 'block';
                    document.getElementById('input-upload').style.display = 'none';
                    document.getElementById('input-method').value = 1;
                } else if (method === 'upload') {
                    document.getElementById('upload-option').classList.add('active');
                    document.getElementById('input-manual').style.display = 'none';
                    document.getElementById('input-upload').style.display = 'block';
                    document.getElementById('input-method').value = ''; // kosongkan supaya tidak masuk logic manual
                }
            }
            $(document).ready(function() {
                $('#select-jenjang').on('change', function() {
                    let jenjangId = $(this).val();
                    if (jenjangId) {
                        $.ajax({
                            url: '/admin/datamaster/karyawan/get-jabatan-by-jenjang/' + jenjangId,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                $('#select-jabatan').empty();
                                $('#select-jabatan').append(
                                    '<option value="">-- Pilih Jabatan --</option>');
                                $.each(data, function(key, value) {
                                    $('#select-jabatan').append('<option value="' + value
                                        .id_jabatan + '">' + value.nama_jabatan +
                                        '</option>');
                                });
                            }
                        });
                    } else {
                        $('#select-jabatan').empty().append('<option value="">-- Pilih Jabatan --</option>');
                    }
                });
            });
        </script>
    @endpush
@endsection
