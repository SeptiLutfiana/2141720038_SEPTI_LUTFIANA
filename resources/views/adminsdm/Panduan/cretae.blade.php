@extends('layouts.app')

@section('title', 'Halaman Tambah Panduan IDP')

@push('style')
    <!-- Summernote CSS -->
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
    <style>
        .note-editor {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .note-editor .note-toolbar {
            background-color: #f8f9fa;
            padding: 8px 10px;
            border-bottom: 1px solid #e9ecef;
        }

        .note-editable {
            min-height: 300px;
            padding: 1rem;
            background-color: #fff;
        }

        .alert-danger ul {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Tambah Data Panduan IDP</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('adminsdm.Panduan.index') }}">Data Panduan IDP</a></div>
                    <div class="breadcrumb-item active">Tambah Panduan</div>
                </div>
            </div>

            <div class="section-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h4>Form Tambah Panduan IDP</h4>
                    </div>

                    <form action="{{ route('adminsdm.Panduan.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="judul">Judul Panduan</label>
                                <input type="text" name="judul" id="judul"
                                    class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul') }}"
                                    placeholder="Masukkan judul panduan">
                                @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="id_role">Panduan Ini Ditujukan Untuk Role</label>
                                <select name="id_role" id="id_role"
                                    class="form-control @error('id_role') is-invalid @enderror">
                                    <option value="">-- Pilih Role User --</option>
                                    @if (isset($role))
                                        @foreach ($role as $item)
                                            <option value="{{ $item->id_role }}"
                                                {{ old('id_role') == $item->id_role ? 'selected' : '' }}>
                                                {{ $item->nama_role }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('id_role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="isi">Isi Panduan</label>
                                <textarea name="isi" id="isi" class="form-control summernote @error('isi') is-invalid @enderror"
                                    placeholder="Ketik isi panduan di sini...">{{ old('isi') }}</textarea>
                                @error('isi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="reset" class="btn btn-warning" id="reset-btn">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('script')
    <!-- Summernote JS -->
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>

    <script>
        console.log("=== SUMMERNOTE INITIALIZATION ===");

        $(document).ready(function() {
            // Debug checks
            console.log('jQuery loaded:', typeof $ !== 'undefined');
            console.log('Bootstrap loaded:', typeof $.fn.modal !== 'undefined');
            console.log('Summernote loaded:', typeof $.fn.summernote !== 'undefined');
            console.log('Target elements found:', $('.summernote').length);

            // Initialize Summernote
            if (typeof $.fn.summernote !== 'undefined') {
                $('.summernote').summernote({
                    height: 300,
                    minHeight: 200,
                    placeholder: 'Ketik isi panduan di sini...',
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear', 'fontsize']],
                        ['fontname', ['fontname']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ],
                    callbacks: {
                        onInit: function() {
                            console.log('✅ Summernote initialized successfully!');
                        }
                    }
                });
            } else {
                console.error('❌ Summernote not loaded!');
                alert('ERROR: Summernote gagal dimuat. Periksa asset path.');
            }

            // Reset button handler
            $('#reset-btn').click(function(e) {
                e.preventDefault();
                if (confirm('Yakin ingin mereset form?')) {
                    $('form')[0].reset();
                    $('.summernote').summernote('code', '');
                }
            });
        });
    </script>
@endpush
