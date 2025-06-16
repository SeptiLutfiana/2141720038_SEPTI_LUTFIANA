@extends('layouts.app')

@section('title', 'Halaman Evaluasi IDP')
@push('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.bootstrap4.min.css">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Data Evaluasi IDP</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Evaluasi IDP</div>
                </div>
            </div>

            <div class="section-body">
                @if (session('msg-success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div class="alert-icon"><i class="far fa-check-circle"></i></div>
                        <div class="alert-body">
                            <div class="alert-title">Sukses</div>
                            {{ session('msg-success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Evaluasi IDP</h4>
                                <div class="card-header-action">
                                    <div class="dropdown mr-1">
                                        <button type="button" class="btn btn-danger rounded-pill dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-file-export"></i> Ekspor
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            {{-- <a class="dropdown-item" href="{{ route('adminsdm.BankEvaluasi.printPdf') }}"
                                                target="_blank">
                                                <i class="fas fa-file-pdf text-danger"></i> PDF
                                            </a> --}}
                                            <a class="dropdown-item"
                                                href="{{ route('adminsdm.BankEvaluasi.EvaluasiPascaIdp.exportExcel') }}">
                                                <i class="fas fa-file-excel text-success"></i> Excel
                                            </a>
                                            <a class="dropdown-item" href="{{ route('adminsdm.BankEvaluasi.EvaluasiPascaIdp.exportCSV') }}">
                                                <i class="fas fa-file-csv text-warning"></i> CSV
                                            </a>
                                            {{-- <a class="dropdown-item" href="{{ route('adminsdm.BankEvaluasi.exportDocx') }}">
                                                <i class="fas fa-file-word text-primary"></i> Word (DOCX)
                                            </a> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('adminsdm.BankEvaluasi.EvaluasiPascaIdp.index') }}"
                                    class="mb-3">
                                    <div class="form-row">
                                        <div class="col-md-3">
                                            <label>Cari Karyawan</label>
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Cari nama pengisi evaluasi..." value="{{ request('search') }}"
                                                oninput="this.form.submit()">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Pilih Tahun</label>
                                            <select name="tahun" class="form-control" onchange="this.form.submit()">
                                                <option value="">-- Semua Tahun --</option>
                                                @foreach ($listTahun as $tahun)
                                                    <option value="{{ $tahun }}"
                                                        {{ request('tahun') == $tahun ? 'selected' : '' }}>
                                                        {{ $tahun }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </form>
                                @livewire('evaluasi-pasca-idp-table', [
                                    'search' => request('search'),
                                    'tahun' => request('tahun'),
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
        Livewire.on('EvaluasiIdpDeleted', message => {
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
