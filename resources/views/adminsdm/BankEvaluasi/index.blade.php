@extends('layouts.app')

@section('title', 'Halaman Bank Evaluasi IDP')
@push('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.bootstrap4.min.css">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1> Data Pertanyaan Evaluasi</h1>

                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a>Bank Evaluasi IDP</a></div>
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
                                <h4>Bank Evaluasi</h4>
                                <div class="card-header-action">
                                    <div class="dropdown mr-2">
                                        <button type="button" class="btn btn-danger rounded-pill dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-file-export"></i> Ekspor
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('adminsdm.BankEvaluasi.printPdf') }}"
                                                target="_blank">
                                                <i class="fas fa-file-pdf text-danger"></i> PDF
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('adminsdm.BankEvaluasi.exportExcel') }}">
                                                <i class="fas fa-file-excel text-success"></i> Excel
                                            </a>
                                            <a class="dropdown-item" href="{{ route('adminsdm.BankEvaluasi.exportCSV') }}">
                                                <i class="fas fa-file-csv text-warning"></i> CSV
                                            </a>
                                            <a class="dropdown-item" href="{{ route('adminsdm.BankEvaluasi.exportDocx') }}">
                                                <i class="fas fa-file-word text-primary"></i> Word (DOCX)
                                            </a>
                                        </div>
                                    </div>

                                    <a href="{{ route('adminsdm.BankEvaluasi.create') }}"
                                        class="btn btn-icon btn-primary icon-left"><i class="fas fa-plus"></i>
                                        Tambah</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('adminsdm.BankEvaluasi.index') }}" class="mb-3">
                                    <div class="form-row">
                                        <div class="col-md-3">
                                            <label>Cari Pertanyaan Evaluasi</label>
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Cari pertanyaan evaluasi..." value="{{ request('search') }}"
                                                oninput="this.form.submit()">
                                        </div>
                                        {{-- <div class="col-md-3">
                                            <label>Cari Jenis Evaluasi</label>
                                            <select name="jenis_evaluasi" class="form-control"
                                                onchange="this.form.submit()">
                                                <option value="">-- Semua Jenis Evaluasi --</option>
                                                <option value="onboarding"
                                                    {{ request('jenis_evaluasi') == 'onboarding' ? 'selected' : '' }}>
                                                    Onboarding</option>
                                                <option value="pasca"
                                                    {{ request('jenis_evaluasi') == 'pasca' ? 'selected' : '' }}>Pasca IDP
                                                </option>
                                            </select>
                                        </div> --}}
                                        <div class="col-md-3">
                                            <label>Cari Tipe Pertanyaan</label>
                                            <select name="tipe_pertanyaan" class="form-control"
                                                onchange="this.form.submit()">
                                                <option value="">-- Semua Tipe Pertanyaan --</option>
                                                <option value="likert"
                                                    {{ request('tipe_pertanyaan') == 'likert' ? 'selected' : '' }}>
                                                    Skala Likert</option>
                                                <option value="esai"
                                                    {{ request('tipe_pertanyaan') == 'esai' ? 'selected' : '' }}>Esai
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Role</label>
                                            <select name="untuk_role" class="form-control" onchange="this.form.submit()">
                                                <option value="">-- Semua Role --</option>
                                                <option value="supervisor"
                                                    {{ request('untuk_role') == 'supervisor' ? 'selected' : '' }}>
                                                    Supervisor</option>
                                                <option value="mentor"
                                                    {{ request('untuk_role') == 'mentor' ? 'selected' : '' }}>Mentor
                                                </option>
                                                <option value="karyawan"
                                                    {{ request('untuk_role') == 'karyawan' ? 'selected' : '' }}>Karyawan
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                                @livewire('bank-evaluasi-table', [
                                    'search' => request('search'),
                                    'jenisEvaluasi' => request('jenis_evaluasi'),
                                    'tipePertanyaan' => request('tipe_pertanyaan'),
                                    'untukRole' => request('untuk_role'),
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
        Livewire.on('BankEvaluasiDeleted', message => {
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
