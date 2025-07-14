@extends('layouts.app')

@section('title', 'Halaman Tambah Data Angkatan PSP')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    <style>
        .input-option {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 10px;
            cursor: pointer;
            border: 1px solid #ccc;
            margin-right: 5px;
            border-radius: 25px;
            /* Membuat tombol lonjong */
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: background-color 0.3s ease;
        }

        .input-option.active {
            background-color: #086044;
            color: #fff;
        }

        .input-option:hover {
            background-color: #83B92C;
            color: #fff;
        }

        .input-option .circle {
            position: absolute;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #fff;
            opacity: 0;
            transform: scale(0);
            transition: transform 0.4s, opacity 0.4s;
        }

        .input-option.active .circle {
            opacity: 1;
            transform: scale(1);
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Tambah Data Angkatan PSP</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a
                            href="{{ route('adminsdm.data-master.karyawan.angkatan-psp.index') }}">Data Angkatan PSP</a>
                    </div>
                    <div class="breadcrumb-item"><a
                            href="{{ route('adminsdm.data-master.karyawan.angkatan-psp.create') }}">Tambah Data Angkatan
                            PSP</a></div>
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
                @if (session('msg-success'))
                    <div class="alert alert-success">{!! session('msg-success') !!}</div>
                @endif

                @if (session('msg-error'))
                    <div class="alert alert-danger">{!! session('msg-error') !!}</div>
                @endif
                <div class="card">
                    <div class="card-header">
                        <h4>Tambah Data Angkatan PSP</h4>
                    </div>
                    <form action="{{ route('adminsdm.data-master.karyawan.angkatan-psp.store') }}" method="POST"
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
                                    <label>Bulan</label>
                                    <select name="bulan" class="form-control @error('bulan') is-invalid @enderror">
                                        <option value="">-- Pilih Bulan --</option>
                                        @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $bulan)
                                            <option value="{{ $bulan }}"
                                                {{ old('bulan') == $bulan ? 'selected' : '' }}>{{ $bulan }}</option>
                                        @endforeach
                                    </select>
                                    @error('bulan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Tahun</label>
                                    <input type="number" name="tahun"
                                        class="form-control @error('tahun') is-invalid @enderror"
                                        value="{{ old('tahun') }}" placeholder="YYYY">
                                    @error('tahun')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Upload File -->
                            <div id="input-upload" style="display: none;">
                                <div class="form-group">
                                    <label>Upload File (CSV/XLSX)</label>
                                    <input type="file" name="file_import"
                                        class="form-control @error('file_import') is-invalid @enderror" accept=".xlsx,.csv">
                                    <small class="form-text text-muted">
                                        Jenis file yang diperbolehkan: <strong>.xlsx</strong>, <strong>.csv</strong>. Ukuran
                                        maksimal: <strong>0.5MB</strong>.<br>
                                        Format Tabel: <strong>no</strong>, <strong>Bulan</strong> (contoh: Januari),
                                        <strong>Tahun</strong>.
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
        </script>
    @endpush
@endsection
