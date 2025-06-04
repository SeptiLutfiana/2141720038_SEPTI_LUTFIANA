@extends('layouts.app')

@section('title', 'Bank IDP')
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Bank Individual Development Plan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('karyawan.dashboard-karyawan') }}">Dashboard</a>
                    </div>
                    <div class="breadcrumb-item">Bank IDP</div>
                </div>
            </div>
            <div class="row">
                @if ($idps->isEmpty())
                    <div class="col-12">
                        <div class="alert alert-primary text-center">
                            <strong>Belum tersedia IDP</strong> untuk jenjang dan learning group Anda saat ini.
                        </div>
                    </div>
                @else
                    @foreach ($idps as $idp)
                        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                            <article class="article">
                                <div class="article-header">
                                    <div class="article-image" data-background="{{ asset('./img/bank-idp.png') }}"></div>
                                    <div class="article-title">
                                        <h2 class="text-truncate" style="max-width: 100%">
                                            <a href="#">{{ $idp->proyeksi_karir }}</a>
                                        </h2>
                                    </div>
                                </div>
                                <div class="article-details">
                                    <p class="text-center">
                                        <strong>{{ Str::limit($idp->deskripsi_idp, 100) }}</strong>
                                    </p>
                                    <div class="article-cta text-center">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#modalIdp{{ $idp->id_idp }}">
                                            Daftar
                                        </button>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                @endif
            </div>
        </section>
    </div>
    <!-- Modal di luar loop untuk menghindari duplikasi -->
    @foreach ($idps as $idp)
        <div class="modal fade" id="modalIdp{{ $idp->id_idp }}" tabindex="-1"
            aria-labelledby="modalIdpLabel{{ $idp->id_idp }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="{{ route('karyawan.IDP.applyBankIdp', $idp->id_idp) }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_idp_template" value="{{ $idp->id_idp }}">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalIdpLabel{{ $idp->id_idp }}">
                                Daftar IDP: {{ $idp->proyeksi_karir }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Informasi IDP</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Deskripsi:</strong><br>{{ $idp->deskripsi_idp }}</p>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Jenjang:</strong><br>{{ $idp->jenjang->nama_jenjang ?? '-' }}
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Learning
                                                    Group:</strong><br>{{ $idp->learningGroup->nama_LG ?? '-' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Supervisor:</strong><br>{{ $idp->supervisor->name ?? '-' }}
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Kuota
                                                    Tersedia:</strong><br>{{ $idp->max_applies - ($idp->current_applies ?? 0) }}
                                            </p>
                                        </div>
                                    </div>
                                    <p><strong>Daftar Kompetensi</p>
                                    <p><strong>Hard Kompetensi</p>
                                    {{-- Accordion Kompetensi ditempatkan di sini, tepat di atas form mentor --}}
                                    @foreach ($idp->idpKompetensis->where('kompetensi.jenis_kompetensi', 'Hard Kompetensi') as $index => $kom)
                                        <button class="accordion-button text-start w-100 d-flex align-items-center"
                                            onclick="toggleAccordion(this)"
                                            style="border: none; background: none; padding: 0;">
                                            <span class="accordion-icon me-2">›</span>
                                            <span class="kompetensi-nama">
                                                {{ $kom->kompetensi->nama_kompetensi }}
                                            </span>
                                        </button>
                                        <div class="accordion-content ps-4 mt-2" style="display: none;">
                                            <p><span>{{ $kom->kompetensi->keterangan }}</span></p>
                                            <p><strong>Metode Belajar:</strong>
                                                @foreach ($kom->metodeBelajars as $metode)
                                                    <span class="badge badge-info">{{ $metode->nama_metodeBelajar }}</span>
                                                @endforeach
                                            </p>
                                            <p><strong>Sasaran:</strong> <br>{!! nl2br(e($kom->sasaran)) !!}</p>
                                            <p><strong>Aksi:</strong> <br>{!! nl2br(e($kom->aksi)) !!}</p>
                                        </div>
                                    @endforeach
                                    <br>
                                    <p><strong>Soft Kompetensi</p>
                                    {{-- Accordion Kompetensi ditempatkan di sini, tepat di atas form mentor --}}
                                    @foreach ($idp->idpKompetensis->where('kompetensi.jenis_kompetensi', 'Soft Kompetensi') as $index => $kom)
                                        <button class="accordion-button text-start w-100 d-flex align-items-center"
                                            onclick="toggleAccordion(this)"
                                            style="border: none; background: none; padding: 0;">
                                            <span class="accordion-icon me-2">›</span>
                                            <p></p><span class="kompetensi-nama">
                                                {{ $kom->kompetensi->nama_kompetensi }}
                                            </span></p>
                                        </button>
                                        <div class="accordion-content ps-4 mt-2" style="display: none;">
                                            <p><span>{{ $kom->kompetensi->keterangan }}</span></p>
                                            <p><strong>Metode Belajar:</strong>
                                                @foreach ($kom->metodeBelajars as $metode)
                                                    <span class="badge badge-info">{{ $metode->nama_metodeBelajar }}</span>
                                                @endforeach
                                            </p>
                                            <p><strong>Sasaran:</strong> <br>{!! nl2br(e($kom->sasaran)) !!}</p>
                                            <p><strong>Aksi:</strong> <br>{!! nl2br(e($kom->aksi)) !!}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label for="mentorSelect{{ $idp->id }}" class="form-label">
                                    <strong>Pilih Mentor <span class="text-danger">*</span></strong>
                                </label>
                                <select class="form-control" name="id_mentor" id="mentorSelect{{ $idp->id }}"
                                    required>
                                    <option value="">-- Pilih Mentor --</option>
                                    @foreach ($mentors as $mentor)
                                        <option value="{{ $mentor->id }}">{{ $mentor->name }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Pilih mentor yang akan membimbing Anda dalam
                                    program IDP ini.</small>
                            </div>

                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle"></i>
                                <strong>Perhatian:</strong> Pastikan Anda telah memilih mentor sebelum mendaftar.
                                Pendaftaran tidak dapat dibatalkan setelah dikonfirmasi.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i> Batal
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check"></i> Ya, Daftar Sekarang
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Swal success alert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('msg-success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: @json(session('msg-success')),
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'swal2-confirm-green'
                    }
                });
            @endif

            @if (session('msg-error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: @json(session('msg-error')),
                    confirmButtonText: 'Tutup',
                    customClass: {
                        confirmButton: 'swal2-confirm-green'
                    }
                });
            @endif
        });
    </script>
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
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form[action="#"]');

            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const mentorId = form.querySelector('select[name="mentor_id"]').value;

                    if (!mentorId) {
                        e.preventDefault();
                        alert('Silakan pilih mentor terlebih dahulu!');
                        return false;
                    }

                    if (!confirm('Apakah Anda yakin ingin mendaftar untuk program IDP ini?')) {
                        e.preventDefault();
                        return false;
                    }
                });
            });

            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.addEventListener('shown.bs.modal', function() {
                    modal.querySelector('select[name="mentor_id"]').value = '';
                });
            });
        });
    </script>
@endpush
