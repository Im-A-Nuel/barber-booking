@extends('layouts.app')

@section('title', 'Booking Saya')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0"><i class="fas fa-calendar-alt"></i> Booking Saya</h1>
        <a href="{{ route('bookings.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Buat Booking Baru
        </a>
    </div>

    <div class="row">
        @forelse ($bookings as $booking)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0">{{ $booking->service->name }}</h5>
                            <span class="badge badge-{{ $booking->status_badge }}">
                                {{ $booking->status_label }}
                            </span>
                        </div>
                        <p class="card-text mb-2">
                            <i class="fas fa-cut"></i> <strong>Stylist:</strong> {{ $booking->stylist->user->name }}<br>
                            <i class="fas fa-calendar"></i> <strong>Tanggal:</strong> {{ $booking->booking_date->format('d M Y') }}<br>
                            <i class="fas fa-clock"></i> <strong>Waktu:</strong> {{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}<br>
                            <i class="fas fa-money-bill-wave"></i> <strong>Harga:</strong> Rp {{ number_format($booking->service->price, 0, ',', '.') }}
                        </p>
                        @if ($booking->notes)
                            <p class="card-text text-muted small">
                                <i class="fas fa-sticky-note"></i> {{ $booking->notes }}
                            </p>
                        @endif
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            @if ($booking->canBeCancelled())
                                <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan booking ini?');">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger" type="submit">
                                        <i class="fas fa-times"></i> Batalkan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Anda belum memiliki booking. <a href="{{ route('bookings.create') }}">Buat booking sekarang</a>
                </div>
            </div>
        @endforelse
    </div>

    {{ $bookings->links() }}
@endsection
