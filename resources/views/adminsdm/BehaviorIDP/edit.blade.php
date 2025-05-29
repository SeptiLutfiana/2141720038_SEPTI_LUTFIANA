@extends('layouts.app')

@section('title', 'Halaman Edit IDP')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Data IDP</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="{{ route('adminsdm.BehaviorIDP.indexGiven') }}">Data
                            Jabatan</a></div>
                    <div class="breadcrumb-item">Edit Data IDP</div>
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
                    <form action="{{ route('adminsdm.BehaviorIDP.update', $idp->id_idp) }}" method="POST" id="mainForm">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <h4>{{ $idp->karyawan->name }} - {{ $idp->karyawan->npk }}</h4>
                            <small class="text-muted d-block mt-1">
                                Jenjang: {{ $idp->jenjang->nama_jenjang ?? '-' }} |
                                Jabatan: {{ $idp->jabatan->nama_jabatan ?? '-' }} |
                                Divisi: {{ $idp->divisi->nama_divisi ?? '-' }} |
                                Penempatan: {{ $idp->penempatan->nama_penempatan ?? '-' }} | <br>
                                Learning Group: {{ $idp->learninggroup->nama_LG ?? '-' }} |
                                Semester: {{ $idp->semester->nama_semester ?? '-' }} |
                                Angkatan PSP:
                                {{ $idp->angkatanpsp->bulan ?? '-' }} {{ $idp->angkatanpsp->tahun ?? '-' }}
                            </small>
                            <br>

                            <div class="form-group">
                                <label>Proyeksi Karir</label>
                                <input type="text" name="proyeksi_karir"
                                    class="form-control @if (old('proyeksi_karir')) is-valid @endif
                                @error('proyeksi_karir') is-invalid @enderror"
                                    value="{{ old('proyeksi_karir', $idp->proyeksi_karir) }}">
                            </div>
                            <div class="form-group">
                                <label>Deskripsi IDP</label>
                                <textarea name="deskripsi_idp"
                                    class="form-control @if (old('deskripsi_idp')) is-valid @endif
                                @error('deskripsi_idp') is-invalid @enderror"
                                    style="height:8rem;">{{ old('deskripsi_idp', $idp->deskripsi_idp) }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Mentor <span class="text-danger">*</span></label>
                                <select name="id_mentor" class="form-control @error('id_mentor') is-invalid @enderror"
                                    required>
                                    @if (empty($idp->id_mentor))
                                        <option value="">-- Pilih Mentor --</option>
                                    @endif
                                    @foreach ($mentors as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('id_mentor', $idp->id_mentor) == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_mentor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if (!empty($idp->id_mentor))
                                    <small class="text-muted">Mentor saat ini:
                                        {{ $idp->mentor->name ?? 'Data tidak ditemukan' }}</small>
                                @else
                                    <small class="text-warning">Belum ada mentor yang dipilih</small>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Supervisor <span class="text-danger">*</span></label>
                                <select name="id_supervisor"
                                    class="form-control @error('id_supervisor') is-invalid @enderror" required>
                                    @if (empty($idp->id_supervisor))
                                        <option value="">-- Pilih Supervisor --</option>
                                    @endif
                                    @foreach ($supervisors as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('id_supervisor', $idp->id_supervisor) == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_supervisor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if (!empty($idp->id_supervisor))
                                    <small class="text-muted">Supervisor saat ini:
                                        {{ $idp->supervisor->name ?? 'Data tidak ditemukan' }}</small>
                                @else
                                    <small class="text-warning">Belum ada supervisor yang dipilih</small>
                                @endif
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Waktu Mulai</label>
                                    <input type="date" name="waktu_mulai"
                                        class="form-control @if (old('waktu_mulai')) is-valid @endif
                                @error('waktu_mulai') is-invalid @enderror"
                                        value="{{ old('waktu_mulai', $idp->waktu_mulai) }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Waktu Selesai</label>
                                    <input type="date" name="waktu_selesai"
                                        class="form-control @if (old('waktu_selesai')) is-valid @endif
                                @error('waktu_selesai') is-invalid @enderror"
                                        value="{{ old('waktu_selesai', $idp->waktu_selesai) }}">
                                </div>
                            </div>

                            <div id="hiddenKompetensiInputs">
                                @foreach ($idp->idpKompetensis as $kom)
                                    <input type="hidden" name="kompetensi[{{ $kom->id_idpKom }}][id]"
                                        value="{{ $kom->id_idpKom }}">
                                    <input type="hidden" name="kompetensi[{{ $kom->id_idpKom }}][sasaran]"
                                        value="{{ $kom->sasaran }}">
                                    <input type="hidden" name="kompetensi[{{ $kom->id_idpKom }}][aksi]"
                                        value="{{ $kom->aksi }}">

                                    @foreach ($kom->metodeBelajars as $metode)
                                        <input type="hidden" name="kompetensi[{{ $kom->id_idpKom }}][id_metode_belajar][]"
                                            value="{{ $metode->id_metodeBelajar }}">
                                    @endforeach
                                @endforeach

                            </div>
                            <div class="form-group">
                                <label>Daftar Kompetensi</label> <br>
                                <label>Soft Kompetensi</label>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th>Nama Kompetensi</th>
                                            <th style="width: 50px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($idp->idpKompetensis->where('kompetensi.jenis_kompetensi', 'Soft Kompetensi')->values() as $index => $kom)
                                            <tr>
                                                <td style="width: 50px;">{{ $index + 1 }}</td>
                                                <td>{{ $kom->kompetensi->nama_kompetensi }}</td>
                                                <td style="width: 50px;">
                                                    <button type="button" class="btn btn-warning btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalKompetensi{{ $kom->id_idpKom }}">
                                                        {{-- PERBAIKAN DI SINI --}}
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Hard Kompetensi --}}
                            <div class="form-group">
                                <label>Hard Kompetensi</label>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th>Nama Kompetensi</th>
                                            <th style="width: 50px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($idp->idpKompetensis->where('kompetensi.jenis_kompetensi', 'Hard Kompetensi')->values() as $index => $kom)
                                            <tr>
                                                <td style="width: 50px;">{{ $index + 1 }}</td>
                                                <td>{{ $kom->kompetensi->nama_kompetensi }}</td>
                                                <td style="width: 50px;">
                                                    <button type="button" class="btn btn-warning btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalHardKompetensi{{ $kom->id_idpKom }}">
                                                        {{-- PERBAIKAN DI SINI --}}
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    {{-- MODAL SECTION --}}
    @foreach ($idp->idpKompetensis as $kom)
        @php
            $jenis = $kom->kompetensi->jenis_kompetensi;
        @endphp

        <div class="modal fade"
            id="{{ $jenis === 'Soft Kompetensi' ? 'modalKompetensi' . $kom->id_idpKom : 'modalHardKompetensi' . $kom->id_idpKom }}"
            {{-- PERBAIKAN DI SINI --}} tabindex="-1"
            aria-labelledby="{{ $jenis === 'Soft Kompetensi' ? 'modalLabel' . $kom->id_idpKom : 'modalHardLabel' . $kom->id_idpKom }}"
            {{-- PERBAIKAN DI SINI --}} aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="{{ $jenis === 'Soft Kompetensi' ? 'modalLabel' . $kom->id_idpKom : 'modalHardLabel' . $kom->id_idpKom }}">
                            {{-- PERBAIKAN DI SINI --}}
                            Edit {{ $jenis }}: {{ $kom->kompetensi->nama_kompetensi }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Keterangan:</strong> {{ $kom->kompetensi->keterangan }}</p>

                        <div class="form-group">
                            <label><strong>Sasaran:</strong></label>
                            <textarea class="form-control modal-sasaran" data-id="{{ $kom->id_idpKom }}" style="height:8rem;">{{ old('kompetensi.' . $kom->id_idpKom . '.sasaran', $kom->sasaran) }}</textarea> {{-- PERBAIKAN DI SINI --}}
                        </div>

                        <div class="form-group mt-3">
                            <label><strong>Aksi:</strong></label>
                            <textarea class="form-control modal-aksi" data-id="{{ $kom->id_idpKom }}" style="height:8rem;">{{ old('kompetensi.' . $kom->id_idpKom . '.aksi', $kom->aksi) }}</textarea> {{-- PERBAIKAN DI SINI --}}
                        </div>

                        <div class="form-group mt-3">
                            <label><strong>Metode Belajar:</strong></label><br>
                            @foreach ($metodeBelajars as $metode)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input modal-metode" type="checkbox"
                                        data-id="{{ $kom->id_idpKom }}" value="{{ $metode->id_metodeBelajar }}"
                                        {{-- PERBAIKAN DI SINI --}}
                                        {{ $kom->metodeBelajars->contains('id_metodeBelajar', $metode->id_metodeBelajar) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ $metode->nama_metodeBelajar }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary btn-simpan-kompetensi"
                            data-id="{{ $kom->id_idpKom }}">Simpan</button> {{-- PERBAIKAN DI SINI --}}
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> {{-- Tambahkan SweetAlert2 untuk umpan balik pengguna yang lebih baik --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Debug: Log semua hidden inputs sebelum submit
            function debugHiddenInputs() {
                console.log('=== DEBUG HIDDEN INPUTS ===');
                const hiddenInputs = document.querySelectorAll('#hiddenKompetensiInputs input[type="hidden"]');
                hiddenInputs.forEach(input => {
                    console.log(`Name: ${input.name}, Value: ${input.value}`);
                });
                console.log('=== END DEBUG ===');
            }

            // Tangani tombol simpan modal
            document.querySelectorAll('.btn-simpan-kompetensi').forEach(button => {
                button.addEventListener('click', function() {
                    const komId = this.dataset.id; // Ini adalah id_idpKom secara langsung

                    const modal = this.closest('.modal');

                    console.log(`Updating kompetensi ID: ${komId}`);

                    // Dapatkan nilai dari modal
                    const sasaran = modal.querySelector(`.modal-sasaran[data-id="${komId}"]`).value;
                    const aksi = modal.querySelector(`.modal-aksi[data-id="${komId}"]`).value;
                    const checkedMetodes = modal.querySelectorAll(
                        `.modal-metode[data-id="${komId}"]:checked`);
                    const hiddenSasaran = document.querySelector(
                        `input[name="kompetensi[${komId}][sasaran]"]`); // Gunakan komId di sini
                    const hiddenAksi = document.querySelector(
                        `input[name="kompetensi[${komId}][aksi]"]`); // Gunakan komId di sini

                    if (hiddenSasaran) {
                        hiddenSasaran.value = sasaran;
                        console.log(`Sasaran diperbarui untuk ID ${komId}: ${sasaran}`);
                    } else {
                        console.error(
                            `Input sasaran tersembunyi tidak ditemukan untuk ID ${komId}`);
                    }

                    if (hiddenAksi) {
                        hiddenAksi.value = aksi;
                        console.log(`Aksi diperbarui untuk ID ${komId}: ${aksi}`);
                    } else {

                    }

                    // Hapus input metode belajar yang ada untuk kompetensi ini
                    document.querySelectorAll(
                            `input[name^="kompetensi[${komId}][id_metode_belajar]"]`)
                        .forEach( // Gunakan komId di sini
                            input => {
                                input.remove();
                            });

                    // Tambahkan input metode belajar baru
                    const hiddenContainer = document.getElementById('hiddenKompetensiInputs');
                    checkedMetodes.forEach(checkbox => {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name =
                            `kompetensi[${komId}][id_metode_belajar][]`; // Gunakan komId di sini
                        hiddenInput.value = checkbox.value;
                        hiddenInput.className = `hidden_metode_${komId}`;
                        hiddenInput.setAttribute('data-id_idpKom',
                            komId); // Perbarui atribut data
                        hiddenContainer.appendChild(hiddenInput);
                        console.log(
                            `Menambahkan metode belajar: ${checkbox.value} untuk ID ${komId}`
                        );
                    });

                    // Tutup modal
                    const modalInstance = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(
                        modal);
                    modalInstance.hide();

                    // Tampilkan pesan sukses
                    if (typeof Swal !== 'undefined') {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });

                        Toast.fire({
                            icon: 'success',
                            title: 'Data kompetensi berhasil diperbarui!'
                        });
                    }

                    // Debug setelah update
                    debugHiddenInputs();
                });
            });

            // Tangani pengiriman form dengan debugging
            document.getElementById('mainForm').addEventListener('submit', function(e) {
                console.log('=== FORM SUBMIT DEBUG ===');

                // Debug: Log semua form data
                const formData = new FormData(this);
                for (let [key, value] of formData.entries()) {
                    if (key.includes('kompetensi')) {
                        console.log(`${key}: ${value}`);
                    }
                }

                debugHiddenInputs();

                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            });
        });
    </script>
@endpush
