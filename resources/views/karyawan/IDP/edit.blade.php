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
                    <div class="breadcrumb-item active"><a href="{{ route('karyawan.dashboard-karyawan') }}">Dashboard</a>
                    </div>
                    <div class="breadcrumb-item active"><a href="{{ route('karyawan.IDP.indexKaryawan') }}">Data IDP</a></div>
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
                            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                @endif
                @php
                    $isDitolak = $idp->status_approval_mentor === 'Ditolak';
                    $punyaTemplateAsal = !is_null($idp->id_idp_template_asal);
                    $mentorOnlyEdit = $isDitolak && $punyaTemplateAsal;
                @endphp
                <div class="card">
                    <form action="{{ route('karyawan.IDP.updateIdp', $idp->id_idp) }}" method="POST" id="mainForm">
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
                            {{-- Hidden field untuk id_idp_template_asal jika ada --}}
                            @if ($punyaTemplateAsal)
                                <input type="hidden" name="id_idp_template_asal" value="{{ $idp->id_idp_template_asal }}">
                            @endif

                            {{-- Proyeksi Karir --}}
                            <div class="form-group">
                                <label>Proyeksi Karir</label>
                                <input type="text" name="proyeksi_karir"
                                    class="form-control @error('proyeksi_karir') is-invalid @enderror"
                                    value="{{ old('proyeksi_karir', $idp->proyeksi_karir) }}"
                                    {{ $mentorOnlyEdit ? 'readonly' : '' }}>
                                @error('proyeksi_karir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- Deskripsi IDP --}}
                            <div class="form-group">
                                <label>Deskripsi IDP</label>
                                <textarea name="deskripsi_idp" class="form-control @error('deskripsi_idp') is-invalid @enderror" style="height:8rem;"
                                    {{ $mentorOnlyEdit ? 'readonly' : '' }}>{{ old('deskripsi_idp', $idp->deskripsi_idp) }}</textarea>
                                @error('deskripsi_idp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Waktu Mulai</label>
                                    @if ($mentorOnlyEdit)
                                        <input type="hidden" name="waktu_mulai"
                                            value="{{ \Carbon\Carbon::parse($idp->waktu_mulai)->format('Y-m-d') }}">
                                        <input type="text" class="form-control"
                                            value="{{ \Carbon\Carbon::parse($idp->waktu_mulai)->translatedFormat('d F Y') }}"
                                            readonly>
                                    @else
                                        <input type="date" name="waktu_mulai"
                                            class="form-control @error('waktu_mulai') is-invalid @enderror"
                                            value="{{ old('waktu_mulai', \Carbon\Carbon::parse($idp->waktu_mulai)->format('Y-m-d')) }}">
                                        @error('waktu_mulai')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @endif

                                </div>
                                <div class="form-group col-md-6">
                                    <label>Waktu Selesai</label>
                                    @if ($mentorOnlyEdit)
                                        <input type="hidden" name="waktu_selesai"
                                            value="{{ \Carbon\Carbon::parse($idp->waktu_selesai)->format('Y-m-d') }}">
                                        <input type="text" class="form-control"
                                            value="{{ \Carbon\Carbon::parse($idp->waktu_selesai)->translatedFormat('d F Y') }}"
                                            readonly>
                                    @else
                                        <input type="date" name="waktu_selesai"
                                            class="form-control @error('waktu_selesai') is-invalid @enderror"
                                            value="{{ old('waktu_selesai', \Carbon\Carbon::parse($idp->waktu_selesai)->format('Y-m-d')) }}">
                                        @error('waktu_selesai')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @endif
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
                                    <input type="hidden" name="kompetensi[{{ $kom->id_idpKom }}][peran]"
                                        value="{{ $kom->peran ?? 'umum' }}">
                                    @foreach ($kom->metodeBelajars as $metode)
                                        <input type="hidden" name="kompetensi[{{ $kom->id_idpKom }}][id_metode_belajar][]"
                                            value="{{ $metode->id_metodeBelajar }}">
                                    @endforeach
                                @endforeach
                            </div>
                            <div class="form-group">
                                <label>Kompetensi</label> <br>
                                @if (!$mentorOnlyEdit)
                                    <button type="button" id="btn-tambah-kompetensi" class="btn btn-primary mb-3"
                                        data-toggle="modal" data-target="#modalTambahKompetensi">
                                        <i class="fas fa-plus-circle"></i> Tambah Kompetensi
                                    </button>
                                @endif
                                <br>
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
                                                    @if (!$mentorOnlyEdit)
                                                        <button type="button" class="btn btn-warning btn-sm mb-1"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalKompetensi{{ $kom->id_idpKom }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <!-- Delete Button -->
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm delete-kompetensi mb-1"
                                                            data-id="{{ $kom->id_idpKom }}"
                                                            data-nama="{{ $kom->kompetensi->nama_kompetensi }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
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
                                                    @if (!$mentorOnlyEdit)
                                                        <button type="button" class="btn btn-warning btn-sm mb-1"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalHardKompetensi{{ $kom->id_idpKom }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <!-- Delete Button -->
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm delete-kompetensi mb-1"
                                                            data-id="{{ $kom->id_idpKom }}"
                                                            data-nama="{{ $kom->kompetensi->nama_kompetensi }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group">
                                <label>Saran IDP</label>
                                <textarea name="saran_idp" id="saran_idp" class="form-control" readonly style="height:6rem;">{{ old('saran_idp', $idp->saran_idp) }}</textarea>
                            </div>
                            {{-- SPV --}}
                            <div class="form-group">
                                <label>Supervisor <span class="text-danger">*</span></label>
                                @if ($mentorOnlyEdit)
                                    <small class="form-text text-muted">Supervisor tidak dapat diubah karena IDP berasal
                                        dari Mapping IDP</small>
                                @endif
                                @if ($idp->status_approval_mentor === 'Disetujui')
                                    <input type="hidden" name="id_supervisor" value="{{ $idp->id_supervisor }}">
                                    <input type="text" class="form-control"
                                        value="{{ $idp->supervisor->name ?? 'Data tidak ditemukan' }}" readonly>
                                @else
                                    <select name="id_supervisor"
                                        class="form-control @error('id_supervisor') is-invalid @enderror"
                                        {{ $mentorOnlyEdit ? 'disabled' : '' }} required>
                                        <option value="">-- Pilih Supervisor --</option>
                                        @foreach ($supervisors as $item)
                                            <option value="{{ $item->id }}"
                                                {{ old('id_supervisor', $idp->id_supervisor) == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($mentorOnlyEdit)
                                        <input type="hidden" name="id_supervisor" value="{{ $idp->id_supervisor }}">
                                    @endif
                                    @error('id_supervisor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                            {{-- Mentor --}}
                            <div class="form-group">
                                <label>Mentor <span class="text-danger">*</span></label>
                                @if ($idp->status_approval_mentor === 'Disetujui')
                                    <input type="hidden" name="id_mentor" value="{{ $idp->id_mentor }}">
                                    <input type="text" class="form-control"
                                        value="{{ $idp->mentor->name ?? 'Data tidak ditemukan' }}" readonly>
                                @else
                                    <select name="id_mentor" class="form-control @error('id_mentor') is-invalid @enderror"
                                        required>
                                        <option value="">-- Pilih Mentor --</option>
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
                                @endif
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <a class="btn btn-warning" href="{{ route('karyawan.IDP.indexKaryawan') }}">Kembali</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    {{-- PERBAIKAN MODAL SECTION - Tambahkan field peran untuk edit kompetensi existing --}}
    @foreach ($idp->idpKompetensis as $kom)
        @php
            $jenis = $kom->kompetensi->jenis_kompetensi;
        @endphp

        <div class="modal fade"
            id="{{ $jenis === 'Soft Kompetensi' ? 'modalKompetensi' . $kom->id_idpKom : 'modalHardKompetensi' . $kom->id_idpKom }}"
            tabindex="-1"
            aria-labelledby="{{ $jenis === 'Soft Kompetensi' ? 'modalLabel' . $kom->id_idpKom : 'modalHardLabel' . $kom->id_idpKom }}"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="{{ $jenis === 'Soft Kompetensi' ? 'modalLabel' . $kom->id_idpKom : 'modalHardLabel' . $kom->id_idpKom }}">
                            Edit {{ $jenis }}: {{ $kom->kompetensi->nama_kompetensi }}
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <p><strong>Keterangan:</strong> {{ $kom->kompetensi->keterangan }}</p>

                        <div class="form-group">
                            <label><strong>Sasaran:</strong></label>
                            <textarea class="form-control modal-sasaran" data-id="{{ $kom->id_idpKom }}" style="height:8rem;"
                                {{ $punyaTemplateAsal ? 'readonly' : '' }}>{{ old('kompetensi.' . $kom->id_idpKom . '.sasaran', $kom->sasaran) }}</textarea>
                        </div>

                        <div class="form-group mt-3">
                            <label><strong>Aksi:</strong></label>
                            <textarea class="form-control modal-aksi" data-id="{{ $kom->id_idpKom }}" style="height:8rem;"
                                {{ $punyaTemplateAsal ? 'readonly' : '' }}>{{ old('kompetensi.' . $kom->id_idpKom . '.aksi', $kom->aksi) }}</textarea>
                        </div>

                        <div class="form-group mt-3">
                            <label><strong>Peran Kompetensi:</strong></label>
                            <select class="form-control modal-peran" data-id="{{ $kom->id_idpKom }}"
                                {{ $punyaTemplateAsal ? 'disabled' : '' }}>
                                <option value="umum" {{ ($kom->peran ?? 'umum') === 'umum' ? 'selected' : '' }}>
                                    Kompetensi Umum</option>
                                <option value="utama" {{ ($kom->peran ?? '') === 'utama' ? 'selected' : '' }}>Kompetensi
                                    Utama</option>
                                <option value="kunci_core" {{ ($kom->peran ?? '') === 'kunci_core' ? 'selected' : '' }}>
                                    Kompetensi Kunci Core</option>
                                <option value="kunci_bisnis"
                                    {{ ($kom->peran ?? '') === 'kunci_bisnis' ? 'selected' : '' }}>
                                    Kompetensi Kunci Bisnis</option>
                                <option value="kunci_enabler"
                                    {{ ($kom->peran ?? '') === 'kunci_enabler' ? 'selected' : '' }}>
                                    Kompetensi Kunci Enabler</option>
                            </select>
                        </div>

                        <div class="form-group mt-3">
                            <label><strong>Metode Belajar:</strong></label><br>
                            @foreach ($metodeBelajars as $metode)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input modal-metode" type="checkbox"
                                        data-id="{{ $kom->id_idpKom }}" value="{{ $metode->id_metodeBelajar }}"
                                        {{ $kom->metodeBelajars->contains('id_metodeBelajar', $metode->id_metodeBelajar) ? 'checked' : '' }}
                                        {{ $punyaTemplateAsal ? 'disabled' : '' }}>
                                    <label class="form-check-label">{{ $metode->nama_metodeBelajar }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Tutup</button>
                        @unless ($punyaTemplateAsal)
                            <button type="button" class="btn btn-primary btn-simpan-kompetensi"
                                data-id="{{ $kom->id_idpKom }}">Simpan</button>
                        @endunless
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modal Tambah Kompetensi -->
    <div class="modal fade" id="modalTambahKompetensi" tabindex="-1" role="dialog"
        aria-labelledby="modalTambahKompetensiLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTambahKompetensiLabel">Tambah Kompetensi</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formKompetensi">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Jenis Kompetensi</label>
                                <select class="form-control jenis_kompetensi" id="modalJenisKompetensi">
                                    <option value="Hard Kompetensi">Hard Kompetensi</option>
                                    <option value="Soft Kompetensi">Soft Kompetensi</option>
                                </select>
                            </div>
                            <!-- Jenjang -->
                            <div class="form-group col-md-6" id="formJenjangGroup">
                                <label>Jenjang</label>
                                <select class="form-control" id="modalJenjangDropdown">
                                    <option value="">Pilih Jenjang</option>
                                    @foreach ($listJenjang as $item)
                                        <option value="{{ $item->id_jenjang }}">{{ $item->nama_jenjang }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Jabatan -->
                            <div class="form-group col-md-6" id="formJabatanGroup">
                                <label>Jabatan</label>
                                <select class="form-control" id="modalJabatanDropdown">
                                    <option value="">Pilih Jabatan</option>
                                    @foreach ($listJabatan as $item)
                                        <option value="{{ $item->id_jabatan }}">{{ $item->nama_jabatan }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Kompetensi</label>
                                <select class="form-control kompetensi-dropdown" id="modalKompetensiDropdown">
                                    <!-- Opsi akan diisi oleh JS -->
                                </select>
                            </div>
                            <!-- Peran Kompetensi, hidden default -->
                            <div class="form-group col-md-12" id="formPeranGroup">
                                <label>Peran Kompetensi</label>
                                <select class="form-control" id="modalPeranDropdown" name="peran">
                                    <option value="umum">Kompetensi Umum</option>
                                    <option value="utama">Kompetensi Utama</option>
                                    <option value="kunci_core">Kompetensi Kunci Core</option>
                                    <option value="kunci_bisnis">Kompetensi Kunci Bisnis</option>
                                    <option value="kunci_enabler">Kompetensi Kunci Enabler</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="modalMetodeBelajar">Pilih Metode Belajar</label>
                            <select id="modalMetodeBelajar" multiple placeholder="Pilih satu atau lebih metode belajar">
                                @foreach ($metodeBelajars as $item)
                                    <option value="{{ $item->id_metodeBelajar }}">
                                        {{ $item->nama_metodeBelajar }}
                                    </option>
                                @endforeach
                            </select>

                        </div>
                        <div class="form-group">
                            <label>Sasaran</label>
                            <textarea class="form-control" id="modalSasaran" style="height:6rem;"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Aksi</label>
                            <textarea class="form-control" id="modalAksi" style="height:6rem;"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btnSimpanKompetensi">
                        <i class="fas fa-save"></i> Simpan Kompetensi
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Template JS File -->
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> {{-- Tambahkan SweetAlert2 untuk umpan balik pengguna yang lebih baik --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tangani tombol hapus kompetensi
            document.querySelectorAll('.delete-kompetensi').forEach(button => {
                button.addEventListener('click', function() {
                    const kompetensiId = this.dataset.id; // Ambil ID kompetensi
                    const kompetensiNama = this.dataset.nama; // Ambil Nama Kompetensi

                    // Konfirmasi penghapusan
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: `Anda akan menghapus kompetensi: ${kompetensiNama}`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Jika pengguna mengonfirmasi, kirim permintaan penghapusan ke server
                            fetch(`/hapus-kompetensi/${kompetensiId}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute(
                                            'content')
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire('Dihapus!',
                                            'Kompetensi telah berhasil dihapus.',
                                            'success');
                                        // Hapus baris dari tabel
                                        const row = button.closest('tr');
                                        row.remove();
                                    } else {
                                        Swal.fire('Gagal!',
                                            'Terjadi kesalahan saat menghapus kompetensi.',
                                            'error');
                                    }
                                });
                        }
                    });
                });
            });
        });

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
            // PERBAIKAN JAVASCRIPT - Tangani tombol simpan modal dengan field peran
            document.querySelectorAll('.btn-simpan-kompetensi').forEach(button => {
                button.addEventListener('click', function() {
                    const komId = this.dataset.id; // ID dari id_idpKom
                    const jenis = this.dataset
                        .jenis; // Jenis: 'Soft Kompetensi' atau 'Hard Kompetensi'
                    const modal = this.closest('.modal');

                    // Ambil nilai sasaran, aksi, dan peran
                    const sasaran = modal.querySelector(`.modal-sasaran[data-id="${komId}"]`).value;
                    const aksi = modal.querySelector(`.modal-aksi[data-id="${komId}"]`).value;
                    const peran = modal.querySelector(`.modal-peran[data-id="${komId}"]`).value;
                    const checkedMetodes = modal.querySelectorAll(
                        `.modal-metode[data-id="${komId}"]:checked`);

                    // Ambil nilai yang sudah ada (original) untuk membandingkan apakah ada perubahan
                    const originalSasaran = document.querySelector(
                        `input[name="kompetensi[${komId}][sasaran]"]`).value;
                    const originalAksi = document.querySelector(
                        `input[name="kompetensi[${komId}][aksi]"]`).value;
                    const originalPeran = document.querySelector(
                        `input[name="kompetensi[${komId}][peran]"]`).value;
                    const originalMetodeIds = Array.from(document.querySelectorAll(
                        `input[name^="kompetensi[${komId}][id_metode_belajar]"]`)).map(input =>
                        input.value);

                    // Cek apakah ada perubahan
                    const isChanged = sasaran !== originalSasaran || aksi !== originalAksi ||
                        peran !== originalPeran || !arraysEqual(Array.from(checkedMetodes).map(
                            checkbox => checkbox.value), originalMetodeIds);

                    if (!isChanged) {
                        // Jika tidak ada perubahan, keluar dari fungsi tanpa menampilkan notifikasi
                        console.log("Tidak ada perubahan data.");
                        return; // Tidak menampilkan notifikasi dan keluar dari fungsi
                    }

                    // Update input tersembunyi untuk sasaran, aksi, dan peran
                    document.querySelector(`input[name="kompetensi[${komId}][sasaran]"]`).value =
                        sasaran;
                    document.querySelector(`input[name="kompetensi[${komId}][aksi]"]`).value = aksi;
                    document.querySelector(`input[name="kompetensi[${komId}][peran]"]`).value =
                        peran;

                    // Hapus semua input metode belajar sebelumnya
                    document.querySelectorAll(
                        `input[name^="kompetensi[${komId}][id_metode_belajar]"]`).forEach(
                        input => input.remove());

                    // Tambahkan kembali input metode belajar yang dicentang
                    const hiddenContainer = document.getElementById('hiddenKompetensiInputs');
                    checkedMetodes.forEach(checkbox => {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = `kompetensi[${komId}][id_metode_belajar][]`;
                        hiddenInput.value = checkbox.value;
                        hiddenInput.className = `hidden_metode_${komId}`;
                        hiddenContainer.appendChild(hiddenInput);
                    });

                    // Tutup modal menggunakan instance bootstrap
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    modalInstance.hide();

                    // Jika ada perubahan, tampilkan notifikasi
                    if (isChanged && typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Data kompetensi berhasil diperbarui!',
                            showConfirmButton: false,
                            timer: 3000,
                            toast: false, // Menghilangkan toast, tampilkan sebagai dialog
                            position: 'center', // Menampilkan di tengah layar
                            customClass: {
                                popup: 'center-popup' // Kelas CSS untuk menyesuaikan tampilan
                            }
                        });
                    }

                    // Debug final
                    debugHiddenInputs();
                });
            });

            // Utility function to compare two arrays
            function arraysEqual(a, b) {
                if (a.length !== b.length) return false;
                for (let i = 0; i < a.length; i++) {
                    if (a[i] !== b[i]) return false;
                }
                return true;
            }

            // Fungsi untuk debug input tersembunyi
            function debugHiddenInputs() {
                console.log('=== DEBUG HIDDEN INPUTS ===');
                const hiddenInputs = document.querySelectorAll('#hiddenKompetensiInputs input[type="hidden"]');
                hiddenInputs.forEach(input => {
                    console.log(`Name: ${input.name}, Value: ${input.value}`);
                });
                console.log('=== END DEBUG ===');
            }

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
        $(document).ready(function() {
            // Inisialisasi TomSelect untuk metode belajar
            let tomSelectMetodeBelajar = new TomSelect("#modalMetodeBelajar", {
                plugins: ['remove_button'],
            });

            const kompetensiData = {
                "Hard Kompetensi": @json($kompetensi->where('jenis_kompetensi', 'Hard Kompetensi')->values()),
                "Soft Kompetensi": @json($kompetensi->where('jenis_kompetensi', 'Soft Kompetensi')->values())
            };

            // Counter untuk ID unik kompetensi baru
            let newKompetensiCounter = 1000; // Mulai dari 1000 untuk membedakan dengan ID database

            // Fungsi untuk mengisi dropdown kompetensi berdasarkan jenis
            function renderKompetensiOptions(jenis_kompetensi) {
                const kompetensiDropdown = $('#modalKompetensiDropdown');
                kompetensiDropdown.empty().append('<option value="">-- Pilih Kompetensi --</option>');

                if (kompetensiData[jenis_kompetensi]) {
                    kompetensiData[jenis_kompetensi].forEach(item => {
                        kompetensiDropdown.append(
                            `<option value="${item.id_kompetensi}">${item.nama_kompetensi}</option>`);
                    });
                }
                const peranDropdown = $('#modalPeranDropdown');
                peranDropdown.empty().append('<option value="">-- Pilih Peran Kompetensi --</option>');
                const peranOptions = [{
                        value: 'umum',
                        label: 'Kompetensi Umum'
                    },
                    {
                        value: 'utama',
                        label: 'Kompetensi Utama'
                    },
                    {
                        value: 'kunci_core',
                        label: 'Kompetensi Kunci Core'
                    },
                    {
                        value: 'kunci_bisnis',
                        label: 'Kompetensi Kunci Bisnis'
                    },
                    {
                        value: 'kunci_enabler',
                        label: 'Kompetensi Kunci Enabler'
                    },
                ];

                peranOptions.forEach(peran => {
                    peranDropdown.append(`<option value="${peran.value}">${peran.label}</option>`);
                });
            }

            // Inisialisasi pertama kali dropdown kompetensi dengan Hard Kompetensi
            renderKompetensiOptions('Hard Kompetensi');

            // Event listener untuk perubahan jenis kompetensi
            $('#modalJenisKompetensi').on('change', function() {
                renderKompetensiOptions($(this).val());
                toggleHardKompetensiFields();
            });

            // Fungsi toggle visibility form jenjang dan jabatan
            function toggleHardKompetensiFields() {
                let jenis = $('#modalJenisKompetensi').val();
                if (jenis === 'Hard Kompetensi') {
                    $('#modalJenjangDropdown').closest('.form-group').show();
                    $('#modalJabatanDropdown').closest('.form-group').show();
                    $('#formPeranGroup').closest('.form-group').hide(); // Hide Peran Kompetensi for Hard Kompetensi
                    $('#modalPeranDropdown').val('umum');
                    renderKompetensiOptions('Hard Kompetensi');

                } else {
                    $('#modalJenjangDropdown').closest('.form-group').hide();
                    $('#modalJabatanDropdown').closest('.form-group').hide();
                    $('#formPeranGroup').closest('.form-group').show(); // Tampilkan Peran untuk Soft Kompetensi
                    renderKompetensiOptions('Soft Kompetensi');
                }
            }

            // Fungsi untuk mendapatkan nama kompetensi berdasarkan ID
            function getNamaKompetensi(kompetensiId, jenis) {
                const data = kompetensiData[jenis];
                const kompetensi = data.find(item => item.id_kompetensi == kompetensiId);
                return kompetensi ? kompetensi.nama_kompetensi : 'Kompetensi tidak ditemukan';
            }

            // Fungsi untuk menambahkan baris ke tabel yang sudah ada
            function addRowToExistingTable(data, jenis) {
                let tbody, currentRowCount;

                if (jenis === 'Soft Kompetensi') {
                    tbody = $('.table').first().find('tbody'); // Tabel Soft Kompetensi (yang pertama)
                    currentRowCount = tbody.find('tr').length;
                } else {
                    tbody = $('.table').last().find('tbody'); // Tabel Hard Kompetensi (yang kedua)
                    currentRowCount = tbody.find('tr').length;
                }

                const newRowNumber = currentRowCount + 1;
                const uniqueId = newKompetensiCounter++;

                // Buat baris baru
                const newRow = `
            <tr data-new-kompetensi="${uniqueId}">
                <td style="width: 50px;">${newRowNumber}</td>
                <td>${data.kompetensiText}</td>
                <td style="width: 50px;">
                        <button type="button" class="btn btn-warning btn-sm edit-new-kompetensi mb-1"
                        data-id="${uniqueId}" data-jenis="${jenis}">
                        <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm delete-new-kompetensi mb-1"
                        data-id="${uniqueId}">
                        <i class="fas fa-trash"></i>
                        </button>
                </td>
            </tr>
        `;

                tbody.append(newRow);
                addHiddenInputsForNewKompetensi(data, uniqueId);
            }

            // Fungsi untuk menambahkan hidden inputs untuk kompetensi baru
            function addHiddenInputsForNewKompetensi(data, uniqueId) {
                const hiddenContainer = document.getElementById('hiddenKompetensiInputs');
                const wrapper = document.createElement('div');
                wrapper.dataset.kompetensiHidden = uniqueId;
                // Input untuk ID (gunakan unique ID)
                const hiddenId = document.createElement('input');
                hiddenId.type = 'hidden';
                hiddenId.name = `kompetensi[new_${uniqueId}][id_kompetensi]`;
                hiddenId.value = data.kompetensiId;
                hiddenId.className = `new_kompetensi_${uniqueId}`;
                hiddenContainer.appendChild(hiddenId);

                // Input untuk sasaran
                const hiddenSasaran = document.createElement('input');
                hiddenSasaran.type = 'hidden';
                hiddenSasaran.name = `kompetensi[new_${uniqueId}][sasaran]`;
                hiddenSasaran.value = data.sasaran;
                hiddenSasaran.className = `new_kompetensi_${uniqueId}`;
                hiddenContainer.appendChild(hiddenSasaran);

                // Input untuk aksi
                const hiddenAksi = document.createElement('input');
                hiddenAksi.type = 'hidden';
                hiddenAksi.name = `kompetensi[new_${uniqueId}][aksi]`;
                hiddenAksi.value = data.aksi;
                hiddenAksi.className = `new_kompetensi_${uniqueId}`;
                hiddenContainer.appendChild(hiddenAksi);

                // Buat input hidden untuk peran
                const peranValue = document.getElementById('modalPeranDropdown').value;
                const hiddenPeran = document.createElement('input');
                hiddenPeran.type = 'hidden';
                hiddenPeran.name = `kompetensi[new_${uniqueId}][peran]`;
                hiddenPeran.value = peranValue;
                hiddenPeran.className = `new_kompetensi_${uniqueId}`;
                hiddenContainer.appendChild(hiddenPeran);

                // Input untuk metode belajar
                data.metodeIds.forEach(metodeId => {
                    const hiddenMetode = document.createElement('input');
                    hiddenMetode.type = 'hidden';
                    hiddenMetode.name = `kompetensi[new_${uniqueId}][id_metode_belajar][]`;
                    hiddenMetode.value = metodeId;
                    hiddenMetode.className = `new_kompetensi_${uniqueId}`;
                    hiddenContainer.appendChild(hiddenMetode);
                });
                hiddenContainer.appendChild(wrapper);

                console.log(`Added new kompetensi with ID: new_${uniqueId}`);
            }

            // Event tombol simpan kompetensi
            $('#btnSimpanKompetensi').on('click', function() {
                const jenis = $('#modalJenisKompetensi').val();
                const kompetensiDropdown = $('#modalKompetensiDropdown');
                const kompetensiId = kompetensiDropdown.val();
                const kompetensiText = kompetensiDropdown.find('option:selected').text();

                const metodeSelect = $('#modalMetodeBelajar');
                const selectedOptions = metodeSelect.find('option:selected');
                const metodeIds = selectedOptions.map(function() {
                    return $(this).val();
                }).get();

                const sasaran = $('#modalSasaran').val();
                const aksi = $('#modalAksi').val();
                const peran = $('#modalPeranDropdown').val();

                // Validasi
                if (!kompetensiId) {
                    alert("Harap pilih kompetensi");
                    return;
                }
                if (metodeIds.length === 0) {
                    alert("Harap pilih minimal satu metode belajar");
                    return;
                }
                if (!sasaran.trim() || !aksi.trim()) {
                    alert("Harap isi sasaran dan aksi");
                    return;
                }

                const data = {
                    kompetensiId: kompetensiId,
                    kompetensiText: kompetensiText,
                    metodeIds: metodeIds,
                    sasaran: sasaran,
                    aksi: aksi,
                    peran: peran
                };

                // Tambahkan ke tabel yang sudah ada
                addRowToExistingTable(data, jenis);

                // Reset form dan tutup modal
                resetFormModal();
                const modalTambahKompetensiElement = document.getElementById('modalTambahKompetensi');
                if (modalTambahKompetensiElement) {
                    const modalInstance = bootstrap.Modal.getInstance(modalTambahKompetensiElement);
                    if (modalInstance) {
                        // Sembunyikan modal jika instance-nya sudah ada
                        modalInstance.hide();
                    } else {
                        // Jika instance belum ada, buat yang baru dan sembunyikan
                        new bootstrap.Modal(modalTambahKompetensiElement).hide();
                    }
                }
                // Tampilkan pesan sukses
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Kompetensi baru berhasil ditambahkan!',
                        showConfirmButton: false, 
                        confirmButtonText: 'Tutup', 
                        position: 'center', 
                        timer: 3000, 
                        timerProgressBar: true, 
                    });
                }

            });

            // Event handler untuk tombol hapus kompetensi baru
            $(document).on('click', '.delete-new-kompetensi', function() {
                const uniqueId = $(this).data('id');
                const kompetensiNama = $(this).data(
                    'nama'); // Menambahkan nama kompetensi untuk ditampilkan di konfirmasi

                // Konfirmasi penghapusan dengan Swal
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: `Anda akan menghapus kompetensi: ${kompetensiNama}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Jika pengguna mengonfirmasi, hapus baris dan data terkait
                        $(`tr[data-new-kompetensi="${uniqueId}"]`).remove(); // Hapus baris
                        $(`.new_kompetensi_${uniqueId}`).remove(); // Hapus hidden inputs

                        // Update nomor urut tabel
                        updateTableRowNumbers();

                        // Tampilkan notifikasi sukses dengan SweetAlert2
                        Swal.fire({
                            icon: 'success',
                            title: 'Kompetensi berhasil dihapus!',
                            showConfirmButton: false,
                            timer: 2000,
                            position: 'top-end',
                            toast: true, // Menampilkan sebagai toast
                            timerProgressBar: true
                        });
                    }
                });
            });

            // Event handler untuk tombol edit kompetensi baru
            $(document).on('click', '.edit-new-kompetensi', function() {
                const uniqueId = $(this).data('id');
                const jenis = $(this).data('jenis');

                // Ambil data dari hidden inputs
                const sasaran = $(`input[name="kompetensi[new_${uniqueId}][sasaran]"]`).val();
                const aksi = $(`input[name="kompetensi[new_${uniqueId}][aksi]"]`).val();
                const kompetensiId = $(`input[name="kompetensi[new_${uniqueId}][id_kompetensi]"]`).val();
                const metodeIds = $(`input[name="kompetensi[new_${uniqueId}][id_metode_belajar][]"]`).map(
                    function() {
                        return $(this).val();
                    }).get();
                const peran = $(`input[name="kompetensi[new_${uniqueId}][peran]"]`).val();

                // Buat modal dinamis untuk edit
                createEditModalForNewKompetensi(uniqueId, jenis, {
                    sasaran: sasaran,
                    aksi: aksi,
                    kompetensiId: kompetensiId,
                    metodeIds: metodeIds,
                    peran: peran
                });
            });

            // Fungsi untuk membuat modal edit kompetensi baru
            function createEditModalForNewKompetensi(uniqueId, jenis, data) {
                const kompetensiName = getNamaKompetensi(data.kompetensiId, jenis);

                const modalHtml = `
            <div class="modal fade" id="modalEditNew${uniqueId}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit ${jenis}: ${kompetensiName}</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Keterangan:</strong> {{ $kom->kompetensi->keterangan }}</p>
                            <div class="form-group">
                                <label><strong>Sasaran:</strong></label>
                                <textarea class="form-control" id="editSasaran${uniqueId}" style="height:8rem;">${data.sasaran}</textarea>
                            </div>
                            <div class="form-group mt-3">
                                <label><strong>Aksi:</strong></label>
                                <textarea class="form-control" id="editAksi${uniqueId}" style="height:8rem;">${data.aksi}</textarea>
                            </div>
                             <div class="form-group mt-3">
                                <label><strong>Peran Kompetensi:</strong></label>
                                <select class="form-control" id="editPeran${uniqueId}">
                                    <option value="umum">Kompetensi Umum</option>
                                    <option value="utama">Kompetensi Utama</option>
                                    option value="kunci_core">Kompetensi Kunci Core</option>
                                    <option value="kunci_bisnis">Kompetensi Kunci Bisnis</option>
                                <option value="kunci_enabler">Kompetensi Kunci Enabler</option>
                                </select>
                            </div>
                            <div class="form-group mt-3">
                                <label><strong>Metode Belajar:</strong></label><br>
                                ${generateMetodeBelajarCheckboxes(uniqueId, data.metodeIds)}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="button" class="btn btn-primary" onclick="saveEditNewKompetensi(${uniqueId})">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

                // Hapus modal jika sudah ada
                $(`#modalEditNew${uniqueId}`).remove();

                // Tambahkan modal ke body
                $('body').append(modalHtml);
                // Set nilai dropdown peran
                $(`#editPeran${uniqueId}`).val(data.peran);


                // Tampilkan modal
                $(`#modalEditNew${uniqueId}`).modal('show');
            }

            // Fungsi untuk generate checkbox metode belajar
            function generateMetodeBelajarCheckboxes(uniqueId, selectedMetodeIds) {
                const metodeBelajars = @json($metodeBelajars);
                let checkboxes = '';

                metodeBelajars.forEach(metode => {
                    const isChecked = selectedMetodeIds.includes(metode.id_metodeBelajar.toString()) ?
                        'checked' : '';
                    checkboxes += `
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" 
                           id="editMetode${uniqueId}_${metode.id_metodeBelajar}"
                           value="${metode.id_metodeBelajar}" ${isChecked}>
                    <label class="form-check-label" for="editMetode${uniqueId}_${metode.id_metodeBelajar}">
                        ${metode.nama_metodeBelajar}
                    </label>
                </div>
            `;
                });

                return checkboxes;
            }
            // Fungsi untuk menyimpan perubahan kompetensi baru
            window.saveEditNewKompetensi = function(uniqueId) {
                const sasaran = $(`#editSasaran${uniqueId}`).val();
                const aksi = $(`#editAksi${uniqueId}`).val();
                const peran = $(`#editPeran${uniqueId}`).val(); // Ambil nilai peran
                const metodeIds = $(`#modalEditNew${uniqueId} input[type="checkbox"]:checked`).map(function() {
                    return $(this).val();
                }).get();

                // Cek jika ada perubahan
                const originalSasaran = $(`input[name="kompetensi[new_${uniqueId}][sasaran]"]`).val();
                const originalAksi = $(`input[name="kompetensi[new_${uniqueId}][aksi]"]`).val();
                const originalPeran = $(`input[name="kompetensi[new_${uniqueId}][peran]"]`).val();
                const originalMetodeIds = $(`input[name="kompetensi[new_${uniqueId}][id_metode_belajar][]"]`)
                    .map(function() {
                        return $(this).val();
                    }).get();

                // Cek apakah ada perubahan
                const isChanged = sasaran !== originalSasaran || aksi !== originalAksi || peran !==
                    originalPeran || !arraysEqual(metodeIds, originalMetodeIds);

                if (!isChanged) {
                    // Jika tidak ada perubahan, tidak tampilkan notifikasi
                    return;
                }

                // Jika ada perubahan, update hidden inputs
                const hiddenContainer = document.getElementById('hiddenKompetensiInputs');
                $(`input[name="kompetensi[new_${uniqueId}][sasaran]"]`).val(sasaran);
                $(`input[name="kompetensi[new_${uniqueId}][aksi]"]`).val(aksi);
                $(`input[name="kompetensi[new_${uniqueId}][peran]"]`).remove();
                const hiddenPeran = document.createElement('input');
                hiddenPeran.type = 'hidden';
                hiddenPeran.name = `kompetensi[new_${uniqueId}][peran]`;
                hiddenPeran.value = peran;
                hiddenPeran.className = `new_kompetensi_${uniqueId}`;
                hiddenContainer.appendChild(hiddenPeran);

                // Hapus metode belajar lama dan tambah yang baru
                $(`.new_kompetensi_${uniqueId}`).filter(
                    `input[name="kompetensi[new_${uniqueId}][id_metode_belajar][]"]`).remove();

                metodeIds.forEach(metodeId => {
                    const hiddenMetode = document.createElement('input');
                    hiddenMetode.type = 'hidden';
                    hiddenMetode.name = `kompetensi[new_${uniqueId}][id_metode_belajar][]`;
                    hiddenMetode.value = metodeId;
                    hiddenMetode.className = `new_kompetensi_${uniqueId}`;
                    hiddenContainer.appendChild(hiddenMetode);
                });

                // Tutup modal dan hapus modal
                $(`#modalEditNew${uniqueId}`).modal('hide');
                $(`#modalEditNew${uniqueId}`).remove();

                // Tampilkan notifikasi jika ada perubahan
                if (isChanged && typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Data kompetensi berhasil diperbarui!',
                        showConfirmButton: false,
                        timer: 3000,
                        toast: false, // Mengubah toast ke false agar tampil seperti dialog
                        position: 'center', // Menampilkan di tengah layar
                        customClass: {
                            popup: 'center-popup' // Anda bisa menambahkan class khusus untuk penyesuaian lebih lanjut
                        }
                    });
                }
            };

            // Utility function to compare two arrays
            function arraysEqual(a, b) {
                if (a.length !== b.length) return false;
                for (let i = 0; i < a.length; i++) {
                    if (a[i] !== b[i]) return false;
                }
                return true;
            }
            // Fungsi untuk update nomor urut tabel
            function updateTableRowNumbers() {
                $('.table tbody').each(function() {
                    $(this).find('tr').each(function(index) {
                        $(this).find('td:first').text(index + 1);
                    });
                });
            }

            // Reset form modal tambah kompetensi
            function resetFormModal() {
                $('#modalJenisKompetensi').val('Hard Kompetensi').trigger('change');
                $('#modalSasaran').val('');
                $('#modalAksi').val('');
                $('#modalJenjangDropdown').val('').trigger('change');
                $('#modalJabatanDropdown').empty().append('<option value="">Pilih Jabatan</option>');
                $('#modalKompetensiDropdown').empty().append('<option value="">Pilih Kompetensi</option>');
                $('#modalPeranDropdown').val('').trigger('change');
                if (typeof tomSelectMetodeBelajar !== 'undefined') {
                    tomSelectMetodeBelajar.clear();
                }
            }

            // Event saat modal tambah kompetensi tampil
            $('#modalTambahKompetensi').on('show.bs.modal', function() {
                resetFormModal();
            });

            // Toggle visibility form jenjang dan jabatan saat awal halaman load
            toggleHardKompetensiFields();

            // Event saat jenjang dipilih -> ajax ambil jabatan
            $('#modalJenjangDropdown').on('change', function() {
                let jenjangId = $(this).val();
                if (jenjangId) {
                    $.ajax({
                        url: '/admin/datamaster/behavior/idp/get-jabatan-by-jenjang/' + jenjangId,
                        type: 'GET',
                        success: function(data) {
                            let jabatanDropdown = $('#modalJabatanDropdown');
                            jabatanDropdown.empty().append(
                                '<option value="">Pilih Jabatan</option>');
                            data.forEach(function(jabatan) {
                                jabatanDropdown.append(
                                    `<option value="${jabatan.id_jabatan}">${jabatan.nama_jabatan}</option>`
                                );
                            });
                        }
                    });
                } else {
                    $('#modalJabatanDropdown').empty().append('<option value="">Pilih Jabatan</option>');
                }
            });

            // Event saat jabatan dipilih -> ajax ambil kompetensi hard
            $('#modalJabatanDropdown').on('change', function() {
                let jabatanId = $(this).val();
                if (jabatanId) {
                    $.ajax({
                        url: '/admin/datamaster/behavior/idp/get-kompetensi-by-jabatan/' +
                            jabatanId,
                        type: 'GET',
                        success: function(data) {
                            let kompetensiDropdown = $('#modalKompetensiDropdown');
                            kompetensiDropdown.empty().append(
                                '<option value="">Pilih Kompetensi</option>');
                            data.forEach(function(komp) {
                                kompetensiDropdown.append(
                                    `<option value="${komp.id_kompetensi}">${komp.nama_kompetensi}</option>`
                                );
                            });
                        }
                    });
                } else {
                    $('#modalKompetensiDropdown').empty().append(
                        '<option value="">Pilih Kompetensi</option>');
                }
            });
        });
    </script>
@endpush
