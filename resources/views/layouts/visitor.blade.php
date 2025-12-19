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
            background: #ecf0f1;
            min-height: 100vh;
        }

        /* Visitor Navbar */
        .visitor-nav {
            background: #2c3e50;
            padding: 15px 30px;
            color: white;
        }

        .visitor-nav .navbar-brand {
            color: white;
            font-size: 20px;
            font-weight: 600;
        }

        .visitor-nav .navbar-brand:hover {
            color: #ecf0f1;
        }

        .visitor-nav .nav-link {
            color: #ecf0f1;
            margin-left: 15px;
        }

        .visitor-nav .nav-link:hover {
            color: white;
        }

        .visitor-nav .navbar-toggler {
            border-color: rgba(255,255,255,0.5);
        }

        .visitor-nav .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .visitor-content {
            padding: 30px 0;
        }

        .content-wrapper {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Footer untuk visitor */
        .visitor-footer {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 20px 0;
            text-align: center;
            margin-top: 30px;
        }

        .visitor-footer a {
            color: #3498db;
            text-decoration: none;
        }

        .visitor-footer a:hover {
            color: #5dade2;
            text-decoration: underline;
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Visitor Navigation -->
    <nav class="navbar navbar-expand-lg visitor-nav">
        <a class="navbar-brand" href="{{ route('visitor.search') }}">
            <i class="fas fa-cut"></i> Barber Booking
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#visitorNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="visitorNavbar">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('visitor.search') }}">
                        <i class="fas fa-search"></i> Cari Layanan
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <main class="visitor-content">
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

    <footer class="visitor-footer">
        <div class="container">
            <p class="mb-2">&copy; {{ date('Y') }} Barber Booking. All rights reserved.</p>
            <p class="mb-0">
                <a href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> Login</a>
                <span class="mx-2">|</span>
                <a href="{{ route('register') }}"><i class="fas fa-user-plus"></i> Register</a>
            </p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('scripts')
</body>
</html>
