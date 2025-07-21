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
                                    <label for="deskripsi_idp">Deskripsi/ Alasan Proyeksi Karir</label>
                                    <textarea name="deskripsi_idp" id="deskripsi_idp" class="form-control @error('deskripsi_idp') is-invalid @enderror"
                                        style="height:6rem;">{{ old('deskripsi_idp') }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="id_mentor">Mentor</label>
                                    <select name="id_mentor" id="id_mentor"
                                        class="tom-select @error('id_mentor') is-invalid @enderror">
                                        <option value="">-- Pilih Mentor --</option>
                                        @foreach ($mentors as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->name }} - {{ $item->divisi->nama_divisi }} -
                                                {{ $item->penempatan->nama_penempatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <!-- Keterangan tambahan untuk memandu pengguna -->
                                    <small class="form-text text-muted">
                                        Pilih mentor yang sesuai dengan bidang atau divisi Anda. Mentor akan memberikan
                                        bimbingan selama program.
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label>Supervisor</label>
                                    <select name="id_supervisor" id="id_supervisor"
                                        class="tom-select @error('id_supervisor') is-invalid @enderror">
                                        <option value="">-- Pilih Supervisor --</option>
                                        @foreach ($supervisors as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }} -
                                                {{ $item->divisi->nama_divisi }} -
                                                {{ $item->penempatan->nama_penempatan }}</option>
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
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="tabel-hard">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="25%">Nama Kompetensi</th>
                                                    <th width="20%">Metode Belajar</th>
                                                    <th width="30%">Sasaran</th>
                                                    <th width="30%">Aksi (Implementasi)</th>
                                                    <th width="15%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="card-header">
                                    <h4>Daftar Soft Kompetensi</h4>
                                </div>
                                <div class="card" style="border-left: 5px solid #28a745; background-color: #e6f9d7;">
                                    <div class="card-body" style="color: #212529;">
                                        Soft Kompetensi Terdiri dari 3 (tiga) aspek yaitu: <br>
                                        <strong> 1 Kompetensi Umum </strong> - kompetensi yang harus dimiliki oleh semua
                                        karyawan pada semua lavel direktorat, jenjang atau jabatan <br>
                                        <strong> 1 Kompetensi Kunci </strong> - kompetensi penting yang harus dimiliki oleh
                                        semua karyawan berdasarkan direktorat (kunci_enable atau kunci_core atau
                                        kunci_business) <br>
                                        <strong> Kompetensi Utama </strong> - kompetensi yang secara khhusus harus dimiliki
                                        oleh karyawan pada semua rumpun direktorat, jenjang atau jabatan
                                    </div>
                                </div>
                                <div class="card-header">
                                    <div class="table-responsive">
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
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
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
                                    <select class="tom-select jenis_kompetensi" id="modalJenisKompetensi">
                                        <option value="Hard Kompetensi">Hard Kompetensi</option>
                                        <option value="Soft Kompetensi">Soft Kompetensi</option>
                                    </select>
                                </div>
                                <!-- Jenjang -->
                                <div class="form-group col-md-6" id="formJenjangGroup">
                                    <label>Jenjang</label>
                                    <select class="tom-select" id="modalJenjangDropdown">
                                        <option value="">Pilih Jenjang</option>
                                        @foreach ($listJenjang as $item)
                                            <option value="{{ $item->id_jenjang }}">{{ $item->nama_jenjang }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Jabatan -->
                                <div class="form-group col-md-6" id="formJabatanGroup">
                                    <label>Jabatan</label>
                                    <select class="tom-select" id="modalJabatanDropdown">
                                        <option value="">Pilih Jabatan</option>
                                        @foreach ($listJabatan as $item)
                                            <option value="{{ $item->id_jabatan }}">{{ $item->nama_jabatan }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Kompetensi</label>
                                    <select class="tom-select kompetensi-dropdown" id="modalKompetensiDropdown">
                                        <option value="">Pilih Kompetensi</option>
                                        <!-- Opsi akan diisi oleh JS -->
                                    </select>
                                </div>
                                <!-- Peran Kompetensi, hidden default -->
                                <div class="form-group col-md-12" id="formPeranGroup">
                                    <label>Peran Kompetensi</label>
                                    <select class="tom-select" id="modalPeranDropdown" name="peran">
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
            let lastJenjangHard = null;
            let lastJabatanHard = null;
            document.querySelectorAll('input[name="metode"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    toggleMetode(this.value);
                });
            });
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
                if (!kompetensiDropdown || !kompetensiDropdown.tomselect) return;

                const ts = kompetensiDropdown.tomselect;
                ts.clearOptions();

                ts.addOption({
                    value: '',
                    text: '-- Pilih Kompetensi --'
                });

                const data = kompetensiData[jenis_kompetensi];
                if (!data || data.length === 0) return;

                data.forEach(item => {
                    ts.addOption({
                        value: item.id_kompetensi,
                        text: item.nama_kompetensi
                    });
                });

                ts.refreshOptions(false);
                ts.setValue('');
            }

            // Inisialisasi pertama kali
            renderKompetensiOptions('Hard Kompetensi');

            // Event listener untuk perubahan jenis kompetensi di modal
            document.getElementById('modalJenisKompetensi').addEventListener('change', function() {
                renderKompetensiOptions(this.value);
            });
            $('#mainIdpForm').submit(function(event) {
                // Cek validasi kompetensi sebelum submit
                if (!validateCompetencies()) {
                    event.preventDefault(); // Mencegah form untuk disubmit jika validasi gagal
                }
            });
            // Event listener untuk tombol simpan kompetensi
            // Fungsi untuk menangani klik simpan kompetensi
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

                // Validasi untuk kompetensi yang sudah ada
                if (kompetensiId && checkIfCompetencyExists(kompetensiId)) {
                    alert("Kompetensi ini sudah ada di tabel Kompetensi.");
                    return; // Jangan menambahkannya lagi
                }

                // Validasi lainnya
                if (!kompetensiId || metodeIds.length === 0 || !sasaran || !aksi) {
                    alert("Harap pilih kompetensi, metode belajar, sasaran, dan aksi.");
                    return;
                }
                if (jenis === 'Soft Kompetensi') {
                    const kunciTypes = ['kunci_enabler', 'kunci_core', 'kunci_bisnis'];
                    const isKunci = kunciTypes.includes(peran);

                    if (isKunci) {
                        const alreadyExists = daftarSoft.some(item => kunciTypes.includes(item.peran));
                        if (alreadyExists) {
                            alert('Hanya boleh satu Soft Kompetensi dengan peran Kunci (Enabler/Core/Business).');
                            return;
                        }
                    }
                }

                // Menambahkan kompetensi ke dalam daftar berdasarkan jenis kompetensi
                const newCompetency = {
                    kompetensiId,
                    kompetensiText,
                    metodeIds,
                    metodeText,
                    sasaran,
                    aksi,
                    peran
                };

                if (jenis === "Hard Kompetensi") {
                    daftarHard.push(newCompetency);
                } else {
                    daftarSoft.push(newCompetency);
                }

                renderTabel(); // Update tabel untuk menampilkan kompetensi yang baru ditambahkan
                resetFormModal(); // Reset form modal setelah kompetensi ditambahkan
                $('#modalTambahKompetensi').modal('hide'); // Menutup modal setelah kompetensi ditambahkan

                updateKompetensiDropdown(); // Memperbarui dropdown kompetensi agar yang sudah dipilih tidak muncul lagi
            });


            function validateCompetencies() {
                const totalHardCompetencies = daftarHard.length;
                const totalSoftCompetencies = daftarSoft.length;

                // Cek apakah ada minimal 3 kompetensi pada hard atau soft
                if (totalHardCompetencies < 3 && totalSoftCompetencies < 3) {
                    alert(
                        "Anda harus memilih minimal 3 Kompetensi Hard atau 3 Kompetensi Soft (Kompetensi Utama, Kompetensi Umum, dan Kompetensi Kunci)."
                    );
                    return false; // Menghentikan submit
                }
                // Validasi harus ada 1 peran 'utama' (boleh dari hard atau soft)
                const totalUtama = [...daftarHard, ...daftarSoft].filter(item => item.peran === 'utama').length;
                if (totalUtama !== 1) {
                    alert("Harus memilih tepat **1 Kompetensi** dengan peran 'Utama'.");
                    return false;
                }
                // Validasi: harus ada satu peran kunci (enabler/core/business) di soft kompetensi
                const kunciTypes = ['kunci_enabler', 'kunci_core', 'kunci_bisnis'];
                const kunciFound = daftarSoft.some(item => kunciTypes.includes(item.peran));
                if (!kunciFound) {
                    alert('Minimal satu Soft Kompetensi harus memiliki peran: Kunci Enabler, Kunci Core, atau Kunci Business.');
                    return false;
                }
                return true; // Lanjutkan submit jika validasi lulus
            }


            function checkIfCompetencyExists(competencyId) {
                // Cek di daftar hard dan soft kompetensi
                const allCompetencies = [...daftarHard, ...daftarSoft];
                return allCompetencies.some(item => item.kompetensiId === competencyId);
            }

            // Fungsi untuk merender tabel setelah kompetensi ditambahkan
            function renderTabel() {
                const tbodyHard = document.querySelector('#tabel-hard tbody');
                const tbodySoft = document.querySelector('#tabel-soft tbody');
                const form = document.getElementById('mainIdpForm'); // Ambil form untuk menambahkan hidden inputs

                // Kosongkan tabel sebelum render
                tbodyHard.innerHTML = '';
                tbodySoft.innerHTML = '';

                // Kosongkan input hidden sebelumnya
                document.querySelectorAll('input[name^="kompetensi["]').forEach(el => el.remove());
                let globalIndex = 0;

                // Render tabel Hard Kompetensi
                daftarHard.forEach((item, index) => {
                    tbodyHard.innerHTML += `
            <tr>
                <td>${item.kompetensiText}</td>
                <td>${item.metodeText}</td>
                <td>${item.sasaran}</td>
                <td>${item.aksi}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger rounded-circle shadow-sm" 
                    style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"
                    onclick="hapusKompetensi('hard', ${index})">
                        <i class="fas fa-trash text-danger"></i>
                    </button>
                </td>
            </tr>
        `;

                    // Tambahkan input hidden untuk data kompetensi
                    addHiddenInputs(item, globalIndex++);
                });

                // Render tabel Soft Kompetensi
                daftarSoft.forEach((item, index) => {
                    tbodySoft.innerHTML += `
            <tr>
                <td>${item.kompetensiText}</td>
                <td>${item.metodeText}</td>
                <td>${item.sasaran}</td>
                <td>${item.aksi}</td>
                <td>${item.peran}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger rounded-circle shadow-sm" 
                    style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"
                    onclick="hapusKompetensi('soft', ${index})">
                        <i class="fas fa-trash text-danger"></i>
                    </button>
                </td>
            </tr>
        `;

                    // Tambahkan input hidden untuk data kompetensi
                    addHiddenInputs(item, globalIndex++);
                });
            }
            // Fungsi untuk menambahkan input hidden ke dalam form
            function addHiddenInputs(item, index) {
                const form = document.getElementById('mainIdpForm');
                const container = document.createElement('div');

                // Menambahkan input hidden untuk data kompetensi
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

                // Menambahkan input hidden ke dalam form
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
                const jenis = document.getElementById('modalJenisKompetensi');
                if (jenis && jenis.tomselect) {
                    jenis.tomselect.setValue('Hard Kompetensi');
                }

                $('#modalSasaran').val('');
                $('#modalAksi').val('');

                // Reset dropdown jenjang dengan nilai terakhir
                const jenjangDropdown = document.getElementById('modalJenjangDropdown');
                if (jenjangDropdown && jenjangDropdown.tomselect) {
                    if (lastJenjangHard) {
                        jenjangDropdown.tomselect.setValue(lastJenjangHard);
                    } else {
                        jenjangDropdown.tomselect.clear(true);
                    }
                }

                // Reset dropdown jabatan dengan nilai terakhir
                const jabatanDropdown = document.getElementById('modalJabatanDropdown');
                if (jabatanDropdown && jabatanDropdown.tomselect) {
                    if (lastJabatanHard) {
                        jabatanDropdown.tomselect.setValue(lastJabatanHard);
                    } else {
                        jabatanDropdown.tomselect.clearOptions();
                        jabatanDropdown.tomselect.setValue('');
                        jabatanDropdown.tomselect.addOption({
                            value: "",
                            text: "Pilih Jabatan"
                        });
                        jabatanDropdown.tomselect.refreshOptions(false);
                    }
                }

                // Reset dropdown kompetensi
                const kompetensiDropdown = document.getElementById('modalKompetensiDropdown');
                if (kompetensiDropdown && kompetensiDropdown.tomselect) {
                    kompetensiDropdown.tomselect.clearOptions();
                    kompetensiDropdown.tomselect.setValue('');
                    kompetensiDropdown.tomselect.addOption({
                        value: "",
                        text: "Pilih Kompetensi"
                    });
                    kompetensiDropdown.tomselect.refreshOptions(false);
                }

                // Reset metode belajar
                if (typeof tomSelectMetodeBelajar !== 'undefined' && tomSelectMetodeBelajar) {
                    tomSelectMetodeBelajar.clear();
                }

                // Reset peran ke default
                const peranDropdown = document.getElementById('modalPeranDropdown');
                if (peranDropdown && peranDropdown.tomselect) {
                    peranDropdown.tomselect.setValue('umum');
                }

                // Trigger jenis kompetensi agar tampilan ikut menyesuaikan
                $('#modalJenisKompetensi').trigger('change');
            }

            // Fungsi untuk memeriksa apakah kompetensi sudah ada dalam daftar
            function checkIfCompetencyExists(competencyId) {
                // Cek di daftar hard dan soft kompetensi
                const allCompetencies = [...daftarHard, ...daftarSoft];
                return allCompetencies.some(item => item.kompetensiId === competencyId);
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
                    lastJenjangHard = jenjangId; // Simpan jenjang yang terakhir dipilih
                    if (jenjangId) {
                        $.ajax({
                            url: '/admin/datamaster/behavior/idp/get-jabatan-by-jenjang/' + jenjangId,
                            type: 'GET',
                            success: function(data) {
                                const jabatanDropdown = document.querySelector(
                                    '#modalJabatanDropdown');

                                // Reset TomSelect-nya jika sudah terinisialisasi
                                if (jabatanDropdown.tomselect) {
                                    jabatanDropdown.tomselect.clearOptions(); // Hapus opsi lama
                                    jabatanDropdown.tomselect.addOption({
                                        value: '',
                                        text: 'Pilih Jabatan'
                                    }); // Tambahkan opsi default
                                }

                                // Tambahkan jabatan baru
                                data.forEach(function(jabatan) {
                                    jabatanDropdown.tomselect.addOption({
                                        value: jabatan.id_jabatan,
                                        text: jabatan.nama_jabatan
                                    });
                                });

                                // Refresh dropdown untuk memastikan opsi diperbarui
                                jabatanDropdown.tomselect.refreshOptions(false);
                            }
                        });
                    }
                });

                // Saat jabatan dipilih -> ambil daftar hard kompetensi
                $('#modalJabatanDropdown').on('change', function() {
                    let jabatanId = $(this).val();
                    lastJabatanHard = jabatanId;
                    if (jabatanId) {
                        $.ajax({
                            url: '/admin/datamaster/behavior/idp/get-kompetensi-by-jabatan/' +
                                jabatanId,
                            type: 'GET',
                            success: function(data) {
                                let kompetensiDropdown = document.querySelector(
                                    '#modalKompetensiDropdown');

                                // Ambil jenis kompetensi
                                let jenis = document.getElementById('modalJenisKompetensi').value;

                                // Jika jenis kompetensi bukan 'Hard Kompetensi', hentikan
                                if (jenis !== 'Hard Kompetensi') return;

                                // Jika TomSelect sudah ada, bersihkan opsi
                                if (kompetensiDropdown && kompetensiDropdown.tomselect) {
                                    kompetensiDropdown.tomselect.clearOptions(); // Hapus opsi lama
                                    kompetensiDropdown.tomselect.addOption({
                                        value: '',
                                        text: 'Pilih Kompetensi'
                                    }); // Tambahkan opsi default
                                }

                                // Tambahkan kompetensi baru dari response data
                                if (data && Array.isArray(data)) {
                                    data.forEach(function(komp) {
                                        kompetensiDropdown.tomselect.addOption({
                                            value: komp.id_kompetensi,
                                            text: komp.nama_kompetensi
                                        });
                                    });

                                    // Refresh dan tampilkan opsi yang baru
                                    if (kompetensiDropdown.tomselect) {
                                        kompetensiDropdown.tomselect.refreshOptions(false);
                                    }
                                }
                            },
                            error: function(err) {
                                console.error("Error fetching kompetensi:", err);
                            }
                        });
                    } else {
                        // Jika jabatanId kosong, reset kompetensi dropdown
                        let kompetensiDropdown = document.querySelector('#modalKompetensiDropdown');
                        if (kompetensiDropdown && kompetensiDropdown.tomselect) {
                            kompetensiDropdown.tomselect.clearOptions();
                            kompetensiDropdown.tomselect.addOption({
                                value: '',
                                text: 'Pilih Kompetensi'
                            });
                            kompetensiDropdown.tomselect.refreshOptions(false);
                        }
                    }
                });
                // Fungsi untuk memperbarui dropdown Kompetensi dengan menghapus yang sudah ada
                function updateKompetensiDropdown() {
                    const kompetensiDropdown = document.getElementById('modalKompetensiDropdown');
                    if (!kompetensiDropdown || !kompetensiDropdown.tomselect) return;

                    const ts = kompetensiDropdown.tomselect;
                    ts.clearOptions(); // Hapus semua opsi yang ada di dropdown

                    ts.addOption({
                        value: '',
                        text: '-- Pilih Kompetensi --'
                    });

                    const jenis = document.getElementById('modalJenisKompetensi').value;
                    const data = kompetensiData[jenis];

                    if (!data || data.length === 0) return;

                    // Menambahkan kompetensi yang belum dimasukkan
                    data.forEach(item => {
                        // Cek apakah kompetensi sudah ada di daftar (Hard/Soft)
                        const isAlreadyAdded = [...daftarHard, ...daftarSoft].some(existingItem => existingItem
                            .kompetensiId === item.id_kompetensi);

                        if (!isAlreadyAdded) {
                            ts.addOption({
                                value: item.id_kompetensi,
                                text: item.nama_kompetensi
                            });
                        }
                    });

                    ts.refreshOptions(false); // Refresh dropdown setelah menambahkan opsi yang baru
                }
                let modalDropdownsInitialized = false;

                $('#modalTambahKompetensi').on('shown.bs.modal', function() {
                    if (!modalDropdownsInitialized) {
                        new TomSelect('#modalJenisKompetensi', {
                            plugins: ['dropdown_input'],
                            allowEmptyOption: true
                        });

                        new TomSelect('#modalJenjangDropdown', {
                            plugins: ['dropdown_input'],
                            allowEmptyOption: true
                        });

                        new TomSelect('#modalJabatanDropdown', {
                            plugins: ['dropdown_input'],
                            allowEmptyOption: true
                        });

                        new TomSelect('#modalKompetensiDropdown', {
                            plugins: ['dropdown_input'],
                            allowEmptyOption: true
                        });

                        new TomSelect('#modalPeranDropdown', {
                            plugins: ['dropdown_input'],
                            allowEmptyOption: true
                        });

                        modalDropdownsInitialized = true;
                    }
                    if (jenis === 'Soft Kompetensi') {
                        const kunciTypes = ['kunci_enabler', 'kunci_core', 'kunci_bisnis'];
                        const alreadyExists = daftarSoft.some(item => kunciTypes.includes(item.peran));

                        if (alreadyExists) {
                            alert(
                                'Soft Kompetensi dengan peran Kunci sudah dipilih. Tidak bisa menambah lagi.'
                            );
                            e.preventDefault();
                            return false;
                        }
                    }
                });
            });
            new TomSelect('#id_mentor', {
                plugins: ['dropdown_input'],
                create: false,
                allowEmptyOption: true
            });

            new TomSelect('#id_supervisor', {
                plugins: ['dropdown_input'],
                create: false,
                allowEmptyOption: true
            });
        </script>
    @endpush
