    @extends('layouts.app')

    @section('title', 'Tambah IDP Karyawan')

    @section('main')
        <div class="main-content">
            <section class="section">
                <div class="section-header">
                    <h1>Tambah IDP Karyawan</h1>
                    <div class="section-header-breadcrumb">
                        <div class="breadcrumb-item active"><a href="{{ route('karyawan.dashboard-karyawan') }}">Dashboard</a>
                        </div>
                        <div class="breadcrumb-item"><a href="{{ route('karyawan.IDP.indexKaryawan') }}">Data
                                Bank
                                IDP</a>
                        </div>
                        <div class="breadcrumb-item">Tambah IDP</div>
                    </div>
                </div>

                <div class="section-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="card" style="border-left: 5px solid #28a745; background-color: #e6f9d7;">
                        <div class="card-body" style="color: #212529;">
                            Silakan susun IDP pribadi Anda dengan memilih soft dan hard kompetensi yang ingin dikembangkan,
                            menetapkan proyeksi karir, dan memilih mentor pendamping. Setelah diajukan, IDP akan menunggu
                            persetujuan dari mentor sebelum dapat dikerjakan selama satu semester. Pastikan pilihan Anda
                            sesuai dengan tujuan pengembangan diri.
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4>Tambah Individual Development Plan</h4>
                        </div>
                        <div class="card-body">
                            <form id="mainIdpForm" action="{{ route('karyawan.IDP.store') }}" method="POST"> @csrf

                                {{-- <div class="form-group">
                                    <label>Metode Input</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="metode" id="metode1"
                                            value="1" onclick="toggleMetode(1)" checked>
                                        <label class="form-check-label" for="metode1">Given IDP</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="metode" id="metode2"
                                            value="2" onclick="toggleMetode(2)">
                                        <label class="form-check-label" for="metode2">Bank IDP</label>
                                    </div>
                                </div> --}}
                                {{-- Form Tambahan --}}
                                <div class="form-group">
                                    <label for="proyeksi_karir">Proyeksi Karir</label>
                                    <input type="text" name="proyeksi_karir" id="proyeksi_karir"
                                        class="form-control @error('proyeksi_karir') is-invalid @enderror"
                                        value="{{ old('proyeksi_karir') }}">
                                </div>
                                <div class="form-group">
                                    <label for="deskripsi_idp">Deskripsi</label>
                                    <textarea name="deskripsi_idp" id="deskripsi_idp" class="form-control @error('deskripsi_idp') is-invalid @enderror"
                                        style="height:6rem;">{{ old('deskripsi_idp') }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Mentor</label>
                                    <select name="id_mentor" class="form-control @error('id_mentor') is-invalid @enderror">
                                        <option value="">-- Pilih Mentor --</option>
                                        @foreach ($mentors as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }} -
                                                {{ $item->divisi->nama_divisi }}
                                                -
                                                {{ $item->penempatan->nama_penempatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Supervisor</label>
                                    <select name="id_supervisor"
                                        class="form-control @error('id_supervisor') is-invalid @enderror">
                                        <option value="">-- Pilih Supervisor --</option>
                                        @foreach ($supervisors as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }} -
                                                {{ $item->divisi->nama_divisi }}
                                                -
                                                {{ $item->penempatan->nama_penempatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="waktu_muali">Waktu Mulai</label>
                                        <input type="date" name="waktu_mulai" id="waktu_mulai"
                                            class="form-control @error('waktu_mulai') is-invalid @enderror"
                                            value="{{ old('waktu_mulai') }}">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="waktu_selesai">Waktu Selesai</label>
                                        <input type="date" name="waktu_selesai" id="waktu_selesai"
                                            class="form-control @error('waktu_selesai') is-invalid @enderror"
                                            value="{{ old('waktu_selesai') }}">
                                    </div>
                                </div>

                                <div class="card-header">
                                    <h4>Kompetensi</h4>
                                </div>

                                <div class="card-header text-left">
                                    <button type="button" id="btn-tambah-kompetensi" class="btn btn-primary mb-3"
                                        data-toggle="modal" data-target="#modalTambahKompetensi">
                                        <i class="fas fa-plus-circle"></i> Tambah Kompetensi
                                    </button>
                                </div>

                                <div class="card-header">
                                    <h4>Daftar Hard Kompetensi</h4>
                                </div>
                                <div class="card-header">
                                    <table class="table table-bordered table-hover" id="tabel-hard">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="25%">Nama Kompetensi</th>
                                                <th width="20%">Metode Belajar</th>
                                                <th width="20%">Sasaran</th>
                                                <th width="20%">Aksi (Implementasi)</th>
                                                <th width="15%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>

                                <div class="card-header">
                                    <h4>Daftar Soft Kompetensi</h4>
                                </div>
                                <div class="card-header">
                                    <table class="table table-bordered table-hover" id="tabel-soft">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="25%">Nama Kompetensi</th>
                                                <th width="20%">Metode Belajar</th>
                                                <th width="20%">Sasaran</th>
                                                <th width="20%">Aksi (implementasi)</th>
                                                <th width="20%">Peran</th>
                                                <th width="15%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>

                                <div class="text-right mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan IDP
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Modal Tambah Kompetensi -->
        <div class="modal fade" id="modalTambahKompetensi" tabindex="-1" role="dialog"
            aria-labelledby="modalTambahKompetensiLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalTambahKompetensiLabel">Tambah Kompetensi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                                <select id="modalMetodeBelajar" multiple
                                    placeholder="Pilih satu atau lebih metode belajar">
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

    @push('styles')
        <style>
            .table th {
                vertical-align: middle !important;
                text-align: center;
            }

            .table td {
                vertical-align: middle !important;
            }

            .kompetensi-item {
                border-left: 4px solid #4e73df;
                background-color: #f8f9fc;
                padding: 15px;
                margin-bottom: 15px;
                border-radius: 4px;
            }

            .select2-container--default .select2-selection--multiple {
                border: 1px solid #d1d3e2;
            }

            .tom-select {
                width: 100% !important;
            }
        </style>
    @endpush

    @push('scripts')
        <!-- Template JS File -->
        <script src="{{ asset('js/scripts.js') }}"></script>
        <script src="{{ asset('js/custom.js') }}"></script>
        <!-- Tom Select JS -->
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
        <script>
            // Gunakan event listener pada radio button itu sendiri, atau pada name 'metode'
            document.querySelectorAll('input[name="metode"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    toggleMetode(this.value); // Panggil fungsi toggleMetode dengan nilai radio yang dipilih
                });
            });

            // Panggil toggleMetode saat halaman pertama kali dimuat untuk memastikan tampilan awal sesuai
            // berdasarkan radio button yang checked
            // Inisialisasi TomSelect untuk Metode Belajar - PERBAIKAN UTAMA
            let tomSelectMetodeBelajar;
            if (document.getElementById('modalMetodeBelajar')) {
                tomSelectMetodeBelajar = new TomSelect("#modalMetodeBelajar", {
                    plugins: ['remove_button'],
                    placeholder: "Pilih satu atau lebih metode belajar"
                });
            }

            const kompetensiData = {
                "Hard Kompetensi": @json($kompetensis->where('jenis_kompetensi', 'Hard Kompetensi')->values()),
                "Soft Kompetensi": @json($kompetensis->where('jenis_kompetensi', 'Soft Kompetensi')->values())
            };
            let kompetensiIndex = 0;
            let daftarHard = [];
            let daftarSoft = [];

            // Fungsi untuk mengisi dropdown kompetensi berdasarkan jenis
            function renderKompetensiOptions(jenis_kompetensi) {
                const kompetensiDropdown = document.getElementById('modalKompetensiDropdown');
                if (!kompetensiDropdown) return;

                kompetensiDropdown.innerHTML = '<option value="">-- Pilih Kompetensi --</option>';

                kompetensiData[jenis_kompetensi].forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id_kompetensi;
                    option.textContent = item.nama_kompetensi;
                    kompetensiDropdown.appendChild(option);
                });
            }

            // Inisialisasi pertama kali
            renderKompetensiOptions('Hard Kompetensi');

            // Event listener untuk perubahan jenis kompetensi di modal
            document.getElementById('modalJenisKompetensi').addEventListener('change', function() {
                renderKompetensiOptions(this.value);
            });

            // Event listener untuk tombol simpan kompetensi
            document.getElementById('btnSimpanKompetensi').addEventListener('click', function() {
                const jenis = document.getElementById('modalJenisKompetensi').value;
                const kompetensiDropdown = document.getElementById('modalKompetensiDropdown');
                const kompetensiId = kompetensiDropdown.value;
                const kompetensiText = kompetensiDropdown.options[kompetensiDropdown.selectedIndex].text;

                const metodeSelect = document.getElementById('modalMetodeBelajar');
                const selectedOptions = Array.from(metodeSelect.selectedOptions);
                const metodeIds = selectedOptions.map(opt => opt.value);
                const metodeText = selectedOptions.map(opt => opt.textContent).join(', ');

                const sasaran = document.getElementById('modalSasaran').value;
                const aksi = document.getElementById('modalAksi').value;
                const peran = document.getElementById('modalPeranDropdown').value;

                // Validasi
                if (!kompetensiId) {
                    alert("Harap pilih kompetensi");
                    return;
                }

                if (metodeIds.length === 0) {
                    alert("Harap pilih minimal satu metode belajar");
                    return;
                }

                if (!sasaran || !aksi) {
                    alert("Harap isi sasaran dan aksi");
                    return;
                }

                const data = {
                    kompetensiId,
                    kompetensiText,
                    metodeIds,
                    metodeText,
                    sasaran,
                    aksi,
                    peran
                };

                if (jenis === "Hard Kompetensi") {
                    daftarHard.push(data);
                } else {
                    daftarSoft.push(data);
                }

                renderTabel();
                resetFormModal();
                $('#modalTambahKompetensi').modal('hide');
            });

            function renderTabel() {
                const tbodyHard = document.querySelector('#tabel-hard tbody');
                const tbodySoft = document.querySelector('#tabel-soft tbody');
                const form = document.querySelector('form');

                // Kosongkan input hidden sebelumnya
                document.querySelectorAll('input[name^="kompetensi["]').forEach(el => el.remove());

                // Render tabel Hard Kompetensi
                tbodyHard.innerHTML = '';
                daftarHard.forEach((item, index) => {
                    tbodyHard.innerHTML += `
        <tr>
            <td>${item.kompetensiText}</td>
            <td>${item.metodeText}</td>
            <td>${item.sasaran}</td>
            <td>${item.aksi}</td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="hapusKompetensi('hard', ${index})">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </td>
        </tr>
    `;

                    // Tambahkan input hidden dengan format yang benar
                    addHiddenInputs(item, index);
                });

                // Render tabel Soft Kompetensi
                tbodySoft.innerHTML = '';
                daftarSoft.forEach((item, index) => {
                    const globalIndex = daftarHard.length + index;

                    tbodySoft.innerHTML += `
        <tr>
            <td>${item.kompetensiText}</td>
            <td>${item.metodeText}</td>
            <td>${item.sasaran}</td>
            <td>${item.aksi}</td>
            <td>${item.peran}</td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="hapusKompetensi('soft', ${index})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;

                    // Tambahkan input hidden dengan format yang benar
                    addHiddenInputs(item, globalIndex);
                });
            }

            function addHiddenInputs(item, index) {
                const form = document.getElementById('mainIdpForm');

                // Buat div container untuk input hidden
                const container = document.createElement('div');

                // Tambahkan input hidden untuk data kompetensi
                container.innerHTML += `
        <input type="hidden" name="kompetensi[${index}][id_kompetensi]" value="${item.kompetensiId}">
        <input type="hidden" name="kompetensi[${index}][sasaran]" value="${item.sasaran}">
        <input type="hidden" name="kompetensi[${index}][aksi]" value="${item.aksi}">
        <input type="hidden" name="kompetensi[${index}][peran]" value="${item.peran}">

    `;

                // Tambahkan input hidden untuk metode belajar (array)
                item.metodeIds.forEach(metodeId => {
                    container.innerHTML += `
            <input type="hidden" name="kompetensi[${index}][id_metode_belajar][]" value="${metodeId}">
        `;
                });

                form.appendChild(container);
            }

            function hapusKompetensi(tipe, index) {
                if (confirm('Apakah Anda yakin ingin menghapus kompetensi ini?')) {
                    if (tipe === 'hard') {
                        daftarHard.splice(index, 1);
                    } else {
                        daftarSoft.splice(index, 1);
                    }
                    renderTabel();
                }
            }

            function resetFormModal() {
                // Reset jenis kompetensi ke default
                $('#modalJenisKompetensi').val('Hard Kompetensi').trigger('change');

                // Reset input teks
                $('#modalSasaran').val('');
                $('#modalAksi').val('');

                // Reset dropdown jenjang
                $('#modalJenjangDropdown').val('').trigger('change');

                // Kosongkan dropdown jabatan
                $('#modalJabatanDropdown').empty().append('<option value="">Pilih Jabatan</option>');

                // Kosongkan dropdown kompetensi
                $('#modalKompetensiDropdown').empty().append('<option value="">Pilih Kompetensi</option>');

                // Reset metode belajar (kalau pakai Tom Select misalnya)
                if (typeof tomSelectMetodeBelajar !== 'undefined') {
                    tomSelectMetodeBelajar.clear();
                }
            }


            // Inisialisasi modal saat ditampilkan
            $('#modalTambahKompetensi').on('show.bs.modal', function() {
                resetFormModal();
            });
            $(document).ready(function() {
                // Sembunyikan jenjang dan jabatan jika jenis kompetensi bukan hard
                toggleHardKompetensiFields();

                // Tampilkan/Hide input berdasarkan pilihan jenis kompetensi
                $('#modalJenisKompetensi').on('change', function() {
                    toggleHardKompetensiFields();
                });

                function toggleHardKompetensiFields() {
                    let jenis = $('#modalJenisKompetensi').val();
                    if (jenis === 'Hard Kompetensi') {
                        $('#modalJenjangDropdown').closest('.form-group').show();
                        $('#modalJabatanDropdown').closest('.form-group').show();
                        $('#formPeranGroup').closest('.form-group').hide(); // Hide Peran Kompetensi for Hard Kompetensi
                        $('#modalPeranDropdown').val('umum');
                    } else {
                        $('#modalJenjangDropdown').closest('.form-group').hide();
                        $('#modalJabatanDropdown').closest('.form-group').hide();
                        $('#formPeranGroup').show(); // Show Peran Kompetensi for Soft Kompetensi
                        renderKompetensiOptions('Soft Kompetensi');
                    }
                }

                // Saat jenjang dipilih -> ambil jabatan berdasarkan jenjang
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
                    }
                });

                // Saat jabatan dipilih -> ambil daftar hard kompetensi
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
                    }
                });
            });
        </script>
    @endpush
