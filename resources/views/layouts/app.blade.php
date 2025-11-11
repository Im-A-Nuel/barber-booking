<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Barber Booking')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 260px;
            background: #2c3e50;
            padding-top: 20px;
            z-index: 1000;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }

        .sidebar-header h3 {
            font-size: 20px;
            margin: 0;
            font-weight: 600;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
        }

        .sidebar-menu li {
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .sidebar-menu li a {
            display: block;
            padding: 15px 25px;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s;
        }

        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background: #34495e;
            color: white;
            border-left: 4px solid #3498db;
            padding-left: 21px;
        }

        .sidebar-menu li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Top Header Styles */
        .top-header {
            position: fixed;
            left: 260px;
            right: 0;
            top: 0;
            height: 60px;
            background: #34495e;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 0 30px;
            z-index: 999;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .top-header-right {
            display: flex;
            align-items: center;
            gap: 25px;
            color: white;
        }

        .clock {
            font-size: 14px;
            color: #ecf0f1;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-info .user-icon {
            width: 35px;
            height: 35px;
            background: #3498db;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .user-name {
            font-size: 14px;
            font-weight: 500;
        }

        .logout-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        .logout-btn i {
            margin-right: 5px;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 260px;
            margin-top: 60px;
            padding: 30px;
            min-height: calc(100vh - 60px);
            background: #ecf0f1;
        }

        .content-wrapper {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Guest Layout */
        .guest-layout {
            margin-left: 0;
            margin-top: 0;
        }

        .guest-nav {
            background: #2c3e50;
            padding: 15px 30px;
            color: white;
        }

        .guest-nav .navbar-brand {
            color: white;
            font-size: 20px;
            font-weight: 600;
        }

        .guest-nav .nav-link {
            color: #ecf0f1;
            margin-left: 15px;
        }

        .guest-nav .nav-link:hover {
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                left: -260px;
            }

            .sidebar.active {
                left: 0;
            }

            .top-header {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    @guest
        <!-- Guest Navigation -->
        <nav class="navbar navbar-expand-lg guest-nav">
            <a class="navbar-brand" href="/">
                <i class="fas fa-cut"></i> Barber Booking
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="py-4 guest-layout">
            <div class="container">
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i> {{ session('status') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    @else
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-cut fa-2x mb-2"></i>
                <h3>Barber Booking</h3>
            </div>
            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                @if(Auth::user()->isAdmin())
                    <li>
                        <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i> Kelola User
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('services.index') }}" class="{{ request()->routeIs('services.*') ? 'active' : '' }}">
                            <i class="fas fa-list"></i> Kelola Service
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('stylists.index') }}" class="{{ request()->routeIs('stylists.*') ? 'active' : '' }}">
                            <i class="fas fa-user-tie"></i> Kelola Stylist
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('schedules.index') }}" class="{{ request()->routeIs('schedules.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt"></i> Kelola Jadwal
                        </a>
                    </li>
                @endif
                @if(Auth::user()->isAdmin() || Auth::user()->isStylist())
                    <li>
                        <a href="{{ route('admin.bookings.index') }}" class="{{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-check"></i> Kelola Booking
                        </a>
                    </li>
                @endif
                @if(Auth::user()->isCustomer())
                    <li>
                        <a href="{{ route('bookings.index') }}" class="{{ request()->routeIs('bookings.index') ? 'active' : '' }}">
                            <i class="fas fa-calendar-check"></i> Booking Saya
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('bookings.create') }}" class="{{ request()->routeIs('bookings.create') ? 'active' : '' }}">
                            <i class="fas fa-calendar-plus"></i> Buat Booking
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('payments.index') }}" class="{{ request()->routeIs('payments.*') ? 'active' : '' }}">
                            <i class="fas fa-money-bill-wave"></i> Riwayat Pembayaran
                        </a>
                    </li>
                @endif
                @if(Auth::user()->isAdmin())
                    <li>
                        <a href="{{ route('payments.index') }}" class="{{ request()->routeIs('payments.*') ? 'active' : '' }}">
                            <i class="fas fa-money-bill-wave"></i> Semua Pembayaran
                        </a>
                    </li>
                @endif
            </ul>
        </aside>

        <!-- Top Header -->
        <header class="top-header">
            <div class="top-header-right">
                <div class="clock">
                    <i class="fas fa-clock"></i>
                    <span id="current-time"></span>
                </div>
                <div class="user-info">
                    @if(Auth::user()->image)
                        <img src="{{ asset('storage/' . Auth::user()->image) }}" alt="{{ Auth::user()->name }}" class="user-icon" style="object-fit: cover;">
                    @else
                        <div class="user-icon">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                    <span class="user-name">{{ Auth::user()->name }}</span>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="button" class="logout-btn" onclick="confirmLogout()">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Konten -->
        <main class="main-content">
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> {{ session('status') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <div class="content-wrapper">
                @yield('content')
            </div>
        </main>
    @endguest

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // jam real time
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const timeString = `${hours}:${minutes}:${seconds}`;

            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                timeElement.textContent = timeString;
            }
        }

        // Update jam 
        if (document.getElementById('current-time')) {
            updateClock();
            setInterval(updateClock, 1000);
        }

        // Logout konfirmasi
        function confirmLogout() {
            Swal.fire({
                title: 'Konfirmasi Logout',
                text: "Apakah Anda yakin ingin keluar dari sistem?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
