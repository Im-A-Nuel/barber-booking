@extends('layouts.app')

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
    }
    .register-card .card-body {
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
    .register-title {
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
</style>

<div class="container register-container">
    <div class="row justify-content-center w-100">
        <div class="col-md-6">
            <div class="card register-card">
                <div class="card-body">
                    <div class="logo-section">
                        <img src="{{ asset('images/logo.png') }}" alt="Barber Booking Logo">
                    </div>
                    <h4 class="register-title">Daftar Akun Baru</h4>
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group">
                            <label for="name">{{ __('Name') }}</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Masukkan nama lengkap">

                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="username">Username</label>
                            <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" placeholder="Masukkan username">

                            @error('username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">Username hanya boleh berisi huruf, angka, dash, dan underscore.</small>
                        </div>

                        <div class="form-group">
                            <label for="email">{{ __('E-Mail Address') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Masukkan email">

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Masukkan password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password-confirm">{{ __('Confirm Password') }}</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password">
                        </div>

                        <div class="form-group mb-3">
                            <button type="submit" class="btn btn-primary btn-register btn-block">
                                <i class="fas fa-user-plus"></i> {{ __('Register') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
