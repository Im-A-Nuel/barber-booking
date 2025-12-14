@extends('layouts.app')

@section('title', 'Daftar Akun - Barber Booking')

@section('content')
<style>
    .register-container {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 0;
    }
    .register-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        max-width: 500px;
        width: 100%;
    }
    .register-card .card-body {
        padding: 40px;
    }
    .logo-section {
        text-align: center;
        margin-bottom: 30px;
    }
    .logo-section i {
        font-size: 60px;
        color: #3498db;
        margin-bottom: 15px;
    }
    .register-title {
        text-align: center;
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 10px;
        font-size: 24px;
    }
    .register-subtitle {
        text-align: center;
        color: #7f8c8d;
        margin-bottom: 30px;
        font-size: 14px;
    }
    .step-indicator {
        display: flex;
        justify-content: center;
        margin-bottom: 30px;
    }
    .step {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: #ecf0f1;
        color: #95a5a6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin: 0 10px;
        position: relative;
    }
    .step.active {
        background: #3498db;
        color: white;
    }
    .step.completed {
        background: #27ae60;
        color: white;
    }
    .step-line {
        width: 50px;
        height: 3px;
        background: #ecf0f1;
        align-self: center;
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
    .btn-register {
        border-radius: 8px;
        padding: 12px 30px;
        background: #3498db;
        border: none;
        font-weight: 500;
        transition: all 0.3s;
    }
    .btn-register:hover {
        background: #2980b9;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
    }
    .login-link {
        text-align: center;
        margin-top: 20px;
    }
    .login-link a {
        color: #3498db;
        text-decoration: none;
    }
    .login-link a:hover {
        text-decoration: underline;
    }
</style>

<div class="register-container">
    <div class="card register-card">
        <div class="card-body">
            <div class="logo-section">
                <i class="fas fa-user-plus"></i>
            </div>
            <h4 class="register-title">Daftar Akun Baru</h4>
            <p class="register-subtitle">Langkah 1: Isi data diri Anda</p>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active">1</div>
                <div class="step-line"></div>
                <div class="step">2</div>
                <div class="step-line"></div>
                <div class="step">3</div>
            </div>

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

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <label for="name">
                        <i class="fas fa-user"></i> Nama Lengkap
                    </label>
                    <input id="name" type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required 
                           autofocus
                           placeholder="Masukkan nama lengkap">
                    @error('name')
                        <span class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-at"></i> Username
                    </label>
                    <input id="username" type="text" 
                           class="form-control @error('username') is-invalid @enderror" 
                           name="username" 
                           value="{{ old('username') }}" 
                           required
                           placeholder="Masukkan username">
                    @error('username')
                        <span class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <small class="form-text text-muted">Hanya huruf, angka, dash, dan underscore.</small>
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input id="email" type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required
                           placeholder="Masukkan email aktif">
                    @error('email')
                        <span class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <small class="form-text text-muted">Kode verifikasi akan dikirim ke email ini.</small>
                </div>

                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary btn-register btn-block">
                        <i class="fas fa-paper-plane"></i> Kirim Kode Verifikasi
                    </button>
                </div>
            </form>

            <div class="login-link">
                Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
            </div>
        </div>
    </div>
</div>
@endsection
