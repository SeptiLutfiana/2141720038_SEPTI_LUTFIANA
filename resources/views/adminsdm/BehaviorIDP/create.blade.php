    @extends('layouts.app')

    @section('title', 'Tambah IDP Karyawan')

    @section('main')
        <div class="main-content">
            <section class="section">
                <div class="section-header">
                    <h1>Tambah IDP Karyawan</h1>
                    <div class="section-header-breadcrumb">
                        <div class="breadcrumb-item active"><a href="{{ route('adminsdm.dashboard') }}">Dashboard</a></div>
                        <div class="breadcrumb-item"><a href="{{ route('adminsdm.BehaviorIDP.ListIDP.indexBankIdp') }}">Data
                                Bank
                                IDP</a>
                        </div>
                        <div class="breadcrumb-item"><a href="{{ route('adminsdm.BehaviorIDP.indexGiven') }}">Data DP</a>
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
                            <strong>Petunjuk untuk Admin:</strong>
                            <ul>
                                <li><strong>Given IDP</strong>: Gunakan fitur ini untuk langsung memberikan rencana IDP
                                    tertentu
                                    kepada karyawan. IDP yang diberikan akan otomatis muncul di halaman karyawan sesuai
                                    jenjang,
                                    learning group dan supervisor yang dipilih.</li>
                                <li><strong>Bank IDP</strong>: Gunakan fitur ini untuk menambahkan daftar perencanaan IDP
                                    umum
                                    yang dapat dipilih secara mandiri oleh karyawan sesuai proyeksi karir masing-masing.
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4>Tambah Individual Development Plan</h4>
                        </div>
                        <div class="card-body">
                            <form id="mainIdpForm" action="{{ route('adminsdm.BehaviorIDP.store') }}" method="POST"> @csrf

                                <div class="form-group">
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
                                </div>
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
                                {{-- Form Metode 1 --}}
                                <div id="form-metode1">
                                    <div class="row">
                                        <div class="form-group col-md-3">
                                            <label for="filter-jenjang">Filter Jenjang</label>
                                            <select id="filter-jenjang" class="tom-select">
                                                <option value="" selected disabled hidden>Semua Jenjang</option>
                                                @foreach ($listJenjang as $item)
                                                    <option value="{{ $item->id_jenjang }}">{{ $item->nama_jenjang }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="filter-lg">Filter Learning Group</label>
                                            <select id="filter-lg" class="tom-select">
                                                <option value="" selected disabled hidden>Semua Learning Group
                                                </option>
                                                @foreach ($listLG as $item)
                                                    <option value="{{ $item->id_LG }}">{{ $item->nama_LG }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="filter-divisi">Filter Divisi</label>
                                            <select id="filter-divisi" class="tom-select">
                                                <option value="" selected disabled hidden>Semua Divisi</option>
                                                @foreach ($listDivisi as $item)
                                                    <option value="{{ $item->id_divisi }}">{{ $item->nama_divisi }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="filter-penempatan">Filter Penempatan</label>
                                            <select id="filter-penempatan" class="tom-select">
                                                <option value="" selected disabled hidden>Semua Penempatan</option>
                                                @foreach ($listPenempatan as $item)
                                                    <option value="{{ $item->id_penempatan }}">
                                                        {{ $item->nama_penempatan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">

                                        <label for="filter-penempatan">Pilih Karyawan</label>

                                        <select name="id_user[]" id="select-karyawan" class="w-100" multiple>
                                            @foreach ($karyawans as $item)
                                                <option value="{{ $item->id }}"
                                                    data-jenjang="{{ $item->jenjang->id_jenjang ?? '' }}"
                                                    data-jenjang-text="{{ $item->jenjang->nama_jenjang ?? 'N/A' }}"
                                                    data-lg="{{ $item->learningGroup->id_LG ?? '' }}"
                                                    data-lg-text="{{ $item->learningGroup->nama_LG ?? 'N/A' }}"
                                                    data-divisi="{{ $item->divisi->id_divisi ?? '' }}"
                                                    data-divisi-text="{{ $item->divisi->nama_divisi ?? 'N/A' }}"
                                                    data-penempatan="{{ $item->penempatan->id_penempatan ?? '' }}"
                                                    data-penempatan-text="{{ $item->penempatan->nama_penempatan ?? 'N/A' }}">
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Mentor</label>
                                        <select name="id_mentor" id="mentor-select">
                                            <option value="" selected disabled hidden>Pilih Mentor</option>
                                            @foreach ($mentors as $item)
                                                <option value="{{ $item->id }}">
                                                    {{ $item->name }} - {{ $item->divisi->nama_divisi }} -
                                                    {{ $item->penempatan->nama_penempatan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Supervisor</label>
                                        <select name="id_supervisor" id="spv-select">
                                            <option value="" selected disabled hidden>Pilih Supervisor</option>
                                            @foreach ($supervisors as $item)
                                                <option value="{{ $item->id }}">
                                                    {{ $item->name }} - {{ $item->divisi->nama_divisi }} -
                                                    {{ $item->penempatan->nama_penempatan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Form Metode 2 --}}
                                <div id="form-metode2" style="display: none;">
                                    <div class="form-group">
                                        <label>Jenjang</label>
                                        <select name="id_jenjang_bank"
                                            class="tom-select @error('id_jenjang_bank') is-invalid @enderror">
                                            <option value="" selected disabled hidden>-- Pilih Jenjang --</option>
                                            @foreach ($listJenjang as $item)
                                                <option value="{{ $item->id_jenjang }}">{{ $item->nama_jenjang }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Learning Group</label>
                                        <select name="id_LG" class="tom-select @error('id_LG') is-invalid @enderror">
                                            <option value="" selected disabled hidden>-- Pilih Learning Group --
                                            </option>
                                            @foreach ($listLG as $item)
                                                <option value="{{ $item->id_LG }}">{{ $item->nama_LG }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Supervisor</label>
                                        <select name="id_supervisor_jenjang"
                                            class="tom-select @error('id_supervisor_jenjang') is-invalid @enderror">
                                            <option value="">-- Pilih Supervisor --</option>
                                            @foreach ($supervisors as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }} -
                                                    {{ $item->npk }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="current_applies">Kuota IDP</label>
                                        <input type="number" name="max_applies" id="max_applies" step="1"
                                            class="form-control @error('max_applies') is-invalid @enderror"
                                            value="{{ old('max_applies') }}">
                                    </div>

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
                                                <th width="30%">Sasaran</th>
                                                <th width="30%">Aksi (Implementasi)</th>
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
            // Gunakan event listener pada radio button itu sendiri, atau pada name 'metode'
            document.querySelectorAll('input[name="metode"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    toggleMetode(this.value); // Panggil fungsi toggleMetode dengan nilai radio yang dipilih
                });
            });

            // Panggil toggleMetode saat halaman pertama kali dimuat untuk memastikan tampilan awal sesuai
            // berdasarkan radio button yang checked
            toggleMetode(document.querySelector('input[name="metode"]:checked').value);

            function toggleMetode(metode) {
                const formMetode1 = document.getElementById('form-metode1');
                const formMetode2 = document.getElementById('form-metode2');

                const selectSupervisor1 = formMetode1.querySelector('select[name="id_supervisor"]');
                const selectSupervisor2 = formMetode2.querySelector('select[name="id_supervisor_jenjang"]');
                const selectJenjangBank = formMetode2.querySelector('select[name="id_jenjang_bank"]');
                const selectLgBank = formMetode2.querySelector('select[name="id_LG"]');


                if (metode == 1) { // Given IDP
                    formMetode1.style.display = 'block';
                    formMetode2.style.display = 'none';

                    // Enable elements for Given IDP
                    selectSupervisor1.disabled = false;

                    // Disable elements for Bank IDP
                    selectSupervisor2.disabled = true;
                    selectJenjangBank.disabled = true;
                    selectLgBank.disabled = true;


                } else { // Bank IDP
                    formMetode1.style.display = 'none';
                    formMetode2.style.display = 'block';

                    // Disable elements for Given IDP
                    selectSupervisor1.disabled = true;

                    // Enable elements for Bank IDP
                    selectSupervisor2.disabled = false;
                    selectJenjangBank.disabled = false;
                    selectLgBank.disabled = false;

                }
            }
            let tomSelectKaryawan = new TomSelect("#select-karyawan", {
                plugins: ['remove_button'],
                placeholder: "Pilih satu atau lebih karyawan",
                render: {
                    option: function(data, escape) {
                        // Dapatkan elemen option asli untuk mengambil data attributes
                        const originalOption = document.querySelector(
                            `#select-karyawan option[value="${data.value}"]`);

                        // Jika filter aktif, tampilkan informasi tambahan
                        const jenjangFilter = document.getElementById('filter-jenjang').value;
                        const lgFilter = document.getElementById('filter-lg').value;
                        const divisiFilter = document.getElementById('filter-divisi').value;
                        const penempatanFilter = document.getElementById('filter-penempatan').value;

                        let additionalInfo = [];
                        if (jenjangFilter === '') additionalInfo.push(
                            `Jenjang: ${originalOption.getAttribute('data-jenjang-text') || '-'}`);
                        if (lgFilter === '') additionalInfo.push(
                            `LG: ${originalOption.getAttribute('data-lg-text') || '-'}`);
                        if (divisiFilter === '') additionalInfo.push(
                            `Divisi: ${originalOption.getAttribute('data-divisi-text') || '-'}`);
                        if (penempatanFilter === '') additionalInfo.push(
                            `Penempatan: ${originalOption.getAttribute('data-penempatan-text') || '-'}`);

                        return `<div>
                        <strong>${escape(data.text)}</strong>
                        ${additionalInfo.length > 0 ? `<br><small>${additionalInfo.join(' | ')}</small>` : ''}
                    </div>`;
                    },
                    item: function(data, escape) {
                        return `<div>${escape(data.text)}</div>`;
                    }
                }
            });

            // Filter functions
            function applyFilters() {
                const jenjang = document.getElementById('filter-jenjang').value;
                const lg = document.getElementById('filter-lg').value;
                const divisi = document.getElementById('filter-divisi').value;
                const penempatan = document.getElementById('filter-penempatan').value;

                // Ambil semua <option> asli
                const allOptions = Array.from(document.querySelectorAll('#select-karyawan option'));

                // Filter data berdasarkan atribut
                const filteredOptions = allOptions.filter(option => {
                    const matchJenjang = jenjang === '' || option.getAttribute('data-jenjang') === jenjang;
                    const matchLg = lg === '' || option.getAttribute('data-lg') === lg;
                    const matchDivisi = divisi === '' || option.getAttribute('data-divisi') === divisi;
                    const matchPenempatan = penempatan === '' || option.getAttribute('data-penempatan') === penempatan;
                    return matchJenjang && matchLg && matchDivisi && matchPenempatan;
                });

                // Reset TomSelect options
                tomSelectKaryawan.clearOptions();

                // Tambahkan kembali option hasil filter
                filteredOptions.forEach(option => {
                    tomSelectKaryawan.addOption({
                        value: option.value,
                        text: option.text,
                    });
                });

                // Refresh dropdown
                tomSelectKaryawan.refreshOptions(false);

                // Jika tidak ada hasil
                if (filteredOptions.length === 0) {
                    tomSelectKaryawan.addOption({
                        value: '',
                        text: 'Tidak ada karyawan yang sesuai dengan filter',
                        disabled: true
                    });
                    tomSelectKaryawan.refreshOptions(false);
                }

                tomSelectKaryawan.clear(); // Reset value yang dipilih
            }

            // Add event listeners to filter dropdowns
            document.getElementById('filter-jenjang').addEventListener('change', function() {
                applyFilters();
                // Reset select karyawan jika filter jenjang berubah
                tomSelectKaryawan.clear();
            });

            document.getElementById('filter-lg').addEventListener('change', function() {
                applyFilters();
                // Reset select karyawan jika filter lg berubah
                tomSelectKaryawan.clear();
            });

            document.getElementById('filter-divisi').addEventListener('change', function() {
                applyFilters();
                // Reset select karyawan jika filter divisi berubah
                tomSelectKaryawan.clear();
            });

            document.getElementById('filter-penempatan').addEventListener('change', function() {
                applyFilters();
                // Reset select karyawan jika filter penempatan berubah
                tomSelectKaryawan.clear();
            });


            let tomSelectMetodeBelajar = new TomSelect("#modalMetodeBelajar", {
                plugins: ['remove_button'],
            });

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
        <tr class="text-center">
            <td>${item.kompetensiText}</td>
            <td>${item.metodeText}</td>
            <td>${item.sasaran}</td>
            <td>${item.aksi}</td>
                <td class="text-center">
    <button type="button"
        class="btn btn-outline-danger rounded-circle shadow-sm"
        style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"
        onclick="hapusKompetensi('hard', ${index})">
        <i class="fas fa-trash text-danger"></i>
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
    <button type="button"
        class="btn btn-outline-danger rounded-circle shadow-sm"
        style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"
        onclick="hapusKompetensi('soft', ${index})">
        <i class="fas fa-trash text-danger"></i>
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
                                let jabatanDropdown = document.querySelector(
                                    '#modalJabatanDropdown');

                                // Reset TomSelect-nya jika sudah terinisialisasi
                                if (jabatanDropdown.tomselect) {
                                    jabatanDropdown.tomselect
                                        .clearOptions(); // hapus opsi sebelumnya
                                    jabatanDropdown.tomselect.addOption({
                                        value: "",
                                        text: "Pilih Jabatan"
                                    }); // tambahkan default option

                                    data.forEach(function(jabatan) {
                                        jabatanDropdown.tomselect.addOption({
                                            value: jabatan.id_jabatan,
                                            text: jabatan.nama_jabatan
                                        });
                                    });

                                    jabatanDropdown.tomselect.refreshOptions(false);
                                }


                                // 🔁 Re-inisialisasi TomSelect
                                if (TomSelect.instances['modalJabatanDropdown']) {
                                    TomSelect.instances['modalJabatanDropdown'].destroy();
                                }

                                new TomSelect('#modalJabatanDropdown', {
                                    plugins: ['dropdown_input'],
                                    allowEmptyOption: true
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
                                let kompetensiDropdown = document.querySelector(
                                    '#modalKompetensiDropdown');

                                // Tambahkan validasi jenis
                                let jenis = document.getElementById('modalJenisKompetensi').value;
                                if (jenis !== 'Hard Kompetensi')
                                    return; // ⛔ Hentikan kalau jenis bukan Hard

                                // Clear & reinitialize only if hard kompetensi
                                if (kompetensiDropdown.tomselect) {
                                    kompetensiDropdown.tomselect.clearOptions();
                                    kompetensiDropdown.tomselect.addOption({
                                        value: '',
                                        text: 'Pilih Kompetensi'
                                    });

                                    data.forEach(function(komp) {
                                        kompetensiDropdown.tomselect.addOption({
                                            value: komp.id_kompetensi,
                                            text: komp.nama_kompetensi
                                        });
                                    });

                                    kompetensiDropdown.tomselect.refreshOptions(false);
                                }
                            }
                        });
                    }
                });

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
                });
            });
            new TomSelect("#mentor-select", {
                plugins: ['dropdown_input'],
                allowEmptyOption: true
            });
            new TomSelect("#spv-select", {
                plugins: ['dropdown_input'],
                allowEmptyOption: true
            });
            // given
            new TomSelect('#filter-jenjang', {
                placeholder: 'Semua Jenjang',
                plugins: ['dropdown_input'],
                allowEmptyOption: true
            });

            new TomSelect('#filter-lg', {
                placeholder: 'Semua Learning Group',
                plugins: ['dropdown_input'],
                allowEmptyOption: true
            });

            new TomSelect('#filter-divisi', {
                placeholder: 'Semua Divisi',
                plugins: ['dropdown_input'],
                allowEmptyOption: true
            });

            new TomSelect('#filter-penempatan', {
                placeholder: 'Semua Penempatan',
                plugins: ['dropdown_input'],
                allowEmptyOption: true
            });
            //bank idp
            let metode2Initialized = false;

            function toggleMetode(metode) {
                const formMetode1 = document.getElementById('form-metode1');
                const formMetode2 = document.getElementById('form-metode2');

                const selectSupervisor1 = formMetode1.querySelector('select[name="id_supervisor"]');
                const selectSupervisor2 = formMetode2.querySelector('select[name="id_supervisor_jenjang"]');
                const selectJenjangBank = formMetode2.querySelector('select[name="id_jenjang_bank"]');
                const selectLgBank = formMetode2.querySelector('select[name="id_LG"]');

                if (metode == 1) {
                    formMetode1.style.display = 'block';
                    formMetode2.style.display = 'none';
                    selectSupervisor1.disabled = false;
                    selectSupervisor2.disabled = true;
                    selectJenjangBank.disabled = true;
                    selectLgBank.disabled = true;
                } else {
                    formMetode1.style.display = 'none';
                    formMetode2.style.display = 'block';
                    selectSupervisor1.disabled = true;
                    selectSupervisor2.disabled = false;
                    selectJenjangBank.disabled = false;
                    selectLgBank.disabled = false;

                    // 🔧 Inisialisasi TomSelect hanya satu kali saat pertama Bank IDP dibuka
                    if (!metode2Initialized) {
                        new TomSelect('select[name="id_jenjang_bank"]', {
                            placeholder: 'Pilih Jenjang',
                            plugins: ['dropdown_input'],
                            allowEmptyOption: true
                        });
                        new TomSelect('select[name="id_LG"]', {
                            plugins: ['dropdown_input'],
                            allowEmptyOption: true
                        });
                        new TomSelect('select[name="id_supervisor_jenjang"]', {
                            plugins: ['dropdown_input'],
                            allowEmptyOption: true
                        });
                        metode2Initialized = true;
                    }
                }
            }

            function resetFormModal() {
                // Reset jenis kompetensi ke default
                const jenisSelect = document.getElementById('modalJenisKompetensi');
                if (jenisSelect.tomselect) jenisSelect.tomselect.setValue('Hard Kompetensi');

                // Reset jenjang
                const jenjangSelect = document.getElementById('modalJenjangDropdown');
                if (jenjangSelect.tomselect) {
                    jenjangSelect.tomselect.clearOptions();
                    jenjangSelect.tomselect.addOption({
                        value: '',
                        text: 'Pilih Jenjang'
                    });

                    // Tambahkan kembali opsi jenjang dari PHP ke TomSelect
                    const listJenjang = @json($listJenjang);
                    listJenjang.forEach(function(item) {
                        jenjangSelect.tomselect.addOption({
                            value: item.id_jenjang,
                            text: item.nama_jenjang
                        });
                    });

                    jenjangSelect.tomselect.setValue('');
                    jenjangSelect.tomselect.refreshOptions(false);
                }

                // Reset jabatan
                const jabatanSelect = document.getElementById('modalJabatanDropdown');
                if (jabatanSelect.tomselect) {
                    jabatanSelect.tomselect.clearOptions();
                    jabatanSelect.tomselect.addOption({
                        value: '',
                        text: 'Pilih Jabatan'
                    });
                    jabatanSelect.tomselect.setValue('');
                }

                // Reset kompetensi
                const kompetensiSelect = document.getElementById('modalKompetensiDropdown');
                if (kompetensiSelect.tomselect) {
                    kompetensiSelect.tomselect.clearOptions();
                    kompetensiSelect.tomselect.addOption({
                        value: '',
                        text: 'Pilih Kompetensi'
                    });
                    kompetensiSelect.tomselect.setValue('');
                }

                // Reset metode belajar
                if (typeof tomSelectMetodeBelajar !== 'undefined') {
                    tomSelectMetodeBelajar.clear();
                }

                // Reset peran
                const peranSelect = document.getElementById('modalPeranDropdown');
                if (peranSelect.tomselect) peranSelect.tomselect.setValue('umum');

                // Kosongkan textarea
                $('#modalSasaran').val('');
                $('#modalAksi').val('');
            }
        </script>
    @endpush
