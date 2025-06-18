@extends('layouts.app')

@section('title', 'Detail Profil')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Profile Account</h1>
            </div>

            <div class="section-body">
                <div class="card">
                    <div class="card-body">

                        {{-- Nav Tabs --}}
                        <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info"
                                    role="tab">Personal
                                    Info</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="password-tab" data-toggle="tab" href="#password"
                                    role="tab">Change
                                    Password</a>
                            </li>
                        </ul>

                        <div class="tab-content" id="profileTabContent">
                            {{-- Tab 1: Personal Info --}}
                            <div class="tab-pane fade show active" id="info" role="tabpanel">
                                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PATCH')

                                    <div class="row">
                                        {{-- Kiri: Form --}}
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label>NPK</label>
                                                <input type="text" class="form-control" value="{{ $user->npk ?? '-' }}"
                                                    readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Nama Lengkap</label>
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ old('name', $user->name) }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" name="email" class="form-control"
                                                    value="{{ old('email', $user->email) }}">
                                            </div>
                                            <div class="form-group">
                                                <label>No. Handphone</label>
                                                <input type="text" name="no_hp" class="form-control"
                                                    value="{{ old('no_hp', $user->no_hp) }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Jenjang</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $user->jenjang->nama_jenjang ?? '-' }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Jabatan</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $user->jabatan->nama_jabatan ?? '-' }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Divisi</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $user->divisi->nama_divisi ?? '-' }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Penempatan</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $user->penempatan->nama_penempatan ?? '-' }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Learning Group</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $user->learninggroup->nama_LG ?? '-' }}" readonly>
                                            </div>
                                            <div class="form-group d-flex gap-2">
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                                                    Simpan</button>
                                                <a href="{{ url()->previous() }}" class="btn btn-warning"><i
                                                        class="fas fa-arrow-left"></i> Kembali</a>
                                            </div>
                                        </div>

                                        {{-- Kanan: Foto Profil --}}
                                        <div
                                            class="col-md-4 d-flex flex-column align-items-center align-self-start mt-md-0 mt-4">
                                            @if ($user->foto_profile)
                                                <img id="preview"
                                                    src="{{ $user->foto_profile ? asset('storage/' . $user->foto_profile) : '#' }}"
                                                    class="rounded-circle border mb-3"
                                                    style="width: 140px; height: 140px; object-fit: cover; object-position: center; {{ $user->foto_profile ? '' : 'display: none;' }}">
                                            @else
                                                <img id="preview"
                                                    src="{{ $user->foto_profile ? asset('storage/' . $user->foto_profile) : '#' }}"
                                                    class="rounded-circle border mb-3"
                                                    style="width: 140px; height: 140px; object-fit: cover; object-position: center; {{ $user->foto_profile ? '' : 'display: none;' }}">
                                            @endif

                                            <input type="file" name="foto_profile" id="foto_profile" class="d-none"
                                                onchange="previewImage(event)">
                                            <button type="button" class="btn btn-warning"
                                                onclick="document.getElementById('foto_profile').click();">
                                                Change Photo
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            {{-- Tab 2: Change Password --}}
                            <div class="tab-pane fade" id="password" role="tabpanel">
                                <div class="card">
                                    <div class="card-body">
                                        @include('profile.partials.update-password-form')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>{{-- Preview Script & SweetAlert --}}
    @push('scripts')
        {{-- SweetAlert --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        {{-- Preview Image --}}
        <script>
            function previewImage(event) {
                const reader = new FileReader();
                reader.onload = function() {
                    document.getElementById('preview').src = reader.result;
                };
                reader.readAsDataURL(event.target.files[0]);
            }

            @if (session('status') === 'profile-updated')
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Profil Anda telah diperbarui.',
                    timer: 2500,
                    showConfirmButton: false
                });
            @endif
        </script>
    @endpush
@endsection
