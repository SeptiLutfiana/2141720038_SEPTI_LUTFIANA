@extends('layouts.app')

@section('title', 'Halaman Tambah Pertanyaan Evaluasi')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Tambah Pertanyaan Evaluasi</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.BankEvaluasi.index') }}">Data Evaluasi</a>
                    </div>
                    <div class="breadcrumb-item">Tambah Pertanyaan Evalausi</a></div>
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
                        <h4>Tambah Pertanyaan Evaluasi</h4>
                    </div>
                    <form action="{{ route('adminsdm.BankEvaluasi.store') }}" method="POST">
                        @csrf
                        <div class="card-body">

                            <div class="form-group">
                                <label for="jenis_evaluasi">Jenis Evaluasi</label>
                                <select name="jenis_evaluasi" id="jenis_evaluasi" class="form-control" required>
                                    <option value="">-- Pilih Jenis Evaluasi --</option>
                                    <option value="onboarding">Onboarding</option>
                                    <option value="pasca">Pasca</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="untuk_role">Pertanyaan Untuk Role</label>
                                <select name="untuk_role" id="untuk_role" class="form-control" required>
                                    <option value="">-- Pilih Role --</option>
                                    <option value="karyawan">Karyawan</option>
                                    <option value="mentor">Mentor</option>
                                    <option value="supervisor">Supervisor</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="tipe_pertanyaan">Tipe Pertanyaan</label>
                                <select name="tipe_pertanyaan" id="tipe_pertanyaan" class="form-control" required>
                                    <option value="">-- Pilih Tipe Pertanyaan --</option>
                                    <option value="likert">Likert (Skala)</option>
                                    <option value="esai">Esai</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="pertanyaan">Isi Pertanyaan</label>
                                <textarea name="pertanyaan" id="pertanyaan" rows="3" class="form-control" required>{{ old('pertanyaan') }}</textarea>
                            </div>

                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-success">Simpan</button>
                        </div>
                    </form>

                </div>
            </div>
        </section>
    </div>
@endsection
