@extends('layouts.app')

@section('title', 'Halaman Tambah Data Semester')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Tambah Data Semester</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a
                            href="{{ route('adminsdm.data-master.data-idp.semester.index') }}">Data Semester</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('adminsdm.data-master.data-idp.semester.create') }}">Tambah
                            Data Semester</a></div>
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
                @if (session('duplikat'))
                    <div class="alert alert-warning mt-3">
                        <strong>Beberapa baris tidak diimpor:</strong>
                        <ul class="mb-0">
                            @foreach (session('duplikat') as $fail)
                                <li>{{ $fail }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header">
                        <h4>Tambah Data Semester</h4>
                    </div>
                    <form action="{{ route('adminsdm.data-master.data-idp.semester.store') }}" method="POST"
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
                                    <label>Nama Semester</label>
                                    <input type="text" name="nama_semester"
                                        class="form-control @error('nama_semester') is-invalid @enderror"
                                        value="{{ old('nama_semester') }}">
                                </div>
                                <div class="form-group">
                                    <label>Keterangan</label>
                                    <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" style="height:8rem;">{{ old('keterangan') }}</textarea>
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
                                        maksimal: <strong>0.5MB</strong>.
                                        <br>
                                        Format Tabel: <strong>no</strong>, <strong>nama_semester</strong>,
                                        <strong>keterangan</strong>.
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
