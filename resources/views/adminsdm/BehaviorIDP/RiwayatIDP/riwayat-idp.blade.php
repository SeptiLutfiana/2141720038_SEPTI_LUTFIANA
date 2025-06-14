@extends('layouts.app')

@section('title', 'Halaman Data Behavior IDP')
@push('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.bootstrap4.min.css">
    <style>
        body {
            zoom: 90%;
            /* ubah ke 85% jika masih terasa besar */
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Riwayat Perencanaan Individual Development Plan</h1>

                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Data IDP</div>
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
                                <h4>Data Individual Development Plan</h4>
                                <div class="card-header-action">
                                    <a href="{{ route('adminsdm.BehaviorIDP.cetakFiltered', [
                                        'search' => request('search'),
                                        'id_jenjang' => request('id_jenjang'),
                                        'id_LG' => request('id_LG'),
                                        'tahun' => request('tahun'),
                                    ]) }}"
                                        class="btn btn-icon btn-danger icon-left" target="_blank">
                                        <i class="fas fa-print"></i> Print PDF
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('adminsdm.BehaviorIDP.RiwayatIDP.indexRiwayatIdp') }}"
                                    class="mb-3">
                                    <div class="form-row">
                                        <div class="col-md-3">
                                            <label>Cari Karyawan</label>
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Cari nama karyawan..." value="{{ request('search') }}"
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
                                            <label>Pilih Learning Group</label>
                                            <select name="id_LG" class="form-control" onchange="this.form.submit()">
                                                <option value="">-- Pilih Learning Group --</option>
                                                @foreach ($listLG as $lg)
                                                    <option value="{{ $lg->id_LG }}"
                                                        {{ request('id_LG') == $lg->id_LG ? 'selected' : '' }}>
                                                        {{ $lg->nama_LG }}
                                                    </option>
                                                @endforeach
                                            </select>
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
                                <div class="table-responsive">
                                    @livewire('riwayat-idp-table', [
                                        'search' => request('search'),
                                        'jenjang' => request('id_jenjang'),
                                        'lg' => request('id_LG'),
                                        'tahun' => request('tahun'),
                                    ])
                                </div>
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
        Livewire.on('idpDeleted', message => {
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
