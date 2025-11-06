@extends('layouts.app')

@section('title', 'Bukti Pembayaran')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0"><i class="fas fa-receipt"></i> BUKTI PEMBAYARAN</h4>
                    <small>Barber Booking System</small>
                </div>
                <div class="card-body">
                    <!-- Payment Info -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <h6 class="text-muted mb-2">NOMOR PEMBAYARAN</h6>
                            <h5 class="font-weight-bold">#PAY-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</h5>
                        </div>
                        <div class="col-6 text-right">
                            <h6 class="text-muted mb-2">TANGGAL</h6>
                            <h5 class="font-weight-bold">{{ $payment->created_at->format('d M Y H:i') }}</h5>
                        </div>
                    </div>

                    <hr>

                    <!-- Customer Info -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">INFORMASI PELANGGAN</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td width="30%"><strong>Nama:</strong></td>
                                <td>{{ $payment->booking->customer->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $payment->booking->customer->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Username:</strong></td>
                                <td>{{ $payment->booking->customer->username }}</td>
                            </tr>
                        </table>
                    </div>

                    <hr>

                    <!-- Booking Info -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">DETAIL LAYANAN</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td width="30%"><strong>Booking ID:</strong></td>
                                <td>#{{ str_pad($payment->booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Layanan:</strong></td>
                                <td>{{ $payment->booking->service->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Durasi:</strong></td>
                                <td>{{ $payment->booking->service->duration_minutes }} menit</td>
                            </tr>
                            <tr>
                                <td><strong>Stylist:</strong></td>
                                <td>{{ $payment->booking->stylist->user->name }} - {{ $payment->booking->stylist->specialty }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal & Waktu:</strong></td>
                                <td>
                                    {{ $payment->booking->booking_date->format('d M Y') }},
                                    {{ substr($payment->booking->start_time, 0, 5) }} - {{ substr($payment->booking->end_time, 0, 5) }}
                                </td>
                            </tr>
                        </table>
                    </div>

                    <hr>

                    <!-- Payment Details -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">DETAIL PEMBAYARAN</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td width="30%"><strong>Jumlah:</strong></td>
                                <td><strong class="h5">Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Metode:</strong></td>
                                <td>
                                    @switch($payment->method)
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
                                            {{ ucfirst($payment->method) }}
                                    @endswitch
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @switch($payment->status)
                                        @case('paid')
                                            <span class="badge badge-success badge-lg">LUNAS</span>
                                            @break
                                        @case('pending')
                                            <span class="badge badge-warning badge-lg">PENDING</span>
                                            @break
                                        @case('failed')
                                            <span class="badge badge-danger badge-lg">GAGAL</span>
                                            @break
                                    @endswitch
                                </td>
                            </tr>
                            @if($payment->paid_at)
                                <tr>
                                    <td><strong>Dibayar Pada:</strong></td>
                                    <td>{{ $payment->paid_at->format('d M Y H:i') }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>

                    <hr>

                    <!-- Footer -->
                    <div class="text-center text-muted mt-4">
                        <small>
                            <i class="fas fa-info-circle"></i>
                            Bukti pembayaran ini dicetak otomatis oleh sistem.<br>
                            Terima kasih telah menggunakan layanan kami.
                        </small>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="fas fa-print"></i> Cetak
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<style>
    @media print {
        .navbar, .card-footer, .btn {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endsection
