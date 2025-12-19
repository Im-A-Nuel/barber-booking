@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<style>
    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: #2c3e50;
    }
    .stat-label {
        color: #7f8c8d;
        font-size: 0.9rem;
        margin-bottom: 8px;
    }
    .stat-trend {
        font-size: 0.85rem;
        font-weight: 500;
    }
    .trend-up { color: #27ae60; }
    .trend-down { color: #e74c3c; }
    .chart-container {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    .appointments-table {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .page-header {
        margin-bottom: 30px;
    }
    .page-title {
        font-size: 2rem;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 5px;
    }
    .page-subtitle {
        color: #7f8c8d;
        font-size: 1rem;
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-home mr-2"></i>
            Selamat Datang, {{ auth()->user()->name }}!
        </h1>
        <p class="page-subtitle">Berikut ringkasan aktivitas barbershop hari ini</p>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-label">
                    <i class="fas fa-calendar-check mr-1"></i> Total Booking Hari Ini
                </div>
                <div class="stat-number">{{ $todayBookings }}</div>
                <div class="stat-trend {{ $bookingTrend >= 0 ? 'trend-up' : 'trend-down' }}">
                    <i class="fas fa-{{ $bookingTrend >= 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i>
                    {{ $bookingTrend >= 0 ? '+' : '' }}{{ $bookingTrend }}% dari kemarin
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-label">
                    <i class="fas fa-calendar-week mr-1"></i> Booking Minggu Ini
                </div>
                <div class="stat-number">{{ $thisWeekBookings }}</div>
                <div class="stat-trend {{ $weeklyBookingTrend >= 0 ? 'trend-up' : 'trend-down' }}">
                    <i class="fas fa-{{ $weeklyBookingTrend >= 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i>
                    {{ $weeklyBookingTrend >= 0 ? '+' : '' }}{{ $weeklyBookingTrend }}% dari minggu lalu
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-label">
                    <i class="fas fa-money-bill-wave mr-1"></i> Revenue Minggu Ini
                </div>
                <div class="stat-number">Rp {{ number_format($thisWeekRevenue, 0, ',', '.') }}</div>
                <div class="stat-trend trend-up">
                    <i class="fas fa-check-circle mr-1"></i> Total pendapatan
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-label">
                    <i class="fas fa-hourglass-half mr-1"></i> Pembayaran Pending
                </div>
                <div class="stat-number">{{ $pendingPayments }}</div>
                <div class="stat-trend" style="color: #f39c12;">
                    <i class="fas fa-exclamation-circle mr-1"></i> Memerlukan verifikasi
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="chart-container">
                <h4 class="mb-3">
                    <i class="fas fa-chart-line mr-2"></i>
                    Tren Booking & Revenue
                </h4>
                <p class="text-muted small mb-3">7 Hari Terakhir</p>
                <div class="text-center py-5">
                    <i class="fas fa-chart-area fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Chart akan ditampilkan di sini</p>
                    <small class="text-muted">Grafik tren booking dan revenue 7 hari terakhir</small>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="chart-container">
                <h4 class="mb-3">
                    <i class="fas fa-credit-card mr-2"></i>
                    Metode Pembayaran
                </h4>
                <p class="text-muted small mb-3">7 Hari Terakhir</p>
                @if($paymentMethods->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($paymentMethods as $method)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="fas fa-circle text-primary mr-2" style="font-size: 8px;"></i>
                                <span class="text-capitalize">{{ $method->method }}</span>
                            </div>
                            <span class="badge badge-primary badge-pill">{{ $method->count }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-wallet fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada pembayaran</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Upcoming Appointments Table -->
    <div class="appointments-table">
        <div class="p-4 border-bottom">
            <h4 class="mb-0">
                <i class="fas fa-calendar-alt mr-2"></i>
                Jadwal Booking Mendatang
            </h4>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th><i class="fas fa-clock mr-1"></i> Waktu</th>
                        <th><i class="fas fa-user mr-1"></i> Customer</th>
                        <th><i class="fas fa-user-tie mr-1"></i> Stylist</th>
                        <th><i class="fas fa-cut mr-1"></i> Service</th>
                        <th><i class="fas fa-info-circle mr-1"></i> Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($upcomingAppointments as $booking)
                    <tr>
                        <td>
                            <strong>{{ $booking->booking_date->format('d M Y') }}</strong><br>
                            <small class="text-muted">{{ substr($booking->start_time, 0, 5) }}</small>
                        </td>
                        <td>{{ $booking->customer->name }}</td>
                        <td>{{ $booking->stylist->user->name }}</td>
                        <td>{{ $booking->service->name }}</td>
                        <td>
                            @if($booking->status === 'confirmed')
                                <span class="badge badge-success">
                                    <i class="fas fa-check mr-1"></i>Confirmed
                                </span>
                            @elseif($booking->status === 'pending')
                                <span class="badge badge-warning">
                                    <i class="fas fa-clock mr-1"></i>Pending
                                </span>
                            @elseif($booking->status === 'completed')
                                <span class="badge badge-info">
                                    <i class="fas fa-check-circle mr-1"></i>Completed
                                </span>
                            @else
                                <span class="badge badge-secondary">{{ $booking->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i><br>
                            <span class="text-muted">Belum ada booking mendatang</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($upcomingAppointments->count() > 0)
        <div class="p-3 text-center border-top">
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-list mr-1"></i> Lihat Semua Appointment
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
