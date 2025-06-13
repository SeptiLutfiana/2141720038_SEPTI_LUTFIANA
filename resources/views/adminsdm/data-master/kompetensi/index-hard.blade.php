@extends('layouts.app')

@section('title', 'Halaman Data Hard Kompetensi')
@push('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.bootstrap4.min.css">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Data Hard Kompetensi</h1>

                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('adminsdm.data-master.kompetensi.indexHard') }}">Hard
                            Kompetensi</a></div>
                </div>
            </div>
            <div class="section-body">
                @if (session('msg-success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
                        <div class="alert-body">
                            <div class="alert-title">Sukses</div>
                            {{ session('msg-success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Data Hard Kompetensi</h4>
                                <div class="card-header-action">
                                    <div class="dropdown mr-2">
                                        <button type="button" class="btn btn-danger rounded-pill dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-file-export"></i> Ekspor
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                                href="{{ route('adminsdm.data-master.kompetensi.printPdfHard') }}"
                                                target="_blank">
                                                <i class="fas fa-file-pdf text-danger"></i> PDF
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('adminsdm.data-master.kompetensi.exportExcelHard') }}">
                                                <i class="fas fa-file-excel text-success"></i> Excel
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('adminsdm.data-master.kompetensi.exportCSVHard') }}">
                                                <i class="fas fa-file-csv text-warning"></i> CSV
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('adminsdm.data-master.kompetensi.exportDocxHard') }}">
                                                <i class="fas fa-file-word text-primary"></i> Word (DOCX)
                                            </a>
                                        </div>
                                    </div>
                                    <a href="{{ route('adminsdm.data-master.kompetensi.create') }}"
                                        class="btn btn-icon btn-primary icon-left"><i class="fas fa-plus"></i>
                                        Tambah</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('adminsdm.data-master.kompetensi.indexHard') }}"
                                    class="mb-3">
                                    <div class="form-row">
                                        <div class="col-md-4">
                                            <label>Cari Kompetensi</label>
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Cari nama kompetensi..." value="{{ request('search') }}"
                                                oninput="this.form.submit()">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Pilih Jenjang</label>
                                            <select name="id_jenjang" class="form-control" onchange="this.form.submit()">
                                                <option value="">-- Semua Jenjang --</option>
                                                @foreach ($listJenjang as $j)
                                                    <option value="{{ $j->id_jenjang }}"
                                                        {{ request('id_jenjang') == $j->id_jenjang ? 'selected' : '' }}>
                                                        {{ $j->nama_jenjang }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Pilih Jabatan</label>
                                            <select name="id_jabatan" class="form-control" onchange="this.form.submit()">
                                                <option value="">-- Semua Jabatan --</option>
                                                @foreach ($listJabatan as $jab)
                                                    @if (!$listJenjang || request('id_jenjang') == $jab->id_jenjang)
                                                        <option value="{{ $jab->id_jabatan }}"
                                                            {{ request('id_jabatan') == $jab->id_jabatan ? 'selected' : '' }}>
                                                            {{ $jab->nama_jabatan }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </form>
                                @livewire('hard-kompetensi-table', [
                                    'search' => request('search'),
                                    'jenis' => request('jenis_kompetensi'),
                                    'jenjang' => request('id_jenjang'),
                                    'jabatan' => request('id_jabatan'),
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('scripts')
    <script>
        Livewire.on('kompetensiDeleted', message => {
            Swal.fire({
                icon: 'success',
                title: 'Sukses',
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        });
    </script>
@endpush
