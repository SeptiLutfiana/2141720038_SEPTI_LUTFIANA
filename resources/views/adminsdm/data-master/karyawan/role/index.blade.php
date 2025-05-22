@extends('layouts.app')

@section('title', 'Halaman Data Role')
@push('style')
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/select.bootstrap4.min.css') }}">
    @livewireStyles()
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Role User</h1>

                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('adminsdm.data-master.karyawan.role.index') }}">Role User</a></div>
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
                                <h4>Data Role User</h4>
                                {{-- <div class="card-header-action">
                                    <a href="#" class="btn btn-icon btn-danger icon-left" target="_blank" rel="noopener noreferrer"><i
                                        class="fas fa-print"></i>
                                    Print PDF</a>
                                    <a href="{{ route('adminsdm.data-master.karyawan.role.create') }}" class="btn btn-icon btn-primary icon-left"><i
                                            class="fas fa-plus"></i>
                                        Tambah</a>
                                </div> --}}
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('adminsdm.data-master.karyawan.role.index') }}" class="mb-3">
                                    <div class="input-group w-25">
                                        <input type="text" name="search" class="form-control" placeholder="Cari Role..." value="{{ request('search') }}">
                                    </div>
                                </form>                                
                                @livewire('role-table', ['search' => request('search')])
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
        Livewire.on('roleDeleted', message => {
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
