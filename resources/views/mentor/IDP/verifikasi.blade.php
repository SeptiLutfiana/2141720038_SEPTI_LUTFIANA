@extends('layouts.app')

@section('title', 'Verifikasi IDP Karyawan')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Verifikasi IDP Karyawan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('mentor.dashboard-mentor') }}">Dashboard</a>
                    </div>
                    <div class="breadcrumb-item"><a href="{{ route('mentor.IDP.indexMentor') }}">Data IDP</a></div>
                    <div class="breadcrumb-item">Verifikasi IDP</div>
                </div>
            </div>

            <div class="section-body">
                <div class="card">
                    <form action="{{ route('mentor.IDP.updateVerifikasi', $idps->id_idp) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-header">
                            <h4>Informasi IDP - {{ $idps->karyawan->name }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>Proyeksi Karir</label>
                                    <input readonly type="text" class="form-control" value="{{ $idps->proyeksi_karir }}">
                                </div>

                                <div class="form-group col-md-12">
                                    <label>Deskripsi</label>
                                    <input readonly type="text" class="form-control" value="{{ $idps->deskripsi_idp }}">
                                </div>

                                <div class="form-group col-md-12">
                                    <label>Mentor</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $idps->mentor->name ?? '-' }}">
                                </div>

                                <div class="form-group col-md-12">
                                    <label>Supervisor</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $idps->supervisor->name ?? '-' }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Waktu Mulai</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ \Carbon\Carbon::parse($idps->waktu_mulai)->format('d-m-Y') }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Waktu Selesai</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ \Carbon\Carbon::parse($idps->waktu_selesai)->format('d-m-Y') }}">
                                </div>
                                <!-- Ganti dengan select status approval mentor yang bisa diedit -->
                                <div class="form-group col-md-12">
                                    <label>Status Approval Mentor</label>
                                    <select name="status_approval_mentor" class="form-control"
                                        {{ $idps->status_approval_mentor == 'Disetujui' ? 'disabled' : '' }} required>
                                        <option value="Menunggu Persetujuan"
                                            {{ $idps->status_approval_mentor == 'Menunggu Persetujuan' ? 'selected' : '' }}>
                                            Menunggu Persetujuan
                                        </option>
                                        <option value="Disetujui"
                                            {{ $idps->status_approval_mentor == 'Disetujui' ? 'selected' : '' }}>
                                            Disetujui
                                        </option>
                                        <option value="Ditolak"
                                            {{ $idps->status_approval_mentor == 'Ditolak' ? 'selected' : '' }}>
                                            Ditolak
                                        </option>
                                    </select>
                                    @if ($idps->status_approval_mentor == 'Disetujui')
                                        <input type="hidden" name="status_approval_mentor"
                                            value="{{ $idps->status_approval_mentor }}">
                                    @else
                                        {{-- Kalau belum disetujui, tetap gunakan select untuk input --}}
                                    @endif
                                </div>
                                <!-- Status Pengajuan IDP -->
                                <div class="form-group col-md-12">
                                    <label>Status Pengajuan IDP</label>
                                    <select name="status_pengajuan_idp" class="form-control" required>
                                        <option value="Menunggu Persetujuan"
                                            {{ $idps->status_pengajuan_idp == 'Menunggu Persetujuan' ? 'selected' : '' }}>
                                            Menunggu Persetujuan
                                        </option>
                                        <option value="Revisi"
                                            {{ $idps->status_pengajuan_idp == 'Revisi' ? 'selected' : '' }}>
                                            Revisi
                                        </option>
                                        <option value="Disetujui"
                                            {{ $idps->status_pengajuan_idp == 'Disetujui' ? 'selected' : '' }}>
                                            Disetujui
                                        </option>
                                        <option value="Tidak Disetujui"
                                            {{ $idps->status_pengajuan_idp == 'Tidak Disetujui' ? 'selected' : '' }}>
                                            Tidak Disetujui
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label>Saran Pengajuan IDP</label>
                                    <textarea name="saran_idp"
                                        class="form-control @if (old('saran_idp')) is-valid @endif
                                @error('saran_idp') is-invalid @enderror"
                                        class="form-control" style="height:6rem;">{{ old('saran_idp', $idps->saran_idp) }}</textarea>
                                </div>
                                <div class="form-group col-md-12">
                                    <label>Daftar Kompetensi</label> <br>
                                    <label> Soft Kompetensi</label>
                                    @foreach ($idps->idpKompetensis->where('kompetensi.jenis_kompetensi', 'Soft Kompetensi') as $kom)
                                        <div class="accordion border-bottom mb-2 pb-2">
                                            <button class="accordion-button text-start w-100 d-flex align-items-center"
                                                onclick="toggleAccordion(this)"
                                                style="border: none; background: none; padding: 0;">
                                                <span class="accordion-icon me-2">›</span>
                                                <span
                                                    class="kompetensi-nama">{{ $kom->kompetensi->nama_kompetensi }}</span>
                                            </button>

                                            <div class="accordion-content ps-4 mt-2" style="display: none;">
                                                <p><span>{{ $kom->kompetensi->keterangan }}</span></p>
                                                <p><strong>Metode Belajar:</strong>
                                                    @foreach ($kom->metodeBelajars as $metode)
                                                        <span
                                                            class="badge badge-info">{{ $metode->nama_metodeBelajar }}</span>
                                                    @endforeach
                                                </p>
                                                <p><strong>Sasaran:</strong> <br></span>{!! nl2br(e($kom->sasaran)) !!}</p>
                                                <p><strong>Aksi:</strong> <br> {!! nl2br(e($kom->aksi)) !!}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-group col-md-12">
                                    <label> Hard Kompetensi</label>
                                    @foreach ($idps->idpKompetensis->where('kompetensi.jenis_kompetensi', 'Hard Kompetensi') as $kom)
                                        <div class="accordion border-bottom mb-2 pb-2">
                                            <button class="accordion-button text-start w-100 d-flex align-items-center"
                                                onclick="toggleAccordion(this)"
                                                style="border: none; background: none; padding: 0;">
                                                <span class="accordion-icon me-2">›</span>
                                                <span
                                                    class="kompetensi-nama">{{ $kom->kompetensi->nama_kompetensi }}</span>
                                            </button>

                                            <div class="accordion-content ps-4 mt-2" style="display: none;">
                                                <p><span>{{ $kom->kompetensi->keterangan }}</span></p>
                                                <p><strong>Metode Belajar:</strong>
                                                    @foreach ($kom->metodeBelajars as $metode)
                                                        <span
                                                            class="badge badge-info">{{ $metode->nama_metodeBelajar }}</span>
                                                    @endforeach
                                                </p>
                                                <p><strong>Sasaran:</strong> <br></span>{!! nl2br(e($kom->sasaran)) !!}</p>
                                                <p><strong>Aksi:</strong> <br> {!! nl2br(e($kom->aksi)) !!}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <a href="{{ route('mentor.IDP.indexMentor') }}" class="btn btn-warning mr-2">Kembali</a>
                            <button type="submit" class="btn btn-primary">Simpan Verifikasi</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <script>
        function toggleAccordion(button) {
            const content = button.nextElementSibling;
            const icon = button.querySelector('.accordion-icon');
            if (content.style.display === "none" || content.style.display === "") {
                content.style.display = "block";
                icon.innerHTML = "˅";
            } else {
                content.style.display = "none";
                icon.innerHTML = "›";
            }
        }
    </script>
@endsection
