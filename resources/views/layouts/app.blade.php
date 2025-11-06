<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Barber Booking')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="/">
            <i class="fas fa-cut"></i> Barber Booking
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                @guest
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
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> {{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('home') }}">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                            @if(Auth::user()->isAdmin())
                                <a class="dropdown-item" href="{{ route('users.index') }}">
                                    <i class="fas fa-users"></i> Kelola User
                                </a>
                                <a class="dropdown-item" href="{{ route('services.index') }}">
                                    <i class="fas fa-list"></i> Kelola Service
                                </a>
                                <a class="dropdown-item" href="{{ route('stylists.index') }}">
                                    <i class="fas fa-user-tie"></i> Kelola Stylist
                                </a>
                                <a class="dropdown-item" href="{{ route('schedules.index') }}">
                                    <i class="fas fa-calendar-alt"></i> Kelola Jadwal
                                </a>
                            @endif
                            @if(Auth::user()->isAdmin() || Auth::user()->isStylist())
                                <a class="dropdown-item" href="{{ route('admin.bookings.index') }}">
                                    <i class="fas fa-calendar-check"></i> Kelola Booking
                                </a>
                            @endif
                            @if(Auth::user()->isCustomer())
                                <a class="dropdown-item" href="{{ route('bookings.index') }}">
                                    <i class="fas fa-calendar-check"></i> Booking Saya
                                </a>
                                <a class="dropdown-item" href="{{ route('bookings.create') }}">
                                    <i class="fas fa-calendar-plus"></i> Buat Booking
                                </a>
                            @endif
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </nav>

    <main class="py-4">
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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
