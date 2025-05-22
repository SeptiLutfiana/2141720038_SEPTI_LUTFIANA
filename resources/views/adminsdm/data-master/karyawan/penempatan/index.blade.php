@extends('layouts.app')

@section('title', 'Halaman Data Penempatan')
@push('style')
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/select.bootstrap4.min.css') }}">
    @livewireStyles()
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1> Data Penempatan</h1>

                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a
                            href="{{ route('adminsdm.data-master.karyawan.penempatan.index') }}">Penempatan</a></div>
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
                                <h4>Data Penempatan</h4>
                                <div class="card-header-action">
                                    <div class="dropdown mr-2">
                                        <button type="button" class="btn btn-danger rounded-pill dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-file-export"></i> Ekspor
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                                href="{{ route('adminsdm.data-master.karyawan.penempatan.printPdf') }}"
                                                target="_blank">
                                                <i class="fas fa-file-pdf text-danger"></i> PDF
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('adminsdm.data-master.karyawan.penempatan.exportExcel') }}">
                                                <i class="fas fa-file-excel text-success"></i> Excel
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('adminsdm.data-master.karyawan.penempatan.exportCSV') }}">
                                                <i class="fas fa-file-csv text-warning"></i> CSV
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('adminsdm.data-master.karyawan.penempatan.exportDocx') }}">
                                                <i class="fas fa-file-word text-primary"></i> Word (DOCX)
                                            </a>
                                        </div>
                                    </div>
                                    <a href="{{ route('adminsdm.data-master.karyawan.penempatan.create') }}"
                                        class="btn btn-icon btn-primary icon-left"><i class="fas fa-plus"></i>
                                        Tambah</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('adminsdm.data-master.karyawan.penempatan.index') }}"
                                    class="mb-3">
                                    <div class="input-group w-25">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Cari Penempatan..." value="{{ request('search') }}">
                                    </div>
                                </form>
                                @livewire('penempatan-table', ['search' => request('search')])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('scripts')
    @livewireScripts()
    <script>
        Livewire.on('penempatanDeleted', message => {
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
