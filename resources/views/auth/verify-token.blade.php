@extends('layouts.app')

@section('title', 'Verifikasi Email - Barber Booking')

@section('content')
<style>
    .verify-container {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 0;
    }
    .verify-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        max-width: 500px;
        width: 100%;
    }
    .verify-card .card-body {
        padding: 40px;
    }
    .logo-section {
        text-align: center;
        margin-bottom: 30px;
    }
    .logo-section i {
        font-size: 60px;
        color: #f39c12;
        margin-bottom: 15px;
    }
    .verify-title {
        text-align: center;
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 10px;
        font-size: 24px;
    }
    .verify-subtitle {
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
        background: #f39c12;
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
    .token-input {
        text-align: center;
        font-size: 32px;
        font-weight: 700;
        letter-spacing: 10px;
        padding: 15px;
        border-radius: 10px;
        border: 2px solid #dee2e6;
    }
    .token-input:focus {
        border-color: #f39c12;
        box-shadow: 0 0 0 0.2rem rgba(243, 156, 18, 0.25);
    }
    .btn-verify {
        border-radius: 8px;
        padding: 12px 30px;
        background: #f39c12;
        border: none;
        font-weight: 500;
        transition: all 0.3s;
    }
    .btn-verify:hover {
        background: #e67e22;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(243, 156, 18, 0.3);
    }
    .email-info {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        margin-bottom: 20px;
    }
    .email-info strong {
        color: #2c3e50;
    }
    .resend-link {
        text-align: center;
        margin-top: 20px;
    }
    .resend-link a {
        color: #3498db;
        text-decoration: none;
    }
    .resend-link a:hover {
        text-decoration: underline;
    }
</style>

<div class="verify-container">
    <div class="card verify-card">
        <div class="card-body">
            <div class="logo-section">
                <i class="fas fa-envelope-open-text"></i>
            </div>
            <h4 class="verify-title">Verifikasi Email</h4>
            <p class="verify-subtitle">Langkah 2: Masukkan kode verifikasi</p>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step completed"><i class="fas fa-check"></i></div>
                <div class="step-line completed"></div>
                <div class="step active">2</div>
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

            <div class="email-info">
                <i class="fas fa-envelope"></i> Kode verifikasi telah dikirim ke:<br>
                <strong>{{ $email }}</strong>
            </div>

            <form method="POST" action="{{ route('register.verify') }}">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="form-group">
                    <label for="token">
                        <i class="fas fa-key"></i> Kode Verifikasi (6 digit)
                    </label>
                    <input id="token" type="text" 
                           class="form-control token-input @error('token') is-invalid @enderror" 
                           name="token" 
                           maxlength="6"
                           required 
                           autofocus
                           placeholder="000000">
                    @error('token')
                        <span class="invalid-feedback text-center">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-warning btn-verify btn-block text-white">
                        <i class="fas fa-check-circle"></i> Verifikasi
                    </button>
                </div>
            </form>

            <div class="resend-link">
                <p class="text-muted mb-2">Tidak menerima kode?</p>
                <form method="POST" action="{{ route('register.resend') }}" style="display: inline;">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <button type="submit" class="btn btn-link p-0">
                        <i class="fas fa-redo"></i> Kirim ulang kode
                    </button>
                </form>
            </div>

            <hr>

            <div class="text-center">
                <a href="{{ route('register') }}" class="text-muted">
                    <i class="fas fa-arrow-left"></i> Kembali ke halaman pendaftaran
                </a>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Auto focus and format token input
    document.getElementById('token').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6);
    });
</script>
@endsection
@endsection
