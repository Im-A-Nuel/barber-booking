@extends('layouts.app')

@section('title', 'Pembayaran via Gateway')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-credit-card"></i> Pembayaran Booking #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}
                        </h5>
                    </div>
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-cut fa-3x text-primary mb-3"></i>
                            <h4 class="font-weight-bold">{{ $booking->service->name }}</h4>
                            <p class="text-muted mb-1">
                                <i class="fas fa-user-circle"></i> Stylist: {{ $booking->stylist->user->name }}
                            </p>
                            <p class="text-muted">
                                <i class="fas fa-calendar"></i> {{ $booking->booking_date->format('d M Y') }} -
                                <i class="fas fa-clock"></i> {{ date('H:i', strtotime($booking->start_time)) }}
                            </p>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Total Pembayaran</strong>
                        </div>

                        <h2 class="text-primary font-weight-bold mb-4">
                            Rp {{ number_format($booking->service->price, 0, ',', '.') }}
                        </h2>

                        <button id="pay-button" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-lock"></i> Bayar Sekarang
                        </button>

                        <div class="mt-4">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt"></i> Pembayaran aman dengan Midtrans
                            </small>
                        </div>

                        <div class="mt-3">
                            <small class="text-muted d-block">Metode pembayaran yang tersedia:</small>
                            <div class="mt-2">
                                <span class="badge badge-pill badge-light mr-1">GoPay</span>
                                <span class="badge badge-pill badge-light mr-1">ShopeePay</span>
                                <span class="badge badge-pill badge-light mr-1">QRIS</span>
                                <span class="badge badge-pill badge-light mr-1">Virtual Account</span>
                                <span class="badge badge-pill badge-light mr-1">Kartu Kredit/Debit</span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali ke Detail Booking
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="font-weight-bold mb-3">
                            <i class="fas fa-question-circle"></i> Informasi Pembayaran
                        </h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-check text-success"></i>
                                Pembayaran akan otomatis terverifikasi setelah berhasil
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success"></i>
                                Status booking akan otomatis berubah menjadi "Selesai"
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success"></i>
                                Link pembayaran berlaku selama 24 jam
                            </li>
                            <li class="mb-0">
                                <i class="fas fa-check text-success"></i>
                                Anda akan menerima bukti pembayaran setelah transaksi berhasil
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Midtrans Snap.js -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function(){
            snap.pay('{{ $snap_token }}', {
                onSuccess: function(result){
                    console.log('Payment success:', result);
                    window.location.href = "{{ route('bookings.show', $booking->id) }}?payment=success";
                },
                onPending: function(result){
                    console.log('Payment pending:', result);
                    window.location.href = "{{ route('bookings.show', $booking->id) }}?payment=pending";
                },
                onError: function(result){
                    console.log('Payment error:', result);
                    window.location.href = "{{ route('bookings.show', $booking->id) }}?payment=error";
                },
                onClose: function(){
                    console.log('Payment popup closed');
                    alert('Anda menutup halaman pembayaran. Silakan klik "Bayar Sekarang" untuk melanjutkan pembayaran.');
                }
            });
        };
    </script>
@endsection
