@extends('layouts.app')

@section('title', 'Halaman Edit Pertanyaan Evaluasi')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Pertanyaan Evaluasi</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.BankEvaluasi.index') }}">Data Evaluasi</a>
                    </div>
                    <div class="breadcrumb-item">Edit Pertanyaan Evaluasi</div>
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
                        <h4>Edit Pertanyaan Evaluasi</h4>
                    </div>
                    <form action="{{ route('adminsdm.BankEvaluasi.update', $bankEvaluasi->id_bank_evaluasi) }}"
                        method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">

                            <div class="form-group">
                                <label>Jenis Evaluasi</label>
                                <input type="hidden" name="jenis_evaluasi" value="pasca">
                                <input type="text" class="form-control" value="Pasca" disabled>
                            </div>
                            <div class="form-group">
                                <label for="untuk_role">Pertanyaan Untuk Role</label>
                                <select name="untuk_role" id="untuk_role" class="form-control" required>
                                    <option value="">-- Pilih Role --</option>
                                    <option value="karyawan"
                                        {{ $bankEvaluasi->untuk_role == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                                    <option value="mentor" {{ $bankEvaluasi->untuk_role == 'mentor' ? 'selected' : '' }}>
                                        Mentor</option>
                                    <option value="supervisor"
                                        {{ $bankEvaluasi->untuk_role == 'supervisor' ? 'selected' : '' }}>Supervisor
                                    </option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="tipe_pertanyaan">Tipe Pertanyaan</label>
                                <select name="tipe_pertanyaan" id="tipe_pertanyaan" class="form-control" required>
                                    <option value="">-- Pilih Tipe Pertanyaan --</option>
                                    <option value="likert"
                                        {{ $bankEvaluasi->tipe_pertanyaan == 'likert' ? 'selected' : '' }}>Likert (Skala)
                                    </option>
                                    <option value="esai" {{ $bankEvaluasi->tipe_pertanyaan == 'esai' ? 'selected' : '' }}>
                                        Esai</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="pertanyaan">Isi Pertanyaan</label>
                                <textarea name="pertanyaan" id="pertanyaan" rows="3" class="form-control" required>{{ old('pertanyaan', $bankEvaluasi->pertanyaan) }}</textarea>
                            </div>

                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
    </div>
    </section>
    </div>
@endsection
