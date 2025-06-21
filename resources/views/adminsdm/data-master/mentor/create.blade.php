@extends('layouts.app')

@section('title', 'Halaman Tambah Data Mentor')
@push('style')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
@endpush


@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Tambah Data Mentor</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.data-master.mentor.index') }}">Data
                            Mentor</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('adminsdm.data-master.mentor.create') }}">Tambah Data
                            Mentor</a></div>
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
                        <h4>Tambah Data Mentor</h4>
                    </div>
                    <form action="{{ route('adminsdm.data-master.mentor.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="id_user">Pilih Karyawan Sebagai Mentor:</label>
                                <select name="id_user" id="id_user" class="tom-select" required>
                                    <option value="">-- Pilih Karyawan --</option>
                                    @foreach ($mentor as $user)
                                        <option value="{{ $user->id }}" data-npk="{{ $user->npk }}"
                                            data-jenjang="{{ $user->jenjang->nama_jenjang ?? '-' }}"
                                            data-jabatan="{{ $user->jabatan->nama_jabatan ?? '-' }}"
                                            data-divisi="{{ $user->divisi->nama_divisi ?? '-' }}"
                                            data-penempatan="{{ $user->penempatan->nama_penempatan ?? '-' }}"
                                            data-lg="{{ $user->learninggroup->nama_LG ?? '-' }}">
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="card-footer text-right">
                                <button type="submit" class="btn btn-primary">Tambah Mentor</button>
                            </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        new TomSelect('#id_user', {
            plugins: ['dropdown_input'],
            create: false,
            allowEmptyOption: true,
            render: {
                option: function(data, escape) {
                    return `
                    <div>
                        <div><strong>${escape(data.text)}</strong></div>
                        <div style="font-size: 12px; color: #6c757d;">
                            NPK: ${escape(data.npk)} | Jenjang: ${escape(data.jenjang)} | Jabatan: ${escape(data.jabatan)}<br>
                            Divisi: ${escape(data.divisi)} | Penempatan: ${escape(data.penempatan)} | LG: ${escape(data.lg)}
                        </div>
                    </div>
                `;
                }
            },
            onInitialize: function() {
                this.options = [...this.options].map(opt => ({
                    ...opt,
                    npk: opt.$option.dataset.npk,
                    jenjang: opt.$option.dataset.jenjang,
                    jabatan: opt.$option.dataset.jabatan,
                    divisi: opt.$option.dataset.divisi,
                    penempatan: opt.$option.dataset.penempatan,
                    lg: opt.$option.dataset.lg
                }));
            }
        });
    </script>
@endpush
