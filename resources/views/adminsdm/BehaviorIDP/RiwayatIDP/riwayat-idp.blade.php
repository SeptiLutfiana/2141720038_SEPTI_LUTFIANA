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

        .form-control,
        .ts-control {
            height: 45px !important;
            padding: 0.75rem 0.75rem !important;
            font-size: 14px;
            line-height: 1.5;
            background-color: #fff;
        }

        .ts-wrapper.single .ts-control {
            background-image: none;
        }

        .ts-control:focus,
        .form-control:focus {
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        /* Biar teks placeholder sama */
        .form-control::placeholder {
            color: #6c757d;
            opacity: 1;
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
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Data Individual Development Plan</h4>
                                <form id="exportForm" method="GET"
                                    action="{{ route('adminsdm.BehaviorIDP.RiwayatIDP.cetakFiltered') }}" target="_blank"
                                    class="mb-0">

                                    <input type="hidden" name="selected" id="selectedIdsInput">
                                    <input type="hidden" name="select_all" id="selectAllFlag" value="0">

                                    {{-- Jaga-jaga filter dikirim ulang saat export --}}
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                    <input type="hidden" name="id_jenjang" value="{{ request('id_jenjang') }}">
                                    <input type="hidden" name="id_LG" value="{{ request('id_LG') }}">
                                    <input type="hidden" name="tahun" value="{{ request('tahun') }}">

                                    <button type="submit" class="btn btn-danger btn-icon icon-left">
                                        <i class="fas fa-print"></i> Print PDF
                                    </button>
                                </form>
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
                                            <select name="id_jenjang" class="tom-select" onchange="this.form.submit()">
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
                                            <label>Pilih Direktorat</label>
                                            <select name="id_LG" class="tom-select" onchange="this.form.submit()">
                                                <option value="">-- Pilih Direktorat --</option>
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
                                            <select name="tahun" class="tom-select" onchange="this.form.submit()">
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
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
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

        function initTomSelect() {
            document.querySelectorAll('.tom-select').forEach(function(selectElement) {
                new TomSelect(selectElement, {
                    plugins: ['dropdown_input'],
                    allowEmptyOption: true,
                    create: false
                });
            });
        }

        // Inisialisasi awal saat page load
        document.addEventListener('DOMContentLoaded', initTomSelect);

        // Re-init jika Livewire render ulang (jika perlu)
        Livewire.hook('message.processed', () => {
            initTomSelect();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAllCheckbox');
            const checkboxes = document.querySelectorAll('.idp-checkbox');
            const exportForm = document.getElementById('exportForm');
            const selectedInput = document.getElementById('selectedIdsInput');
            const selectAllFlag = document.getElementById('selectAllFlag');

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    const isChecked = this.checked;
                    checkboxes.forEach(cb => cb.checked = isChecked);
                    selectAllFlag.value = isChecked ? '1' : '0';
                });
            }

            exportForm.addEventListener('submit', function(e) {
                if (selectAllFlag.value === '1') {
                    selectedInput.value = ''; // kosongkan, supaya controller ambil semua data
                } else {
                    let selected = [];
                    checkboxes.forEach(cb => {
                        if (cb.checked) selected.push(cb.value);
                    });
                    selectedInput.value = selected.join(',');
                }
            });
        });
    </script>
@endpush
