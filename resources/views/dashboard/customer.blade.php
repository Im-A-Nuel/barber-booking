@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
<style>
    .welcome-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .info-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        transition: transform 0.2s;
    }
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    .appointment-card {
        border-left: 4px solid #3498db;
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .appointment-time {
        color: #3498db;
        font-size: 1.1rem;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .appointment-title {
        font-size: 1.3rem;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    .alert-card {
        border-left: 4px solid;
        background: white;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .alert-failed {
        border-left-color: #e74c3c;
    }
    .alert-pending {
        border-left-color: #f39c12;
    }
    .history-item {
        display: flex;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid #ecf0f1;
    }
    .history-item:last-child {
        border-bottom: none;
    }
    .history-icon {
        width: 40px;
        height: 40px;
        background: #e3f2fd;
        color: #3498db;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }
    .section-title {
        font-size: 1.3rem;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 20px;
    }
</style>

<div class="container-fluid">
    <!-- Welcome Card -->
    <div class="welcome-card">
        <h2 class="mb-2">
            <i class="fas fa-hand-sparkles mr-2"></i>
            Selamat Datang, {{ auth()->user()->name }}!
        </h2>
        <p class="mb-0">Berikut ringkasan appointment dan detail akun Anda</p>
    </div>

    <div class="row">
        <!-- Left Column (Main Content) -->
        <div class="col-lg-8 mb-4">
            <!-- Upcoming Appointment -->
            <h3 class="section-title">
                <i class="fas fa-calendar-check mr-2"></i>
                Appointment Mendatang
            </h3>
            @if($nextAppointment)
            <div class="appointment-card">
                <div class="appointment-time">
                    <i class="fas fa-clock mr-2"></i>
                    {{ $nextAppointment->booking_date->format('l, d F Y') }} @ {{ substr($nextAppointment->start_time, 0, 5) }}
                </div>
                <div class="appointment-title">
                    {{ $nextAppointment->service->name }} dengan {{ $nextAppointment->stylist->user->name }}
                </div>
                <p class="text-muted mb-3">
                    {{ $nextAppointment->service->description ?? 'Potong rambut profesional dengan gaya yang Anda inginkan.' }}
                </p>
                <div class="mb-3">
                    <span class="text-muted mr-2">Status:</span>
                    @if($nextAppointment->status === 'confirmed')
                        <span class="badge badge-success">
                            <i class="fas fa-check-circle mr-1"></i>Dikonfirmasi
                        </span>
                    @else
                        <span class="badge badge-warning">
                            <i class="fas fa-clock mr-1"></i>Menunggu Konfirmasi
                        </span>
                    @endif
                </div>
                <div class="mt-3">
                    <a href="{{ route('bookings.show', $nextAppointment->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye mr-1"></i>Lihat Detail
                    </a>
                    @if($nextAppointment->canBeCancelled())
                    <form action="{{ route('bookings.cancel', $nextAppointment->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm" onclick="return confirm('Apakah Anda yakin ingin membatalkan booking ini?')">
                            <i class="fas fa-times mr-1"></i>Cancel
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @else
            <div class="info-card text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                <h5 class="text-muted mb-3">Belum Ada Appointment</h5>
                <p class="text-muted mb-4">Buat booking baru untuk mendapatkan layanan terbaik dari kami.</p>
                <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>Buat Booking Sekarang
                </a>
            </div>
            @endif

            <!-- Payment Alerts -->
            @if($paymentAlerts->count() > 0)
            <h3 class="section-title mt-5">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Notifikasi Pembayaran
            </h3>
            @foreach($paymentAlerts as $alert)
            <div class="alert-card {{ $alert->status === 'failed' ? 'alert-failed' : 'alert-pending' }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h5 class="mb-2 {{ $alert->status === 'failed' ? 'text-danger' : 'text-warning' }}">
                            <i class="fas fa-{{ $alert->status === 'failed' ? 'times-circle' : 'hourglass-half' }} mr-2"></i>
                            {{ $alert->status === 'failed' ? 'Pembayaran Gagal' : 'Pembayaran Pending' }}
                        </h5>
                        <p class="text-muted mb-0">
                            Untuk {{ $alert->booking->service->name }} pada {{ $alert->booking->booking_date->format('d M Y') }}. 
                            Jumlah: <strong>Rp {{ number_format($alert->amount, 0, ',', '.') }}</strong>
                        </p>
                    </div>
                    <a href="{{ route('payments.create', $alert->booking_id) }}" class="btn btn-sm {{ $alert->status === 'failed' ? 'btn-danger' : 'btn-warning' }}">
                        <i class="fas fa-credit-card mr-1"></i>
                        {{ $alert->status === 'failed' ? 'Bayar Ulang' : 'Bayar Sekarang' }}
                    </a>
                </div>
            </div>
            @endforeach
            @endif
        </div>

        <!-- Right Column (Sidebar) -->
        <div class="col-lg-4">
            <!-- Favorite Services -->
            @if($favoriteServices->count() > 0)
            <div class="info-card">
                <h5 class="mb-3">
                    <i class="fas fa-star text-warning mr-2"></i>
                    Layanan Favorit Anda
                </h5>
                @foreach($favoriteServices as $favorite)
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div>
                        <strong>{{ $favorite->service->name }}</strong><br>
                        <small class="text-muted">Rp {{ number_format($favorite->service->price, 0, ',', '.') }}</small>
                    </div>
                    <a href="{{ route('bookings.create') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-calendar-plus"></i> Book
                    </a>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Booking History -->
            <div class="info-card mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="fas fa-history mr-2"></i>
                        Riwayat Booking
                    </h5>
                    <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-link">Lihat Semua</a>
                </div>
                @if($bookingHistory->count() > 0)
                    @foreach($bookingHistory as $history)
                    <div class="history-item">
                        <div class="history-icon">
                            <i class="fas fa-cut"></i>
                        </div>
                        <div class="flex-grow-1">
                            <strong>{{ $history->service->name }}</strong><br>
                            <small class="text-muted">dengan {{ $history->stylist->user->name }}</small>
                        </div>
                        <div class="text-right">
                            <small class="text-muted">{{ $history->booking_date->format('d M') }}</small>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Belum ada riwayat booking</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
