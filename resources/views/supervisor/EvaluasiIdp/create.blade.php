@extends('layouts.app')

@section('title', 'Isi Evaluasi Mentor')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Evaluasi {{ ucfirst($jenisEvaluasi) }} Sebagai Mentor</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active">
                        <a href="{{ route('mentor.dashboard-mentor') }}">Dashboard</a>
                    </div>
                    <div class="breadcrumb-item active">
                        <a href="{{ route('supervisor.EvaluasiIdp.indexSpv') }}">Data Evaluasi</a>
                    </div>
                    <div class="breadcrumb-item">Kerjakan Evaluasi IDP</div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>Evaluasi Pasca IDP oleh Supervisor</h4>
                </div>

                <form action="{{ route('supervisor.EvaluasiIdp.storeSpv') }}" method="POST">
                    @csrf
                    <div class="card-body">

                        <input type="hidden" name="id_idp" value="{{ $id_idp }}">
                        <input type="hidden" name="id_user" value="{{ $id_user }}"> {{-- ID mentor --}}
                        <input type="hidden" name="jenis_evaluasi" value="{{ $jenisEvaluasi }}">

                        @foreach ($pertanyaans as $pertanyaan)
                            <div class="card shadow-sm mb-4">
                                <div class="card-body">
                                    <h6 class="mb-3">{{ $loop->iteration }}. {{ $pertanyaan->pertanyaan }}</h6>

                                    @if ($pertanyaan->tipe_pertanyaan === 'likert')
                                        <div class="d-flex justify-content-between text-center">
                                            @php
                                                $labels = [
                                                    1 => 'Sangat Tidak Setuju',
                                                    2 => 'Tidak Setuju',
                                                    3 => 'Netral',
                                                    4 => 'Setuju',
                                                    5 => 'Sangat Setuju',
                                                ];
                                            @endphp

                                            @for ($i = 1; $i <= 5; $i++)
                                                <div class="flex-fill px-1">
                                                    <label class="d-block">
                                                        <input type="radio"
                                                            name="jawaban_likert[{{ $pertanyaan->id_bank_evaluasi }}]"
                                                            value="{{ $i }}" required>
                                                        <div class="mt-1 font-weight-bold">{{ $i }}</div>
                                                        <div class="small text-muted">{{ $labels[$i] }}</div>
                                                    </label>
                                                </div>
                                            @endfor
                                        </div>
                                    @else
                                        <textarea name="jawaban_esai[{{ $pertanyaan->id_bank_evaluasi }}]" class="form-control mt-2" rows="3"
                                            placeholder="Tulis jawaban Anda..." required style="height:6rem;"></textarea>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary px-4">Kirim Evaluasi</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
