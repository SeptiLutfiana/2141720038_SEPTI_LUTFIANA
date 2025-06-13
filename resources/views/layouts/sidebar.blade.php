<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="index.html">
                <img src="{{ asset('img/Logo-MenaraPEFI.png') }}" alt="MENARA PEFI" style="height:40px;">
            </a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.html">MP</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">MENU UTAMA</li>
            <li class="nav-item dropdown {{ $type_menu === 'dashboard' ? 'active' : '' }}">
                {{-- <a href="{{ url('adminsdm-dashboard') }}" --}}
                <a href="{{ Auth::user()->id_role == 1
                    ? route('adminsdm.dashboard')
                    : (Auth::user()->id_role == 2
                        ? route('supervisor.spv-dashboard')
                        : (Auth::user()->id_role == 3
                            ? route('mentor.dashboard-mentor')
                            : (Auth::user()->id_role == 4
                                ? route('karyawan.dashboard-karyawan')
                                : '#'))) }}"
                    class="nav-link"><i
                        class="fas fa-fire {{ (Auth::user()->id_role == 1 && request()->routeIs('adminsdm.dashboard')) ||
                        (Auth::user()->id_role == 2 && request()->routeIs('supervisor.spv-dashboard')) ||
                        (Auth::user()->id_role == 3 && request()->routeIs('mentor.dashboard-mentor')) ||
                        (Auth::user()->id_role == 4 && request()->routeIs('karyawan.dashboard-karyawan'))
                            ? 'active'
                            : '' }}"></i><span>Dashboard</span></a>
            </li>

            {{-- AdminSDM --}}
            @if (Auth::user()->id_role == 1)
                <li class="nav-item dropdown {{ $type_menu === 'idps' ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-columns"></i> <span>Behavior IDP</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('admin/datamaster/behavior/idp/create') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/behavior/idp/create') }}">Tambah IDP</a>
                        </li>
                        <li class="{{ Request::is('admin/datamaster/behavior/idp/bank/idp') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/behavior/idp/bank/idp') }}">Bank IDP</a>
                        </li>
                        <li class="{{ Request::is('admin/datamaster/behavior/idp/given/idp') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/behavior/idp/given/idp') }}">
                                List Perencanaan IDP</a>
                        </li>
                        <li class="{{ Request::is('admin/datamaster/behavior/idp/riwayat/idp') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/behavior/idp/riwayat/idp') }}">Riwayat
                                Perencanaan IDP</a>
                        </li>
                        <li class="{{ Request::is('admin/datamaster/panduan/idp') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/panduan/idp') }}">Panduan</a>
                        </li>
                    </ul>
                </li>
                <li class="menu-header">DATA MASTER</li>
                <li class="nav-item dropdown {{ $type_menu === 'data-master' ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-users"></i>
                        <span>Data Karyawan</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('admin/datamaster/karyawan') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/karyawan') }}">Data Karyawan</a>
                        </li>
                        <li class="{{ Request::is('/admin/datamaster/divisi') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('/admin/datamaster/divisi') }}">Data Divisi</a>
                        </li>
                        <li class="{{ Request::is('/admin/datamaster/jabatan') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('/admin/datamaster/jabatan') }}">Data Jabatan</a>
                        </li>
                        <li class="{{ Request::is('admin/datamaster/angkatanpsp') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/angkatanpsp') }}">Angkatan PSP</a>
                        </li>
                        <li class="{{ Request::is('admin/datamaster/penempatan') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/penempatan') }}">Penempatan</a>
                        </li>
                        <li class="{{ Request::is('admin/datamaster/jenjang') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/jenjang') }}">Jenjang</a>
                        </li>
                        <li class="{{ Request::is('admin/datamaster/learning/group') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/learning/group') }}">Learning Group</a>
                        </li>
                        <li class="{{ Request::is('admin/datamaster/semester') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/semester') }}">Semester</a>
                        </li>
                        <li class="{{ Request::is('admin/datamaster/role') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/role') }}">Role User</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown {{ $type_menu === 'kompetensi' ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="far fa-file-alt"></i> <span>Data
                            Kompetensi</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('admin/datamaster/kompetensi/soft') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/kompetensi/soft') }}">Soft
                                Kompetensi</a>
                        </li>
                    </ul>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('admin/datamaster/kompetensi/hard') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/kompetensi/hard') }}">Hard
                                Kompetensi</a>
                        </li>
                    </ul>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('admin/datamaster/kompetensi/create') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/kompetensi/create') }}">Tambah
                                Kompetensi</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown {{ $type_menu === 'metodebelajar' ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-tasks"></i> <span>Data
                            IDP</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('admin/datamaster/metode/belajar') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/metode/belajar') }}">Metode Belajar</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown {{ $type_menu === 'supervisor' ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-user-tie"></i> <span>Data
                            Supervisor</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('admin/datamaster/supervisor') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/supervisor') }}">Supervisor</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown {{ $type_menu === 'mentor' ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-chalkboard-teacher"></i>
                        <span>Data
                            Mentor</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('admin/datamaster/mentor') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/mentor') }}">Mentor</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown {{ $type_menu === 'evaluasi' ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-clipboard-check"></i> <span>Data
                            Evaluasi</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('admin/datamaster/bank/evaluasi') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/bank/evaluasi') }}">Bank Evaluasi</a>
                        </li>
                    </ul>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('admin/datamaster/bank/evaluasi/pasca/idp') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/bank/evaluasi/pasca/idp') }}">Evaluasi
                                Pasca IDP</a>
                        </li>
                    </ul>
                </li>
        </ul>
        @endif
        @if (Auth::user()->id_role == 2)
            <!-- Supervisor -->
            <ul class="sidebar-menu">
                <li class="nav-item dropdown {{ $type_menu === 'supervisor' ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-columns"></i> <span>Behavior IDP</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('supervisor/behavior/idp') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('supervisor/behavior/idp') }}">List Perencanaan IDP</a>
                        </li>
                        <li class="{{ Request::is('supervisor/behavior/idp/riwayat') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('supervisor/behavior/idp/riwayat') }}">Riwayat
                                Perencanaan IDP</a>
                        </li>
                        <li class="{{ Request::is('supervisor/panduan/idp') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('supervisor/panduan/idp') }}">Panduan</a>
                        </li>
                    </ul>
                </li>
            </ul>
        @endif
        @if (Auth::user()->id_role == 3)
            <!-- Mentor -->
            <ul class="sidebar-menu">
                <li class="nav-item dropdown {{ $type_menu === 'mentor' ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-columns"></i> <span>Behavior IDP</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('mentor/behavior/idp') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('mentor/behavior/idp') }}">List Perencanaan IDP</a>
                        </li>
                        <li class="{{ Request::is('mentor/behavior/idp/riwayat') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('mentor/behavior/idp/riwayat') }}">Riwayat Perencanaan
                                IDP</a>
                        </li>
                        <li class="{{ Request::is('mentor/panduan/idp') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('mentor/panduan/idp') }}">Panduan</a>
                        </li>
                    </ul>
                </li>
                <!-- Menu Evaluasi (Dipisah) -->
                <li class="nav-item dropdown {{ $type_menu === 'evaluasi' ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-poll"></i>
                        <span>Evaluasi</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('evaluasi-onboarding') ? 'active' : '' }}">
                            <a href="{{ url('evaluasi-onboarding') }}">Evaluasi On Boarding</a>
                        </li>
                        <li class="{{ Request::is('mentor/bank/evaluasi/idp') ? 'active' : '' }}">
                            <a href="{{ url('mentor/bank/evaluasi/idp') }}">Evaluasi Pasca IDP</a>
                        </li>
                    </ul>
                </li>
            </ul>
        @endif
        @if (Auth::user()->id_role == 4)
            <ul class="sidebar-menu">
                <li class="nav-item dropdown {{ $type_menu === 'karyawan' ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-columns"></i> <span>Behavior IDP</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('karyawan/behavior/idp/create') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('karyawan/behavior/idp/create') }}">Tambah IDP</a>
                        </li>
                        <li class="{{ Request::is('karyawan/behavior/idp/bank-id') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('karyawan/behavior/idp/bank-idp') }}">Bank IDP</a>
                        </li>
                        <li class="{{ Request::is('karyawan/behavior/idp') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('karyawan/behavior/idp') }}">List Perencanaan IDP</a>
                        </li>
                        <li class="{{ Request::is('karyawan/behavior/idp/riwayat') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('karyawan/behavior/idp/riwayat') }}">Riwayat Perencanaan
                                IDP</a>
                        </li>
                        <li class="{{ Request::is('karyawan/panduan/idp/panduan/karyawan') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('karyawan/panduan/idp/panduan/karyawan') }}">Panduan</a>
                        </li>
                    </ul>
                </li>
                <!-- Menu Evaluasi (Dipisah) -->
                <li class="nav-item dropdown {{ $type_menu === 'evaluasi' ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-poll"></i>
                        <span>Evaluasi</span></a>
                    <ul class="dropdown-menu">
                        <li
                            class="{{ Request::is('admin/datamaster/bank/evaluasi/pasca/idp/create') ? 'active' : '' }}">
                            <a href="{{ url('karyawan/bank/evaluasi/idp/create') }}">Evaluasi On Boarding</a>
                        </li>
                        <li class="{{ Request::is('karyawan/bank/evaluasi/idp') ? 'active' : '' }}">
                            <a href="{{ url('karyawan/bank/evaluasi/idp') }}">Evaluasi Pasca IDP</a>
                        </li>
                    </ul>
                </li>
            </ul>
        @endif
    </aside>
</div>
