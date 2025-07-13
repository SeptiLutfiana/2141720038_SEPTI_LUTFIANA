@extends('layouts.app')

@section('title', 'Halaman Edit Kompetensi')

@push('style')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
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
                                    class="form-control tom-select @if (old('nama_kompetensi')) is-valid @endif @error('nama_kompetensi') is-invalid @enderror"
                                    value="{{ old('nama_kompetensi', $kompetensi->nama_kompetensi) }}">
                            </div>

                            @if ($kompetensi->jenis_kompetensi === 'Hard Kompetensi')
                                <div class="form-group">
                                    <label>Jenjang</label>
                                    <select name="id_jenjang"
                                        class="tom-select @error('id_jenjang') is-invalid @enderror">
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
                                        class="tom-select @error('id_jabatan') is-invalid @enderror">
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
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect('select[name="id_jenjang"]');
            var jabatanSelect = new TomSelect('select[name="id_jabatan"]');

            // Update jabatan saat jenjang berubah
            document.querySelector('select[name="id_jenjang"]').addEventListener('change', function() {
                var jenjangId = this.value;
                jabatanSelect.clearOptions();
                jabatanSelect.addOption({
                    value: '',
                    text: 'Loading...'
                });
                jabatanSelect.refreshOptions(false);

                if (jenjangId) {
                    fetch(`/admin/datamaster/kompetensi/get-jabatan-by-jenjang/${jenjangId}`)
                        .then(response => response.json())
                        .then(data => {
                            jabatanSelect.clearOptions();
                            jabatanSelect.addOption({
                                value: '',
                                text: '-- Pilih Jabatan --'
                            });
                            data.forEach(function(item) {
                                jabatanSelect.addOption({
                                    value: item.id_jabatan,
                                    text: item.nama_jabatan
                                });
                            });
                            jabatanSelect.refreshOptions(false);

                            // Set selected value setelah load
                            const oldJabatan = @json(old('id_jabatan', $kompetensi->id_jabatan));
                            if (oldJabatan) {
                                jabatanSelect.setValue(oldJabatan);
                            }
                        }).catch(() => {
                            jabatanSelect.clearOptions();
                            jabatanSelect.addOption({
                                value: '',
                                text: '-- Gagal memuat jabatan --'
                            });
                            jabatanSelect.refreshOptions(false);
                        });
                } else {
                    jabatanSelect.clearOptions();
                    jabatanSelect.addOption({
                        value: '',
                        text: '-- Pilih Jenjang terlebih dahulu --'
                    });
                    jabatanSelect.refreshOptions(false);
                }
            });

            // Trigger agar jabatan langsung termuat saat halaman dibuka
            document.querySelector('select[name="id_jenjang"]').dispatchEvent(new Event('change'));
        });
    </script>
@endpush
