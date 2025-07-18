<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand text-center py-3">
            @php
                $dashboardRoute = match (session('active_role')) {
                    1 => route('adminsdm.dashboard'),
                    2 => route('supervisor.spv-dashboard'),
                    3 => route('mentor.dashboard-mentor'),
                    4 => route('karyawan.dashboard-karyawan'),
                    default => '#',
                };
            @endphp
            <a href="{{ $dashboardRoute }}">
                <img src="{{ asset('img/Logo-MenaraPEFI.png') }}" alt="MENARA PEFI" style="max-height: 40px; width: auto;">
            </a>
        </div>

        <div class="sidebar-brand sidebar-brand-sm">
            @php
                $dashboardRoute = match (session('active_role')) {
                    1 => route('adminsdm.dashboard'),
                    2 => route('supervisor.spv-dashboard'),
                    3 => route('mentor.dashboard-mentor'),
                    4 => route('karyawan.dashboard-karyawan'),
                    default => '#',
                };
            @endphp
            <a href="{{ $dashboardRoute }}">MP</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">MENU UTAMA</li>
            <li class="nav-item dropdown {{ $type_menu === 'dashboard' ? 'active' : '' }}">
                <a href="{{ session('active_role') == 1
                    ? route('adminsdm.dashboard')
                    : (session('active_role') == 2
                        ? route('supervisor.spv-dashboard')
                        : (session('active_role') == 3
                            ? route('mentor.dashboard-mentor')
                            : (session('active_role') == 4
                                ? route('karyawan.dashboard-karyawan')
                                : '#'))) }}"
                    class="nav-link">
                    <i
                        class="fas fa-fire {{ (session('active_role') == 1 && request()->routeIs('adminsdm.dashboard')) ||
                        (session('active_role') == 2 && request()->routeIs('supervisor.spv-dashboard')) ||
                        (session('active_role') == 3 && request()->routeIs('mentor.dashboard-mentor')) ||
                        (session('active_role') == 4 && request()->routeIs('karyawan.dashboard-karyawan'))
                            ? 'active'
                            : '' }}"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            {{-- AdminSDM --}}
            @if (session('active_role') == 1)
                <li class="nav-item dropdown {{ $type_menu === 'idps' ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-columns"></i> <span>Behavior IDP</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('admin/datamaster/behavior/idp/create') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/behavior/idp/create') }}">Tambah IDP</a>
                        </li>
                        <li
                            class="{{ Request::is('admin/datamaster/behavior/idp/bank/idp') ||
                            Request::is('admin/datamaster/behavior/idp/*/edit/bank/idp') ||
                            (Request::is('admin/datamaster/behavior/idp/*/detail') && !Request::is('admin/datamaster/behavior/idp/*/detail/given'))
                                ? 'active'
                                : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/behavior/idp/bank/idp') }}">Mapping
                                IDP</a>
                        </li>
                        <li
                            class="{{ Request::is('admin/datamaster/behavior/idp/given/idp') ||
                            Request::is('admin/datamaster/behavior/idp/*/detail/given') ||
                            Request::is('admin/datamaster/behavior/idp/*/edit')
                                ? 'active'
                                : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/behavior/idp/given/idp') }}">
                                List Perencanaan IDP</a>
                        </li>
                        <li
                            class="{{ Request::is('admin/datamaster/behavior/idp/riwayat/idp') ||
                            Request::is('admin/datamaster/behavior/idp/*/riwayat/idp')
                                ? 'active'
                                : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/behavior/idp/riwayat/idp') }}">Riwayat
                                Perencanaan IDP</a>
                        </li>
                        <li
                            class="{{ Request::is('admin/datamaster/panduan/idp') ||
                            Request::is('admin/datamaster/panduan/idp/*/edit') ||
                            Request::is('admin/datamaster/panduan/idp/*/detail')
                                ? 'active'
                                : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/panduan/idp') }}">Panduan</a>
                        </li>
                    </ul>
                </li>
                <li class="menu-header">DATA MASTER</li>
                <li class="nav-item dropdown {{ $type_menu === 'data-master' ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-users"></i>
                        <span>Data Karyawan</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('admin/datamaster/karyawan*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/karyawan') }}">Data Karyawan</a>
                        </li>
                        <li class="{{ Request::is('admin/datamaster/divisi*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('/admin/datamaster/divisi') }}">Data Divisi</a>
                        </li>
                        <li class="{{ Request::is('admin/datamaster/jabatan*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('/admin/datamaster/jabatan') }}">Data Jabatan</a>
                        </li>
                        <li class="{{ Request::is('admin/datamaster/angkatanpsp*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/angkatanpsp') }}">Angkatan PSP</a>
                        </li>
                        <li class="{{ Request::is('admin/datamaster/penempatan*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/penempatan') }}">Penempatan</a>
                        </li>
                        <li class="{{ Request::is('admin/datamaster/jenjang*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/jenjang') }}">Jenjang</a>
                        </li>
                        <li class="{{ Request::is('admin/datamaster/learning/group*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/learning/group') }}">Direktorat</a>
                        </li>
                        <li class="{{ Request::is('admin/datamaster/semester*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/semester') }}">Semester</a>
                        </li>
                        <li class="{{ Request::is('admin/datamaster/role*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/role') }}">Role User</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown {{ $type_menu === 'kompetensi' ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="far fa-file-alt"></i> <span>Data
                            Kompetensi</span></a>
                    <ul class="dropdown-menu">
                        <li
                            class="{{ Request::is('admin/datamaster/kompetensi/soft') ||
                            (Request::is('admin/datamaster/kompetensi/*/edit') && request()->query('from') == 'soft') ||
                            Request::is('admin/datamaster/kompetensi/*/detail/soft')
                                ? 'active'
                                : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/kompetensi/soft') }}">Soft
                                Kompetensi</a>
                        </li>
                    </ul>
                    <ul class="dropdown-menu">
                        <li
                            class="{{ Request::is('admin/datamaster/kompetensi/hard') ||
                            (Request::is('admin/datamaster/kompetensi/*/edit') && request()->query('from') == 'hard') ||
                            Request::is('admin/datamaster/kompetensi/*/detail/hard')
                                ? 'active'
                                : '' }}">
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
                        <li
                            class="{{ Request::is('admin/datamaster/metode/belajar*') || Request::is('admin/datamaster/metode/belajar/create*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/metode/belajar') }}">Metode Belajar</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown {{ $type_menu === 'supervisor' ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-user-tie"></i> <span>Data
                            Supervisor</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('admin/datamaster/supervisor*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/supervisor') }}">Supervisor</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown {{ $type_menu === 'mentor' ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-chalkboard-teacher"></i>
                        <span>Data
                            Mentor</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('admin/datamaster/mentor*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/mentor') }}">Mentor</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown {{ $type_menu === 'evaluasi' ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-clipboard-check"></i> <span>Data
                            Evaluasi</span></a>
                    <ul class="dropdown-menu">
                        <li
                            class="{{ Request::is('admin/datamaster/bank/evaluasi') || Request::is('admin/datamaster/bank/evaluasi/*/edit') || Request::is('admin/datamaster/bank/evaluasi/*/detail') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/bank/evaluasi') }}">Bank Evaluasi</a>
                        </li>
                    </ul>
                    <ul class="dropdown-menu">
                        <li
                            class="{{ Request::is('admin/datamaster/bank/evaluasi/pasca/idp*') || Request::is('admin/datamaster/bank/evaluasi/*/detail/jawaban') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('admin/datamaster/bank/evaluasi/pasca/idp') }}">Evaluasi
                                Pasca IDP</a>
                        </li>
                    </ul>
                </li>
        </ul>
    @elseif (session('active_role') == 2)
        <!-- Supervisor -->
        <ul class="sidebar-menu">
            <li class="nav-item dropdown {{ $type_menu === 'supervisor' ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                        class="fas fa-columns"></i> <span>Behavior IDP</span></a>
                <ul class="dropdown-menu">
                    <li
                        class="{{ Request::is('supervisor/behavior/idp') || Request::is('supervisor/behavior/idp/detail/*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('supervisor/behavior/idp') }}">List Perencanaan IDP</a>
                    </li>
                    <li
                        class="{{ Request::is('supervisor/behavior/idp/riwayat') || Request::is('supervisor/behavior/idp/*/riwayat') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('supervisor/behavior/idp/riwayat') }}">Riwayat Perencanaan
                            IDP</a>
                    </li>
                    <li class="{{ Request::is('supervisor/panduan/idp') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('supervisor/panduan/idp') }}">Panduan</a>
                    </li>
                </ul>
            </li>
            <!-- Menu Evaluasi (Dipisah) -->
            <li class="nav-item dropdown {{ $type_menu === 'evaluasi' ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-poll"></i>
                    <span>Evaluasi</span></a>
                <ul class="dropdown-menu">
                    <li
                        class="{{ Request::is('supervisor/evaluasi/idp') || Request::is('supervisor/evaluasi/idp/create') ? 'active' : '' }}">
                        <a href="{{ url('supervisor/evaluasi/idp') }}">Evaluasi Pasca IDP</a>
                    </li>
                </ul>
            </li>
        </ul>
    @elseif (session('active_role') == 3)
        <!-- Mentor -->
        <ul class="sidebar-menu">
            <li class="nav-item dropdown {{ $type_menu === 'mentor' ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                        class="fas fa-columns"></i> <span>Behavior IDP</span></a>
                <ul class="dropdown-menu">
                    <li
                        class="{{ Request::is('mentor/behavior/idp') ||
                        Request::is('mentor/behavior/idp/detail/*') ||
                        Request::is('mentor/behavior/idp/verifikasi/*') ||
                        Request::is('mentor/behavior/idp/penilaian/idp/*')
                            ? 'active'
                            : '' }}">
                        <a class="nav-link" href="{{ url('mentor/behavior/idp') }}">List Perencanaan IDP</a>
                    </li>
                    <li
                        class="{{ Request::is('mentor/behavior/idp/riwayat') || Request::is('mentor/behavior/idp/*/riwayat') ? 'active' : '' }}">
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
                    <li
                        class="{{ Request::is('mentor/evaluasi/onboarding/idp') || Request::is('mentor/evaluasi/onboarding/idp/create') ? 'active' : '' }}">
                        <a href="{{ url('mentor/evaluasi/onboarding/idp') }}">Evaluasi Onboarding</a>
                    </li>
                    <li
                        class="{{ Request::is('mentor/bank/evaluasi/idp') || Request::is('mentor/bank/evaluasi/idp/create') ? 'active' : '' }}">
                        <a href="{{ url('mentor/bank/evaluasi/idp') }}">Evaluasi Pasca IDP</a>
                    </li>
                </ul>
            </li>
        </ul>
    @elseif (session('active_role') == 4)
        <ul class="sidebar-menu">
            <li class="nav-item dropdown {{ $type_menu === 'karyawan' ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                        class="fas fa-columns"></i> <span>Behavior IDP</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('karyawan/behavior/idp/create') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('karyawan/behavior/idp/create') }}">Tambah IDP</a>
                    </li>
                    <li class="{{ Request::is('karyawan/behavior/idp/bank-id*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('karyawan/behavior/idp/bank-idp') }}">Mapping IDP</a>
                    </li>
                    <li
                        class="{{ Request::is('karyawan/behavior/idp') ||
                        Request::is('karyawan/behavior/idp/*/edit') ||
                        Request::is('karyawan/behavior/idp/*/detail/idp')
                            ? 'active'
                            : '' }}">
                        <a class="nav-link" href="{{ url('karyawan/behavior/idp') }}">List Perencanaan IDP</a>
                    </li>
                    <li
                        class="{{ Request::is('karyawan/behavior/idp/progres') ||
                        (Request::is('karyawan/behavior/idp/*/detail') && request()->has('pengerjaan'))
                            ? 'active'
                            : '' }}">
                        <a class="nav-link" href="{{ url('karyawan/behavior/idp/progres') }}">Progres Perencanaan
                            IDP</a>
                    </li>
                    <li
                        class="{{ Request::is('karyawan/behavior/idp/riwayat') || Request::is('karyawan/behavior/idp/*/riwayat') ? 'active' : '' }}">
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
                        class="{{ Request::is('karyawan/evaluasi/onboarding/idp') || Request::is('karyawan/evaluasi/onboarding/detail/*')
                            ? 'active'
                            : '' }}">
                        <a href="{{ url('karyawan/evaluasi/onboarding/idp') }}">Evaluasi On Boarding</a>
                    </li>
                    <li
                        class="{{ Request::is('karyawan/bank/evaluasi/idp') || Request::is('karyawan/bank/evaluasi/idp/create')
                            ? 'active'
                            : '' }}">
                        <a href="{{ url('karyawan/bank/evaluasi/idp') }}">Evaluasi Pasca IDP</a>
                    </li>
                </ul>
            </li>
        </ul>
        @endif
    </aside>
</div>
