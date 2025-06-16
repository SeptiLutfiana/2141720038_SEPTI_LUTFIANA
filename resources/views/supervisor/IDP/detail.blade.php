@extends('layouts.app')

@section('title', 'Detail IDP Karyawan')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Detail IDP Karyawan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('supervisor.spv-dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('supervisor.IDP.indexSupervisor') }}">Data IDP</a></div>
                    <div class="breadcrumb-item">Detail IDP</div>
                </div>
            </div>

            <div class="section-body">
                <div class="card">
                    <div class="card-body">
                        <h4>Informasi IDP - {{ $idps->karyawan->name }}</h4>
                        <small class="text-muted d-block mt-1">
                            Jenjang: {{ $idps->jenjang->nama_jenjang ?? '-' }} |
                            Jabatan: {{ $idps->jabatan->nama_jabatan ?? '-' }} |
                            Divisi: {{ $idps->divisi->nama_divisi ?? '-' }} |
                            Penempatan: {{ $idps->penempatan->nama_penempatan ?? '-' }} | <br>
                            Learning Group: {{ $idps->learninggroup->nama_LG ?? '-' }} |
                            Semester: {{ $idps->semester->nama_semester ?? '-' }} |
                            Angkatan PSP:
                            {{ $idps->angkatanpsp->bulan ?? '-' }} {{ $idps->angkatanpsp->tahun ?? '-' }}
                        </small>
                        <br>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>Proyeksi Karir</label>
                                <input readonly type="text" class="form-control" value="{{ $idps->proyeksi_karir }}">
                            </div>

                            <div class="form-group col-md-12">
                                <label>Deskripsi</label>
                                <textarea readonly type="text" class="form-control"style="height:6rem;">{{ $idps->deskripsi_idp }}</textarea>
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
                                <textarea readonly type="text" class="form-control" style="height:4rem;">{{ $idps->saran_idp }}</textarea>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Daftar Kompetensi</label> <br>
                                <label> Soft Kompetensi</label>
                                @foreach ($idps->idpKompetensis->where('kompetensi.jenis_kompetensi', 'Soft Kompetensi') as $kom)
                                    <div class="accordion border-bottom mb-2 pb-2">
                                        @php
                                            $sudahDiratingSemua = $kom->pengerjaans->every(function ($peng) {
                                                return $peng->nilaiPengerjaanIdp &&
                                                    $peng->nilaiPengerjaanIdp->rating !== null;
                                            });

                                            $statusText = $sudahDiratingSemua
                                                ? 'Sudah Dinilai Supervisor'
                                                : 'Menunggu Rating Supervisor';
                                            $statusColor = $sudahDiratingSemua ? '#3b82f6' : '#f59e0b';
                                        @endphp

                                        <button class="accordion-button text-start w-100 d-flex align-items-center"
                                            onclick="toggleAccordion(this)"
                                            style="border: none; background: none; padding: 0;">
                                            <span class="accordion-icon me-2">›</span>
                                            <span class="kompetensi-nama">
                                                {{ $kom->kompetensi->nama_kompetensi }}

                                                <span
                                                    style="padding: 3px 8px;
                                                        border-radius: 12px;
                                                        color: white;
                                                        font-weight: 600;
                                                        background-color: {{ $statusColor }};">
                                                    {{ $statusText }}
                                                </span>
                                            </span>
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
                                            <p><strong>Riwayat Upload Implementasi (Hasil)</strong></p>
                                            <table class="table table-bordered table-sm">
                                                <thead class="table-light">
                                                    <tr class="text-center">
                                                        <th width="2%">No</th>
                                                        <th width="5%">File</th>
                                                        <th width="28%">Keterangan</th>
                                                        <th width="10%">Tanggal Upload</th>
                                                        <th width="15%">Rating Kompetensi</th>
                                                        <th width="15%">Saran Supervisor</th>
                                                        <th width="15%">Aksi</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($kom->pengerjaans as $index => $peng)
                                                        @php
                                                            $ext = strtolower(
                                                                pathinfo($peng->upload_hasil, PATHINFO_EXTENSION),
                                                            );
                                                            $icon = match ($ext) {
                                                                'pdf' => 'bi bi-file-earmark-pdf-fill text-danger',
                                                                'doc',
                                                                'docx'
                                                                    => 'bi bi-file-earmark-word-fill text-primary',
                                                                'xls',
                                                                'xlsx'
                                                                    => 'bi bi-file-earmark-excel-fill text-success',
                                                                'jpg',
                                                                'jpeg',
                                                                'png'
                                                                    => 'bi bi-file-earmark-image-fill text-warning',
                                                                'mp4' => 'bi bi-file-earmark-play-fill text-dark',
                                                                default => 'bi bi-file-earmark-fill',
                                                            };
                                                            $fileUrl = asset('storage/' . $peng->upload_hasil);
                                                            $isPreviewable = in_array($ext, [
                                                                'pdf',
                                                                'jpg',
                                                                'jpeg',
                                                                'png',
                                                                'mp4',
                                                            ]);
                                                        @endphp
                                                        <tr>
                                                            <td class="text-center">{{ $loop->iteration }}</td>
                                                            <td class="text-center">
                                                                @if ($isPreviewable)
                                                                    {{-- File bisa dibuka langsung --}}
                                                                    <a href="{{ $fileUrl }}" target="_blank"
                                                                        title="Lihat file">
                                                                        <i class="{{ $icon }}"
                                                                            style="font-size: 1.5rem;"></i>
                                                                    </a>
                                                                @else
                                                                    {{-- File harus didownload --}}
                                                                    <a href="{{ $fileUrl }}" download
                                                                        title="Download file">
                                                                        <i class="{{ $icon }}"
                                                                            style="font-size: 1.5rem;"></i>
                                                                    </a>
                                                                @endif
                                                            </td>
                                                            <td>{{ $peng->keterangan_hasil ?? '-' }}</td>
                                                            <td class="text-center">
                                                                {{ $peng->created_at->format('d-m-Y') }}</td>
                                                            <td class="text-center">
                                                                {{ $peng->nilaiPengerjaanIdp->rating ?? '-' }}</td>
                                                            <td class="text-center">
                                                                {{ $peng->nilaiPengerjaanIdp->saran ?? '-' }}</td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-primary btn-nilai"
                                                                    data-toggle="modal" data-target="#nilaiModal"
                                                                    data-id="{{ $peng->id_idpKomPeng }}"
                                                                    data-kompetensi="{{ $peng->idpKompetensi->kompetensi->nama_kompetensi }}"
                                                                    data-rating="{{ $peng->nilaiPengerjaanIdp->rating ?? '' }}"
                                                                    data-saran="{{ $peng->nilaiPengerjaanIdp->saran ?? '' }}">
                                                                    <i class="bi bi-pencil-square"></i> Nilai
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-group col-md-12">
                                <label> Hard Kompetensi</label>
                                @foreach ($idps->idpKompetensis->where('kompetensi.jenis_kompetensi', 'Hard Kompetensi') as $kom)
                                    <div class="accordion border-bottom mb-2 pb-2">
                                        @php
                                            $sudahDiratingSemua = $kom->pengerjaans->every(function ($peng) {
                                                return $peng->nilaiPengerjaanIdp &&
                                                    $peng->nilaiPengerjaanIdp->rating !== null;
                                            });

                                            $statusText = $sudahDiratingSemua
                                                ? 'Sudah Dinilai Supervisor'
                                                : 'Menunggu Rating Supervisor';
                                            $statusColor = $sudahDiratingSemua ? '#3b82f6' : '#f59e0b';
                                        @endphp

                                        <button class="accordion-button text-start w-100 d-flex align-items-center"
                                            onclick="toggleAccordion(this)"
                                            style="border: none; background: none; padding: 0;">
                                            <span class="accordion-icon me-2">›</span>
                                            <span class="kompetensi-nama">
                                                {{ $kom->kompetensi->nama_kompetensi }}

                                                <span
                                                    style="padding: 3px 8px;
                                                        border-radius: 12px;
                                                        color: white;
                                                        font-weight: 600;
                                                        background-color: {{ $statusColor }};">
                                                    {{ $statusText }}
                                                </span>
                                            </span>
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
                                            <p><strong>Riwayat Upload Implementasi (Hasil)</strong></p>
                                            <table class="table table-bordered table-sm">
                                                <thead class="table-light">
                                                    <tr class="text-center">
                                                        <th width="2%">No</th>
                                                        <th width="5%">File</th>
                                                        <th width="28%">Keterangan</th>
                                                        <th width="10%">Tanggal Upload</th>
                                                        <th width="15%">Rating Kompetensi</th>
                                                        <th width="15%">Saran Supervisor</th>
                                                        <th width="15%">Aksi</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($kom->pengerjaans as $index => $peng)
                                                        @php
                                                            $ext = strtolower(
                                                                pathinfo($peng->upload_hasil, PATHINFO_EXTENSION),
                                                            );
                                                            $icon = match ($ext) {
                                                                'pdf' => 'bi bi-file-earmark-pdf-fill text-danger',
                                                                'doc',
                                                                'docx'
                                                                    => 'bi bi-file-earmark-word-fill text-primary',
                                                                'xls',
                                                                'xlsx'
                                                                    => 'bi bi-file-earmark-excel-fill text-success',
                                                                'jpg',
                                                                'jpeg',
                                                                'png'
                                                                    => 'bi bi-file-earmark-image-fill text-warning',
                                                                'mp4' => 'bi bi-file-earmark-play-fill text-dark',
                                                                default => 'bi bi-file-earmark-fill',
                                                            };
                                                            $fileUrl = asset('storage/' . $peng->upload_hasil);
                                                            $isPreviewable = in_array($ext, [
                                                                'pdf',
                                                                'jpg',
                                                                'jpeg',
                                                                'png',
                                                                'mp4',
                                                            ]);
                                                        @endphp
                                                        <tr>
                                                            <td class="text-center">{{ $loop->iteration }}</td>
                                                            <td class="text-center">
                                                                @if ($isPreviewable)
                                                                    {{-- File bisa dibuka langsung --}}
                                                                    <a href="{{ $fileUrl }}" target="_blank"
                                                                        title="Lihat file">
                                                                        <i class="{{ $icon }}"
                                                                            style="font-size: 1.5rem;"></i>
                                                                    </a>
                                                                @else
                                                                    {{-- File harus didownload --}}
                                                                    <a href="{{ $fileUrl }}" download
                                                                        title="Download file">
                                                                        <i class="{{ $icon }}"
                                                                            style="font-size: 1.5rem;"></i>
                                                                    </a>
                                                                @endif
                                                            </td>
                                                            <td>{{ $peng->keterangan_hasil ?? '-' }}</td>
                                                            <td class="text-center">
                                                                {{ $peng->created_at->format('d-m-Y') }}</td>
                                                            <td class="text-center"
                                                                id="rating-{{ $peng->id_idpKomPeng }}">
                                                                {{ $peng->nilaiPengerjaanIdp->rating ?? 'belum dinilai' }}
                                                            </td>
                                                            <td class="text-center"
                                                                id="saran-{{ $peng->id_idpKomPeng }}">
                                                                {{ $peng->nilaiPengerjaanIdp->saran ?? 'telum ada saran' }}
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-primary btn-nilai"
                                                                    data-toggle="modal" data-target="#nilaiModal"
                                                                    data-id="{{ $peng->id_idpKomPeng }}"
                                                                    data-kompetensi="{{ $peng->idpKompetensi->kompetensi->nama_kompetensi }}"
                                                                    data-rating="{{ $peng->nilaiPengerjaanIdp->rating ?? '' }}"
                                                                    data-saran="{{ $peng->nilaiPengerjaanIdp->saran ?? '' }}">
                                                                    <i class="bi bi-pencil-square"></i> Nilai
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <a class="btn btn-primary" href="{{ route('supervisor.IDP.indexSupervisor') }}">Kembali</a>
                    </div>
                </div>
            </div>
    </div>
    </div>
    <!-- Modal Penilaian -->
    <div class="modal fade" id="nilaiModal" tabindex="-1" role="dialog" aria-labelledby="nilaiModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="nilaiForm" method="POST" action="{{ route('supervisor.IDP.store') }}">
                @csrf
                <input type="hidden" name="id_idpKomPeng" id="id_idpKomPeng_input">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-center" id="nilaiModalLabel">Penilaian Rating Implementasi IDP oleh
                            Supervisor
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @php
                            $statuses = [
                                '1' => 'Kurang',
                                '2' => 'Perlu Peningkatan',
                                '3' => 'Cukup',
                                '4' => 'Baik',
                                '5' => 'Sangat Baik',
                            ];
                        @endphp

                        <style>
                            .rating-container {
                                display: flex;
                                justify-content: center;
                                gap: 1rem;
                            }

                            .rating-item {
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                font-size: 0.85rem;
                            }

                            .rating-item label {
                                font-weight: bold;
                                margin-bottom: 0.25rem;
                            }

                            .rating-desc {
                                font-size: 0.7rem;
                                color: #6c757d;
                                margin-top: 0.25rem;
                            }

                            .btn-check {
                                display: none;
                            }

                            .btn-rating {
                                border: 2px solid #83B92C;
                                border-radius: 50%;
                                width: 40px;
                                height: 40px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-weight: 500;
                                color: #83B92C;
                                cursor: pointer;
                                transition: all 0.2s ease-in-out;
                            }

                            .btn-check:checked+.btn-rating {
                                background-color: #83B92C;
                                color: white;
                            }
                        </style>

                        <div class="text-center mb-3">
                            <label class="fw-bold mb-2 d-block">Pilih Nilai Rating:</label>
                            <div class="rating-container">
                                @foreach ($statuses as $value => $label)
                                    <div class="rating-item">
                                        <input type="radio" class="btn-check" name="rating"
                                            id="rating{{ $value }}" value="{{ $value }}">
                                        <label class="btn-rating"
                                            for="rating{{ $value }}">{{ $value }}</label>
                                        <div class="rating-desc">{{ $label }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="invalid-feedback d-block" id="error-rating"></div>
                        <div class="form-group">
                            <label for="saran">Saran</label>
                            <textarea name="saran" id="saran" class="form-control @error('saran') is-invalid @enderror" rows="3"
                                style="height:6rem;">{{ old('saran') }}</textarea>
                            <div class="invalid-feedback d-block" id="error-saran"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </section>
    </div>
@endsection
@push('scripts')
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
            const nilaiModal = document.getElementById('nilaiModal');
            const form = document.getElementById('nilaiForm');
            const modalTitle = nilaiModal.querySelector('.modal-title');
            const saranInput = document.getElementById('saran');

            const statusRadios = nilaiModal.querySelectorAll('input[name="rating"]');

            // Ketika modal dibuka lewat tombol "Nilai"
            document.querySelectorAll('.btn-nilai').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const kompetensi = this.getAttribute('data-kompetensi');
                    const rating = this.getAttribute('data-rating');
                    const saran = this.getAttribute('data-saran');

                    document.getElementById('id_idpKomPeng_input').value = id;

                    // Set rating
                    document.querySelectorAll('input[name="rating"]').forEach(radio => {
                        radio.checked = (radio.value === rating);
                    });

                    // Set saran
                    document.getElementById('saran').value = saran || '';
                });
            });

        });
    </script>
    <script>
        $(document).ready(function() {
        $('#nilaiForm').submit(function(e) {
            e.preventDefault();

            // Hapus error sebelumnya
            $('#error-rating').text('');
            $('#error-saran').text('');

            var formData = $(this).serialize();

            $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            $('#nilaiModal').modal('hide'); // Tutup modal

                            // Tampilkan alert sukses
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message, // ← ini dari controller kamu
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }).then(() => {
                        // Setelah alert selesai → reload halaman
                        location.reload();
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.rating) {
                            $('#error-rating').text(errors.rating[0]);
                        }
                        if (errors.saran) {
                            $('#error-saran').text(errors.saran[0]);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan. Silakan coba lagi.',
                        });
                    }
                }
        });
        });
        });
    </script>
@endpush
