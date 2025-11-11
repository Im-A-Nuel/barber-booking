@extends('layouts.app')

@section('content')
<style>
    .login-container {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .login-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .login-card .card-body {
        padding: 40px;
    }
    .logo-section {
        text-align: center;
        margin-bottom: 30px;
    }
    .logo-section img {
        max-width: 300px;
        width: 100%;
        margin-bottom: 10px;
    }
    .login-title {
        text-align: center;
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 30px;
        font-size: 24px;
    }
    .form-control {
        border-radius: 8px;
        padding: 12px 15px;
        border: 1px solid #dee2e6;
    }
    .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }
    .btn-login {
        border-radius: 8px;
        padding: 12px 30px;
        background: #3498db;
        border: none;
        font-weight: 500;
        transition: all 0.3s;
    }
    .btn-login:hover {
        background: #2980b9;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
    }
</style>

<div class="container login-container">
    <div class="row justify-content-center w-100">
        <div class="col-md-6">
            <div class="card login-card">
                <div class="card-body">
                    <div class="logo-section">
                        <img src="{{ asset('images/logo.png') }}" alt="Barber Booking Logo">
                    </div>
                    <h4 class="login-title">Selamat Datang Kembali</h4>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group">
                            <label for="email">Email atau Username</label>
                            <input id="email" type="text" class="form-control @error('email') @error('username') is-invalid @enderror @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Masukkan email atau username">

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            @error('username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Masukkan password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <button type="submit" class="btn btn-primary btn-login btn-block">
                                <i class="fas fa-sign-in-alt"></i> {{ __('Login') }}
                            </button>
                        </div>

                        @if (Route::has('password.request'))
                            <div class="text-center">
                                <a class="text-muted" href="{{ route('password.request') }}">
                                    <i class="fas fa-key"></i> {{ __('Forgot Your Password?') }}
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
