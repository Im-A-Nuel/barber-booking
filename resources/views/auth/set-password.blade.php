@extends('layouts.app')

@section('title', 'Buat Password - Barber Booking')

@section('content')
<style>
    .password-container {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 0;
    }
    .password-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        max-width: 500px;
        width: 100%;
    }
    .password-card .card-body {
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
    .password-title {
        text-align: center;
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 10px;
        font-size: 24px;
    }
    .password-subtitle {
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
    }
    .step.active {
        background: #27ae60;
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
    .step-line.completed {
        background: #27ae60;
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
    .btn-complete {
        border-radius: 8px;
        padding: 12px 30px;
        background: #27ae60;
        border: none;
        font-weight: 500;
        transition: all 0.3s;
    }
    .btn-complete:hover {
        background: #219a52;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
    }
    .user-info {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .user-info p {
        margin: 5px 0;
        color: #2c3e50;
    }
    .user-info i {
        width: 20px;
        color: #7f8c8d;
    }
</style>

<div class="password-container">
    <div class="card password-card">
        <div class="card-body">
            <div class="logo-section">
                <i class="fas fa-lock"></i>
            </div>
            <h4 class="password-title">Buat Password</h4>
            <p class="password-subtitle">Langkah 3: Buat password untuk akun Anda</p>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step completed"><i class="fas fa-check"></i></div>
                <div class="step-line completed"></div>
                <div class="step completed"><i class="fas fa-check"></i></div>
                <div class="step-line completed"></div>
                <div class="step active">3</div>
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

            <div class="user-info">
                <p><i class="fas fa-user"></i> <strong>{{ $name }}</strong></p>
                <p><i class="fas fa-envelope"></i> {{ $email }}</p>
            </div>

            <form method="POST" action="{{ route('register.password') }}">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input id="password" type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           name="password" 
                           required 
                           autofocus
                           placeholder="Minimal 8 karakter">
                    @error('password')
                        <span class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <small class="form-text text-muted">Password minimal 8 karakter.</small>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">
                        <i class="fas fa-check-circle"></i> Konfirmasi Password
                    </label>
                    <input id="password_confirmation" type="password" 
                           class="form-control" 
                           name="password_confirmation" 
                           required
                           placeholder="Ulangi password">
                </div>

                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-success btn-complete btn-block">
                        <i class="fas fa-check"></i> Selesaikan Pendaftaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
