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
                    <div class="breadcrumb-item active">
                        <a
                            href="{{ $kompetensi->jenis_kompetensi === 'Soft Kompetensi'
                                ? route('adminsdm.data-master.kompetensi.indexSoft')
                                : route('adminsdm.data-master.kompetensi.indexHard') }}">
                            {{ $kompetensi->jenis_kompetensi === 'Soft Kompetensi' ? 'Data Soft Kompetensi' : 'Data Hard Kompetensi' }}
                        </a>
                    </div>
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
                    <form action="{{ route('adminsdm.data-master.kompetensi.update', $kompetensi->id_kompetensi) }}"
                        method="POST">
                        <div class="card-body">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label>Nama Kompetensi</label>
                                <input type="text" name="nama_kompetensi"
                                    class="form-control @if (old('nama_kompetensi')) is-valid @endif @error('nama_kompetensi') is-invalid @enderror"
                                    value="{{ old('nama_kompetensi', $kompetensi->nama_kompetensi) }}">
                            </div>

                            @if ($kompetensi->jenis_kompetensi === 'Hard Kompetensi')
                                <div class="form-group">
                                    <label>Jenjang</label>
                                    <select name="id_jenjang"
                                        class="form-control select2 @error('id_jenjang') is-invalid @enderror">
                                        <option value="">-- Pilih Jenjang --</option>
                                        @foreach ($jenjang as $j)
                                            <option value="{{ $j->id_jenjang }}"
                                                {{ old('id_jenjang', $kompetensi->id_jenjang) == $j->id_jenjang ? 'selected' : '' }}>
                                                {{ $j->nama_jenjang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Jabatan</label>
                                    <select name="id_jabatan"
                                        class="form-control select2 @error('id_jabatan') is-invalid @enderror">
                                        <option value="">-- Pilih Jabatan --</option>
                                        @foreach ($jabatan as $jbt)
                                            <option value="{{ $jbt->id_jabatan }}"
                                                {{ old('id_jabatan', $kompetensi->id_jabatan) == $jbt->id_jabatan ? 'selected' : '' }}>
                                                {{ $jbt->nama_jabatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea name="keterangan"
                                    class="form-control @if (old('keterangan')) is-valid @endif @error('keterangan') is-invalid @enderror"
                                    style="height:8rem;">{{ old('keterangan', $kompetensi->keterangan) }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            @if ($kompetensi->jenis_kompetensi === 'Soft Kompetensi')
                                <a href="{{ route('adminsdm.data-master.kompetensi.indexSoft') }}"
                                    class="btn btn-warning mr-2">Kembali</a>
                            @else
                                <a href="{{ route('adminsdm.data-master.kompetensi.indexHard') }}"
                                    class="btn btn-warning mr-2">Kembali</a>
                            @endif
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('select[name="id_jenjang"]').on('change', function() {
                var jenjangId = $(this).val();
                var $jabatanSelect = $('select[name="id_jabatan"]');
                $jabatanSelect.html('<option>Loading...</option>');

                if (jenjangId) {
                    $.ajax({
                        url: '/admin/datamaster/kompetensi/get-jabatan-by-jenjang/' + jenjangId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $jabatanSelect.empty();
                            $jabatanSelect.append(
                                '<option value="">-- Pilih Jabatan --</option>');
                            $.each(data, function(key, value) {
                                $jabatanSelect.append('<option value="' + value
                                    .id_jabatan + '">' + value.nama_jabatan +
                                    '</option>');
                            });
                        },
                        error: function() {
                            $jabatanSelect.html(
                                '<option value="">-- Gagal memuat jabatan --</option>');
                        }
                    });
                } else {
                    $jabatanSelect.html('<option value="">-- Pilih Jenjang terlebih dahulu --</option>');
                }
            });

            // Trigger change on page load agar dropdown jabatan terisi sesuai jenjang yg sudah dipilih
            $('select[name="id_jenjang"]').trigger('change');
        });
    </script>
@endpush
