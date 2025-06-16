@extends('layouts.app')

@section('title', 'Detail IDP Karyawan')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Detail IDP Karyawan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('karyawan.dashboard-karyawan') }}">Dashboard</a>
                    </div>
                    <div class="breadcrumb-item"><a href="{{ route('karyawan.IDP.indexKaryawan') }}">Data IDP</a></div>
                    <div class="breadcrumb-item">Detail IDP</div>
                </div>
            </div>

            <div class="section-body">
                <div class="card">
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

                            <div class="form-group col-md-12">
                                <label>Status Approval Mentor</label>
                                <input readonly type="text" class="form-control"
                                    value="{{ $idps->status_approval_mentor }}">
                            </div>

                            <div class="form-group col-md-12">
                                <label>Status Pengajuan IDP</label>
                                <input readonly type="text" class="form-control"
                                    value="{{ $idps->status_pengajuan_idp }}">
                            </div>

                            <div class="form-group col-md-12">
                                <label>Status Pengerjaan IDP</label>
                                <input readonly type="text" class="form-control" value="{{ $idps->status_pengerjaan }}">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Saran Pengajuan IDP</label>
                                <textarea readonly class="form-control" style="height:6rem;">{{ $idps->saran_idp }}</textarea>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Daftar Kompetensi</label> <br>
                                <label> Soft Kompetensi</label>
                                @foreach ($idps->idpKompetensis->where('kompetensi.jenis_kompetensi', 'Soft Kompetensi') as $kom)
                                    <div class="accordion border-bottom mb-2 pb-2">
                                        <button class="accordion-button text-start w-100 d-flex align-items-center"
                                            onclick="toggleAccordion(this)"
                                            style="border: none; background: none; padding: 0;">
                                            <span class="accordion-icon me-2 bi bi-chevron-right"></span>
                                            <span class="kompetensi-nama">{{ $kom->kompetensi->nama_kompetensi }} -
                                                (Kompetensi {{ $kom->peran }})</span>
                                        </button>

                                        <div class="accordion-content ps-4 mt-2" style="display: none;">
                                            <p><span>{{ $kom->kompetensi->keterangan }}</span></p>
                                            <p><strong>Metode Belajar:</strong>
                                                @foreach ($kom->metodeBelajars as $metode)
                                                    <span class="badge badge-info">{{ $metode->nama_metodeBelajar }}</span>
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
                                            <span class="accordion-icon me-2 bi bi-chevron-right"></span>
                                            <span class="kompetensi-nama">{{ $kom->kompetensi->nama_kompetensi }}</span>
                                        </button>

                                        <div class="accordion-content ps-4 mt-2" style="display: none;">
                                            <p><span>{{ $kom->kompetensi->keterangan }}</span></p>
                                            <p><strong>Metode Belajar:</strong>
                                                @foreach ($kom->metodeBelajars as $metode)
                                                    <span class="badge badge-info">{{ $metode->nama_metodeBelajar }}</span>
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
                        <a class="btn btn-primary" href="{{ route('karyawan.IDP.indexKaryawan') }}">Kembali</a>
                    </div>
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
                icon.classList.remove('bi-chevron-right');
                icon.classList.add('bi-chevron-down');
            } else {
                content.style.display = "none";
                icon.classList.remove('bi-chevron-down');
                icon.classList.add('bi-chevron-right');
            }
        }
    </script>

@endsection
