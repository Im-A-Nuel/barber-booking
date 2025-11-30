<!doctype html>
<html lang="en">
<head>
    <title>Barber Booking - Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            margin-top: 10%;
            margin-bottom: 10%;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .card-header {
            background: #3498db;
            color: white;
            border-radius: 10px 10px 0 0;
            text-align: center;
            padding: 20px;
        }
        .card-header h4 {
            margin: 0;
            font-weight: 600;
            font-size: 24px;
        }
        .card-body {
            padding: 30px;
        }
        .form-group label {
            color: #2c3e50;
            font-weight: 500;
            margin-bottom: 10px;
        }
        .form-control {
            border-radius: 5px;
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            margin-bottom: 15px;
        }
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        .btn-primary {
            background: #3498db;
            border: none;
            border-radius: 5px;
            padding: 12px 30px;
            font-weight: 500;
            margin-top: 10px;
        }
        .btn-primary:hover {
            background: #2980b9;
        }
        .text-center a {
            color: #3498db;
            text-decoration: none;
            font-size: 14px;
        }
        .text-center a:hover {
            text-decoration: underline;
        }
        .alert {
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .form-check {
            margin-top: 15px;
            margin-bottom: 15px;
        }
        .form-check-label {
            margin-left: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row justify-content-center login-container">
            <div class="col-md-3">
                <!-- Alert Messages -->
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>Login Gagal!</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <ul class="mt-2 mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Login Card -->
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-user-circle"></i> Login</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email / Username -->
                            <div class="form-group pt-3">
                                <label for="email">Email atau Username</label>
                                <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Masukkan email atau username" required autofocus value="{{ old('email') }}">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="form-group pt-3">
                                <label for="password">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Masukkan password" required>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Remember Me -->
                            <div class="form-check pt-2">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Ingat saya
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group pt-3">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </button>
                            </div>
                        </form>

                        <!-- Forgot Password Link -->
                        @if (Route::has('password.request'))
                            <div class="text-center pt-3">
                                <a href="{{ route('password.request') }}">
                                    <i class="fas fa-key"></i> Lupa Password?
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
