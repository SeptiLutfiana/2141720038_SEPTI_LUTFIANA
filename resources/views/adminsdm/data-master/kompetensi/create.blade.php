@extends('layouts.app')

@section('title', 'Halaman Tambah Data Kompetensi')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Tambah Data Kompetensi</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a
                            href="{{ route('adminsdm.data-master.kompetensi.indexSoft') }}">Data Soft
                            Kompetensi</a></div>
                    <div class="breadcrumb-item active"><a
                            href="{{ route('adminsdm.data-master.kompetensi.indexHard') }}">Data
                            Hard Kompetensi</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('adminsdm.data-master.kompetensi.create') }}">Tambah Data
                            Kompetensi</a></div>
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

                <!-- Menampilkan pesan sukses -->
                @if (session('success'))
                    <div class="pt-3">
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
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
                    <div class="alert alert-warning mt-2">
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
                        <h4>Tambah Data Kompetensi</h4>
                    </div>
                    <form action="{{ route('adminsdm.data-master.kompetensi.store') }}" method="POST"
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
                                    <label for="jenis_kompetensi">Jenis Kompetensi</label>
                                    <select name="jenis_kompetensi"
                                        class="form-control @error('jenis_kompetensi') is-invalid @enderror">
                                        <option value="">-- Pilih Kompetensi --</option>
                                        <option value="Hard Kompetensi"
                                            {{ old('jenis_kompetensi', $kompetensi->jenis_kompetensi ?? '') == 'Hard Kompetensi' ? 'selected' : '' }}>
                                            Hard Kompetensi</option>
                                        <option value="Soft Kompetensi"
                                            {{ old('jenis_kompetensi', $kompetensi->jenis_kompetensi ?? '') == 'Soft Kompetensi' ? 'selected' : '' }}>
                                            Soft Kompetensi</option>
                                    </select>
                                    @error('jenis_kompetensi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group jenjang-jabatan-group" style="display:none;">
                                    <label>Jenjang</label>
                                    <select name="id_jenjang"
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

                                <div class="form-group jenjang-jabatan-group" style="display:none;">
                                    <label>Jabatan</label>
                                    <select name="id_jabatan"
                                        class="form-control @error('id_jabatan') is-invalid @enderror">
                                        <option value="">-- Pilih Jabatan --</option>
                                        @foreach ($jabatan as $item)
                                            <option value="{{ $item->id_jabatan }}"
                                                {{ old('id_jabatan') == $item->id_jabatan ? 'selected' : '' }}>
                                                {{ $item->nama_jabatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Nama Kompetensi</label>
                                    <input type="text" name="nama_kompetensi"
                                        class="form-control @error('nama_kompetensi') is-invalid @enderror"
                                        value="{{ old('nama_kompetensi') }}">
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
                                        Format Tabel: <strong>no</strong>, <strong>jenis_kompetensi</strong>,
                                        <strong>jenjang</strong>, <strong>jabatan</strong>,
                                        <strong>jenis_kompetensi</strong>, <strong>keterangan</strong>.
                                        <br>
                                        <strong>Catatan: </strong>Untuk Soft Kompetensi, silakan kosongkan bagian Jenjang
                                        dan Jabatan. </small>
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
                    document.getElementById('input-method').value = '';
                }
            }

            // Tampilkan atau sembunyikan jenjang & jabatan berdasarkan jenis kompetensi
            document.querySelector('select[name="jenis_kompetensi"]').addEventListener('change', function() {
                const jenis = this.value;
                const jenjangFields = document.querySelectorAll('.jenjang-jabatan-group');
                if (jenis === 'Hard Kompetensi') {
                    jenjangFields.forEach(el => el.style.display = 'block');
                } else {
                    jenjangFields.forEach(el => el.style.display = 'none');
                }
            });

            // Load jabatan berdasarkan jenjang
            document.querySelector('select[name="id_jenjang"]').addEventListener('change', function() {
                const jenjangId = this.value;
                const jabatanSelect = document.querySelector('select[name="id_jabatan"]');
                jabatanSelect.innerHTML = `<option value="">Loading...</option>`;

                fetch(`/admin/datamaster/kompetensi/get-jabatan-by-jenjang/${jenjangId}`)
                    .then(response => response.json())
                    .then(data => {
                        jabatanSelect.innerHTML = `<option value="">-- Pilih Jabatan --</option>`;
                        data.forEach(jabatan => {
                            jabatanSelect.innerHTML +=
                                `<option value="${jabatan.id_jabatan}">${jabatan.nama_jabatan}</option>`;
                        });
                    })
                    .catch(error => {
                        jabatanSelect.innerHTML = `<option value="">-- Gagal Memuat Jabatan --</option>`;
                        console.error('Error fetching jabatan:', error);
                    });
            });

            // Jalankan di awal jika old value ada
            window.addEventListener('DOMContentLoaded', () => {
                const jenis = document.querySelector('select[name="jenis_kompetensi"]').value;
                const jenjangFields = document.querySelectorAll('.jenjang-jabatan-group');
                if (jenis === 'Hard Kompetensi') {
                    jenjangFields.forEach(el => el.style.display = 'block');
                }

                // Jika ada old('id_jenjang'), trigger change untuk load jabatan awal
                const selectedJenjang = document.querySelector('select[name="id_jenjang"]').value;
                const selectedJabatan = `{{ old('id_jabatan') }}`;
                if (selectedJenjang) {
                    fetch(`/admin/datamaster/kompetensi/get-jabatan-by-jenjang/${selectedJenjang}`)
                        .then(response => response.json())
                        .then(data => {
                            const jabatanSelect = document.querySelector('select[name="id_jabatan"]');
                            jabatanSelect.innerHTML = `<option value="">-- Pilih Jabatan --</option>`;
                            data.forEach(jabatan => {
                                const selected = jabatan.id_jabatan == selectedJabatan ? 'selected' : '';
                                jabatanSelect.innerHTML +=
                                    `<option value="${jabatan.id_jabatan}" ${selected}>${jabatan.nama_jabatan}</option>`;
                            });
                        });
                }
            });
        </script>
    @endpush

@endsection
