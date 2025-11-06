@extends('layouts.app')

@section('title', 'Detail Booking')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0"><i class="fas fa-calendar-check"></i> Detail Booking</h1>
        <span class="badge badge-{{ $booking->status_badge }} badge-lg" style="font-size: 1rem;">
            {{ $booking->status_label }}
        </span>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title"><i class="fas fa-info-circle"></i> Informasi Booking</h5>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">ID Booking:</th>
                            <td>#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                        </tr>
                        <tr>
                            <th>Layanan:</th>
                            <td>{{ $booking->service->name }}</td>
                        </tr>
                        <tr>
                            <th>Durasi:</th>
                            <td>{{ $booking->service->duration_minutes }} menit</td>
                        </tr>
                        <tr>
                            <th>Harga:</th>
                            <td><strong>Rp {{ number_format($booking->service->price, 0, ',', '.') }}</strong></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5 class="card-title"><i class="fas fa-calendar-alt"></i> Jadwal</h5>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Tanggal:</th>
                            <td>{{ $booking->booking_date->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Waktu:</th>
                            <td>{{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}</td>
                        </tr>
                        <tr>
                            <th>Stylist:</th>
                            <td>
                                <i class="fas fa-user-circle"></i> {{ $booking->stylist->user->name }}<br>
                                <small class="text-muted">{{ $booking->stylist->specialty }}</small>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            @if ($booking->notes)
                <hr>
                <div>
                    <h5 class="card-title"><i class="fas fa-sticky-note"></i> Catatan</h5>
                    <p class="text-muted">{{ $booking->notes }}</p>
                </div>
            @endif

            <hr>
            <div>
                <h5 class="card-title"><i class="fas fa-user"></i> Informasi Customer</h5>
                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="20%">Nama:</th>
                        <td>{{ $booking->customer->name }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $booking->customer->email }}</td>
                    </tr>
                </table>
            </div>

            <hr>
            <div>
                <h5 class="card-title"><i class="fas fa-money-bill-wave"></i> Informasi Pembayaran</h5>
                @if ($booking->payment)
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="20%">Status:</th>
                            <td>
                                @switch($booking->payment->status)
                                    @case('paid')
                                        <span class="badge badge-success">Lunas</span>
                                        @break
                                    @case('pending')
                                        <span class="badge badge-warning">Pending</span>
                                        @break
                                    @case('failed')
                                        <span class="badge badge-danger">Gagal</span>
                                        @break
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <th>Jumlah:</th>
                            <td><strong>Rp {{ number_format($booking->payment->amount, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <th>Metode:</th>
                            <td>
                                @switch($booking->payment->method)
                                    @case('cash')
                                        Tunai (Cash)
                                        @break
                                    @case('transfer')
                                        Transfer Bank
                                        @break
                                    @case('e-wallet')
                                        E-Wallet
                                        @break
                                    @case('debit_card')
                                        Kartu Debit
                                        @break
                                    @case('credit_card')
                                        Kartu Kredit
                                        @break
                                    @default
                                        {{ ucfirst($booking->payment->method) }}
                                @endswitch
                            </td>
                        </tr>
                        @if ($booking->payment->paid_at)
                            <tr>
                                <th>Tanggal Bayar:</th>
                                <td>{{ $booking->payment->paid_at->format('d M Y H:i') }}</td>
                            </tr>
                        @endif
                    </table>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Pembayaran belum dicatat.
                        @if (in_array($booking->status, [\App\Booking::STATUS_CONFIRMED, \App\Booking::STATUS_COMPLETED]) && ($booking->customer_id === auth()->id() || auth()->user()->isAdmin()))
                            <a href="{{ route('payments.create', $booking->id) }}" class="btn btn-sm btn-primary ml-2">
                                <i class="fas fa-plus"></i> Catat Pembayaran
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <hr>
            <div class="text-muted small">
                <i class="fas fa-clock"></i> Dibuat pada: {{ $booking->created_at->format('d M Y H:i') }}<br>
                @if ($booking->created_at != $booking->updated_at)
                    <i class="fas fa-edit"></i> Terakhir diupdate: {{ $booking->updated_at->format('d M Y H:i') }}
                @endif
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between">
                <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
                @if ($booking->canBeCancelled() && $booking->customer_id === auth()->id())
                    <form action="{{ route('bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan booking ini?');">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times"></i> Batalkan Booking
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
