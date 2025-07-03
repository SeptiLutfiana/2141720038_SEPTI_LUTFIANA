@extends('layouts.app')

@section('title', 'Halaman Edit Panduan IDP')

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
                <h1>Edit Data Panduan IDP</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('adminsdm.Panduan.index') }}">Data Panduan IDP</a></div>
                    <div class="breadcrumb-item active">Edit Panduan</div>
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
                        <h4>Form Edit Panduan IDP</h4>
                    </div>

                    <form action="{{ route('adminsdm.Panduan.update', $panduan->id_panduan) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group">
                                <label for="judul">Judul Panduan</label>
                                <input type="text" name="judul" id="judul"
                                    class="form-control @error('judul') is-invalid @enderror"
                                    value="{{ old('judul', $panduan->judul) }}" placeholder="Masukkan judul panduan">
                                @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @php
                                $selectedRoleId = $panduan->roles->first()->id_role ?? old('id_role');
                            @endphp

                            <div class="form-group">
                                <label for="id_role">Panduan Ini Ditujukan Untuk Role</label>
                                <select name="id_role" id="id_role"
                                    class="form-control @error('id_role') is-invalid @enderror">
                                    <option value="">-- Pilih Role User --</option>
                                    @if (isset($role))
                                        @foreach ($role as $item)
                                            <option value="{{ $item->id_role }}"
                                                {{ $selectedRoleId == $item->id_role ? 'selected' : '' }}>
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
                                <label>Upload PDF atau Dokumen</label>
                                <input type="file" id="upload-file-btn" class="form-control-file"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx">
                            </div>
                            <div class="form-group">
                                <label for="isi">Isi Panduan</label>
                                <textarea name="isi" id="isi" class="form-control summernote @error('isi') is-invalid @enderror"
                                    placeholder="Ketik isi panduan di sini...">{{ old('isi', $panduan->isi) }}</textarea>
                                @error('isi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card-footer text-right">
                            <a href="{{ route('adminsdm.Panduan.index') }}" class="btn btn-warning">Kembali</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- Summernote JS -->
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>

    <script>
        console.log("=== SUMMERNOTE INITIALIZATION (EDIT MODE) ===");

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
                            console.log('✅ Summernote initialized successfully in edit mode!');
                        }
                    }
                });
            } else {
                console.error('❌ Summernote not loaded!');
                alert('ERROR: Summernote gagal dimuat. Periksa asset path.');
            }

            // Reset button handler - reset to original values
            $('#reset-btn').click(function(e) {
                e.preventDefault();
                if (confirm('Yakin ingin mereset form ke nilai asli?')) {
                    // Reset form fields to original values
                    $('#judul').val('{{ $panduan->judul }}');
                    $('#id_role').val('{{ $panduan->id_role }}');
                    $('.summernote').summernote('code', '{!! addslashes($panduan->isi) !!}');
                }
            });
        });
        $('#upload-file-btn').on('change', function() {
            const file = this.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: '{{ route('adminsdm.Panduan.upload-file') }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(url) {
                    const linkHtml = `<a href="${url}" target="_blank">${file.name}</a>`;
                    $('.summernote').summernote('pasteHTML', linkHtml);
                },
                error: function(xhr) {
                    alert('Gagal upload file: ' + xhr.responseText);
                }
            });
        });
    </script>
@endpush
