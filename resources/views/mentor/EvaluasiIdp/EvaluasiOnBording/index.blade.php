@extends('layouts.app')

@section('title', 'Halaman Evaluasi Onboarding')
@push('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.bootstrap4.min.css">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Evaluasi Onboarding</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('mentor.dashboard-mentor') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Evaluasi Onboarding</div>
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
                                <h4>Evaluasi Onboarding IDP</h4>
                            </div>
                            <div class="card-body">
                                <livewire:evaluasi-on-bording-mentor-table />
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
