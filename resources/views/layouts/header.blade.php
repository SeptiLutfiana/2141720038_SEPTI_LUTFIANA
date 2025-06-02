<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i
                        class="fas fa-search"></i></a></li>
        </ul>
    </form>
    <ul class="navbar-nav navbar-right">
        <li class="dropdown dropdown-list-toggle">
            <a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg">
                <i class="far fa-bell"></i>
                @if (auth()->user()->unreadNotifications->count() > 0)
                    <span class="badge badge-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
                @endif
            </a>

            <div class="dropdown-menu dropdown-list dropdown-menu-right">
                <div class="dropdown-header">
                    Notifications
                    <div class="float-right">
                        <a href="#">Mark All As Read</a>
                    </div>
                </div>

                <div class="dropdown-list-content dropdown-list-icons">
                    @php
                        // Ambil ID role aktif dari session
                        $activeRole = session('active_role');

                        // Tentukan nama peran
                        $userRole = match ($activeRole) {
                            1 => 'adminsdm',
                            2 => 'supervisor',
                            3 => 'mentor',
                            4 => 'karyawan',
                            default => 'karyawan',
                        };
                    @endphp

                    <div class="dropdown-list-content dropdown-list-icons">
                        @if (auth()->user()->unreadNotifications->count() > 0)
                            @foreach (auth()->user()->unreadNotifications as $notification)
                                @php
                                    $idpId = $notification->data['id_idp'] ?? 0;
                                    $idpKomPengId = $notification->data['id_idpKomPeng'] ?? 0;
                                    $notifId = $notification->id;

                                    // Tentukan nama dan rute berdasarkan peran aktif
                                    switch ($userRole) {
                                        case 'mentor':
                                            $nama = $notification->data['nama_karyawan'] ?? 'Karyawan';
                                            $routeName = 'mentor.IDP.mentor.idp.show';
                                            break;
                                        case 'supervisor':
                                            $nama = $notification->data['nama_karyawan'] ?? 'Karyawan';
                                            $routeName = 'karyawan.IDP.showKaryawan';
                                            break;
                                        case 'karyawan':
                                            $nama = $notification->data['nama_mentor'] ?? 'Mentor';
                                            $routeName = 'karyawan.IDP.showKaryawan';
                                            break;
                                        case 'adminsdm':
                                            $nama = $notification->data['nama_user'] ?? 'Mentor';
                                            $routeName = 'adminsdm.dashboard';
                                            break;
                                    }
                                @endphp

                                <a href="{{ $routeName !== '#' ? route($routeName, ['id' => $idpId]) . '?pengerjaan=' . $idpKomPengId . '&notification_id=' . $notifId : '#' }}"
                                    class="dropdown-item dropdown-item-unread">
                                    <div class="dropdown-item-icon bg-info text-white">
                                        <i class="fas fa-file-upload"></i>
                                    </div>
                                    <div class="dropdown-item-desc">
                                        <b>{{ $nama }}</b>
                                        {{ $notification->data['message'] ?? 'mengunggah hasil IDP' }}
                                        <div class="time text-primary">{{ $notification->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <div class="text-center p-3 text-muted">Tidak ada notifikasi baru.</div>
                        @endif
                    </div>
                </div>
            </div>
        </li>

        <li class="dropdown"><a href="#" data-toggle="dropdown"
                class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <img alt="image" src="{{ asset('img/avatar/avatar-1.png') }}" class="rounded-circle mr-1">
                <div class="d-sm-none d-lg-inline-block">
                    Hi, {{ Auth::user()->name }} (
                    @php
                        $role = session('active_role');
                    @endphp
                    @if ($role == 1)
                        Admin SDM
                    @elseif($role == 2)
                        Supervisor
                    @elseif($role == 3)
                        Mentor
                    @elseif($role == 4)
                        Karyawan
                    @else
                        Unknown
                    @endif
                    )
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-title">
                    Hi, {{ Auth::user()->name }} (
                    @php
                        $role = session('active_role');
                    @endphp
                    @if ($role == 1)
                        Admin SDM
                    @elseif($role == 2)
                        Supervisor
                    @elseif($role == 3)
                        Mentor
                    @elseif($role == 4)
                        Karyawan
                    @else
                        Unknown
                    @endif
                    )
                </div>
                <a href="features-profile.html" class="dropdown-item has-icon">
                    <i class="far fa-user"></i> Profile
                </a>
                <a href="features-settings.html" class="dropdown-item has-icon">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a href="#" class="dropdown-item has-icon d-flex justify-content-between align-items-center"
                    id="switchRoleToggle">
                    <span><i class="fas fa-exchange-alt"></i> Switch Role</span>
                    <i class="fas fa-chevron-down" id="dropdownIcon"></i>
                </a>
                <div id="switchRoleMenu" style="display: none; transition: all 0.1s ease; padding-left: 15px;">
                    <form action="{{ route('switchRole') }}" method="POST">
                        @csrf
                        <button class="dropdown-item has-icon" type="submit" name="role" value="1">
                            <i class="fas fa-user-shield"></i> Admin
                        </button>
                        <button class="dropdown-item has-icon" type="submit" name="role" value="2">
                            <i class="fas fa-user-tie"></i> Supervisor
                        </button>
                        <button class="dropdown-item has-icon" type="submit" name="role" value="3">
                            <i class="fas fa-chalkboard-teacher"></i> Mentor
                        </button>
                        <button class="dropdown-item has-icon" type="submit" name="role" value="4">
                            <i class="fas fa-users"></i> Karyawan
                        </button>
                    </form>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="dropdown-item has-icon text-danger">
                        <i class="fas fa-sign-out-alt"></i>Logout
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>
<script>
    document.getElementById('switchRoleToggle').addEventListener('click', function(e) {
        e.preventDefault(); // Mencegah link reload halaman
        e.stopPropagation(); // Mencegah event bubble (tutup dropdown)

        const menu = document.getElementById('switchRoleMenu');
        const icon = document.getElementById('dropdownIcon');

        const isVisible = menu.style.display === 'block';
        menu.style.display = isVisible ? 'none' : 'block';
        icon.classList.toggle('rotate-180', !isVisible);
    });

    // Optional: Tambah rotasi untuk panah
    const style = document.createElement('style');
    style.textContent = `
        .rotate-180 {
            transform: rotate(180deg);
            transition: transform 0.3s ease;
        }
    `;
    document.head.appendChild(style);
</script>
