<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li><a href="#"
                    data-toggle="sidebar"
                    class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li><a href="#"
                    data-toggle="search"
                    class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
        </ul>
    </form>
    <ul class="navbar-nav navbar-right">
        <li class="dropdown dropdown-list-toggle"><a href="#"
                data-toggle="dropdown"
                class="nav-link nav-link-lg message-toggle beep"><i class="far fa-envelope"></i></a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right">
                <div class="dropdown-header">Messages
                    <div class="float-right">
                        <a href="#">Mark All As Read</a>
                    </div>
                </div>
                <div class="dropdown-list-content dropdown-list-message">
                    <a href="#"
                        class="dropdown-item dropdown-item-unread">
                        <div class="dropdown-item-avatar">
                            <img alt="image"
                                src="{{ asset('img/avatar/avatar-1.png') }}"
                                class="rounded-circle">
                            <div class="is-online"></div>
                        </div>
                        <div class="dropdown-item-desc">
                            <b>Kusnaedi</b>
                            <p>Hello, Bro!</p>
                            <div class="time">10 Hours Ago</div>
                        </div>
                    </a>
                    <a href="#"
                        class="dropdown-item dropdown-item-unread">
                        <div class="dropdown-item-avatar">
                            <img alt="image"
                                src="{{ asset('img/avatar/avatar-2.png') }}"
                                class="rounded-circle">
                        </div>
                        <div class="dropdown-item-desc">
                            <b>Dedik Sugiharto</b>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit</p>
                            <div class="time">12 Hours Ago</div>
                        </div>
                    </a>
                    <a href="#"
                        class="dropdown-item dropdown-item-unread">
                        <div class="dropdown-item-avatar">
                            <img alt="image"
                                src="{{ asset('img/avatar/avatar-3.png') }}"
                                class="rounded-circle">
                            <div class="is-online"></div>
                        </div>
                        <div class="dropdown-item-desc">
                            <b>Agung Ardiansyah</b>
                            <p>Sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                            <div class="time">12 Hours Ago</div>
                        </div>
                    </a>
                    <a href="#"
                        class="dropdown-item">
                        <div class="dropdown-item-avatar">
                            <img alt="image"
                                src="{{ asset('img/avatar/avatar-4.png') }}"
                                class="rounded-circle">
                        </div>
                        <div class="dropdown-item-desc">
                            <b>Ardian Rahardiansyah</b>
                            <p>Duis aute irure dolor in reprehenderit in voluptate velit ess</p>
                            <div class="time">16 Hours Ago</div>
                        </div>
                    </a>
                    <a href="#"
                        class="dropdown-item">
                        <div class="dropdown-item-avatar">
                            <img alt="image"
                                src="{{ asset('img/avatar/avatar-5.png') }}"
                                class="rounded-circle">
                        </div>
                        <div class="dropdown-item-desc">
                            <b>Alfa Zulkarnain</b>
                            <p>Exercitation ullamco laboris nisi ut aliquip ex ea commodo</p>
                            <div class="time">Yesterday</div>
                        </div>
                    </a>
                </div>
                <div class="dropdown-footer text-center">
                    <a href="#">View All <i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
        </li>
        <li class="dropdown dropdown-list-toggle"><a href="#"
                data-toggle="dropdown"
                class="nav-link notification-toggle nav-link-lg beep"><i class="far fa-bell"></i></a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right">
                <div class="dropdown-header">Notifications
                    <div class="float-right">
                        <a href="#">Mark All As Read</a>
                    </div>
                </div>
                <div class="dropdown-list-content dropdown-list-icons">
                    <a href="#"
                        class="dropdown-item dropdown-item-unread">
                        <div class="dropdown-item-icon bg-primary text-white">
                            <i class="fas fa-code"></i>
                        </div>
                        <div class="dropdown-item-desc">
                            Template update is available now!
                            <div class="time text-primary">2 Min Ago</div>
                        </div>
                    </a>
                    <a href="#"
                        class="dropdown-item">
                        <div class="dropdown-item-icon bg-info text-white">
                            <i class="far fa-user"></i>
                        </div>
                        <div class="dropdown-item-desc">
                            <b>You</b> and <b>Dedik Sugiharto</b> are now friends
                            <div class="time">10 Hours Ago</div>
                        </div>
                    </a>
                    <a href="#"
                        class="dropdown-item">
                        <div class="dropdown-item-icon bg-success text-white">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="dropdown-item-desc">
                            <b>Kusnaedi</b> has moved task <b>Fix bug header</b> to <b>Done</b>
                            <div class="time">12 Hours Ago</div>
                        </div>
                    </a>
                    <a href="#"
                        class="dropdown-item">
                        <div class="dropdown-item-icon bg-danger text-white">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="dropdown-item-desc">
                            Low disk space. Let's clean it!
                            <div class="time">17 Hours Ago</div>
                        </div>
                    </a>
                    <a href="#"
                        class="dropdown-item">
                        <div class="dropdown-item-icon bg-info text-white">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="dropdown-item-desc">
                            Welcome to Stisla template!
                            <div class="time">Yesterday</div>
                        </div>
                    </a>
                </div>
                <div class="dropdown-footer text-center">
                    <a href="#">View All <i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
        </li>
        <li class="dropdown"><a href="#"
                data-toggle="dropdown"
                class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <img alt="image"
                    src="{{ asset('img/avatar/avatar-1.png') }}"
                    class="rounded-circle mr-1">
                    <div class="d-sm-none d-lg-inline-block">
                        Hi, {{ Auth::user()->name }} ( 
                        @php
                        $role = session('active_role');
                        @endphp
                        @if($role == 1)
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
                    @if($role == 1)
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
                <a href="features-profile.html"
                    class="dropdown-item has-icon">
                    <i class="far fa-user"></i> Profile
                </a>
                <a href="features-settings.html"
                    class="dropdown-item has-icon">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a href="#" class="dropdown-item has-icon d-flex justify-content-between align-items-center" id="switchRoleToggle">
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
    document.getElementById('switchRoleToggle').addEventListener('click', function (e) {
        e.preventDefault();          // Mencegah link reload halaman
        e.stopPropagation();         // Mencegah event bubble (tutup dropdown)
        
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



