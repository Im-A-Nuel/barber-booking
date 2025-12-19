@extends('layouts.app')

@section('title', 'Reset Password - Barber Booking')

@section('content')
<style>
    .reset-password-container {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .reset-password-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        max-width: 500px;
        width: 100%;
    }
    .reset-password-card .card-body {
        padding: 40px;
    }
    .logo-section {
        text-align: center;
        margin-bottom: 30px;
    }
    .logo-section i {
        font-size: 60px;
        color: #27ae60;
        margin-bottom: 15px;
    }
    .reset-title {
        text-align: center;
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 10px;
        font-size: 24px;
    }
    .reset-subtitle {
        text-align: center;
        color: #7f8c8d;
        margin-bottom: 30px;
        font-size: 14px;
    }
    .form-control {
        border-radius: 8px;
        padding: 12px 15px;
        border: 1px solid #dee2e6;
    }
    .form-control:focus {
        border-color: #27ae60;
        box-shadow: 0 0 0 0.2rem rgba(39, 174, 96, 0.25);
    }
    .btn-reset {
        border-radius: 8px;
        padding: 12px 30px;
        background: #27ae60;
        border: none;
        font-weight: 500;
        width: 100%;
    }
    .btn-reset:hover {
        background: #219a52;
    }
    .back-to-login {
        text-align: center;
        margin-top: 20px;
    }
    .back-to-login a {
        color: #3498db;
        text-decoration: none;
    }
    .back-to-login a:hover {
        text-decoration: underline;
    }
</style>

<div class="reset-password-container">
    <div class="card reset-password-card">
        <div class="card-body">
            <div class="logo-section">
                <i class="fas fa-key"></i>
            </div>
            <h4 class="reset-title">Reset Password</h4>
            <p class="reset-subtitle">
                Masukkan password baru untuk akun Anda.
            </p>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Alamat Email
                    </label>
                    <input id="email" type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           name="email" 
                           value="{{ $email ?? old('email') }}" 
                           required 
                           autocomplete="email" 
                           autofocus
                           readonly>

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password Baru
                    </label>
                    <input id="password" type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           name="password" 
                           required 
                           autocomplete="new-password"
                           placeholder="Minimal 8 karakter">

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password-confirm">
                        <i class="fas fa-check-circle"></i> Konfirmasi Password
                    </label>
                    <input id="password-confirm" type="password" 
                           class="form-control" 
                           name="password_confirmation" 
                           required 
                           autocomplete="new-password"
                           placeholder="Ulangi password baru">
                </div>

                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-success btn-reset">
                        <i class="fas fa-save"></i> Reset Password
                    </button>
                </div>
            </form>

            <div class="back-to-login">
                <a href="{{ route('login') }}">
                    <i class="fas fa-arrow-left"></i> Kembali ke Login
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
