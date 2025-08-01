@extends('layouts.app')

@section('title', 'Halaman Data Karyawan')
@push('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.bootstrap4.min.css">
    <style>
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
    </script>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1> Data Karyawan</h1>

                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a
                            href="{{ route('adminsdm.data-master.karyawan.data-karyawan.index') }}">Data Karyawan</a></div>
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
                                <h4>Data Karyawan</h4>
                                <div class="card-header-action">
                                    <div class="dropdown mr-2">
                                        <button type="button" class="btn btn-danger rounded-pill dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-file-export"></i> Ekspor
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                                href="{{ route('adminsdm.data-master.karyawan.data-karyawan.printPdf') }}"
                                                target="_blank">
                                                <i class="fas fa-file-pdf text-danger"></i> PDF
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('adminsdm.data-master.karyawan.data-karyawan.exportExcel') }}">
                                                <i class="fas fa-file-excel text-success"></i> Excel
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('adminsdm.data-master.karyawan.data-karyawan.exportCSV') }}">
                                                <i class="fas fa-file-csv text-warning"></i> CSV
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('adminsdm.data-master.karyawan.data-karyawan.exportDocx') }}">
                                                <i class="fas fa-file-word text-primary"></i> Word (DOCX)
                                            </a>
                                        </div>
                                    </div>
                                    <a href="{{ route('adminsdm.data-master.karyawan.data-karyawan.create') }}"
                                        class="btn btn-icon btn-primary icon-left"><i class="fas fa-plus"></i>
                                        Tambah</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="GET"
                                    action="{{ route('adminsdm.data-master.karyawan.data-karyawan.index') }}"
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
                                            <select name="lg" class="tom-select" onchange="this.form.submit()">
                                                <option value="">-- Pilih Direktorat --</option>
                                                @foreach ($listLG as $lg)
                                                    <option value="{{ $lg->id_LG }}"
                                                        {{ request('lg') == $lg->id_LG ? 'selected' : '' }}>
                                                        {{ $lg->nama_LG }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Pilih Role</label>
                                            <select name="role" class="tom-select" onchange="this.form.submit()">
                                                <option value="">-- Pilih Role User --</option>
                                                @foreach ($listRole as $roleUser)
                                                    <option value="{{ $roleUser->id_role }}"
                                                        {{ request('role') == $roleUser->id_role ? 'selected' : '' }}>
                                                        {{ $roleUser->nama_role }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- <div class="col-md-3">
                                            <label>Pilih Semester</label>
                                            <select name="semester" class="form-control" onchange="this.form.submit()">
                                                <option value="">-- Pilih Semester --</option>
                                                @foreach ($listSemester as $LS)
                                                    <option value="{{ $LS->id_semester}}" {{ request('semester') == $semester->id_semester ? 'selected' : '' }}>
                                                        {{ $semester->nama_semester }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div> --}}
                                    </div>
                                </form>
                                <div class="table-responsive">
                                    @livewire('karyawan-table', [
                                        'search' => request('search'),
                                        'jenjang' => request('id_jenjang'),
                                        'lg' => request('lg'),
                                        'role' => request('role'),
                                        // 'semester' => request('semester'),
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
        Livewire.on('karyawanDeleted', message => {
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
@endpush
