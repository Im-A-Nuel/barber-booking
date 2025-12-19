@extends('layouts.app')

@section('title', 'Lupa Password - Barber Booking')

@section('content')
<style>
    .forgot-password-container {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .forgot-password-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        max-width: 500px;
        width: 100%;
    }
    .forgot-password-card .card-body {
        padding: 40px;
    }
    .logo-section {
        text-align: center;
        margin-bottom: 30px;
    }
    .logo-section i {
        font-size: 60px;
        color: #2c3e50;
        margin-bottom: 15px;
    }
    .forgot-title {
        text-align: center;
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 10px;
        font-size: 24px;
    }
    .forgot-subtitle {
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
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }
    .btn-reset {
        border-radius: 8px;
        padding: 12px 30px;
        background: #3498db;
        border: none;
        font-weight: 500;
        width: 100%;
    }
    .btn-reset:hover {
        background: #2980b9;
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

<div class="forgot-password-container">
    <div class="card forgot-password-card">
        <div class="card-body">
            <div class="logo-section">
                <i class="fas fa-lock"></i>
            </div>
            <h4 class="forgot-title">Lupa Password?</h4>
            <p class="forgot-subtitle">
                Masukkan email Anda dan kami akan mengirimkan link untuk reset password.
            </p>

            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('status') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Alamat Email
                    </label>
                    <input id="email" type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="email" 
                           autofocus
                           placeholder="Masukkan email Anda">

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary btn-reset">
                        <i class="fas fa-paper-plane"></i> Kirim Link Reset Password
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
