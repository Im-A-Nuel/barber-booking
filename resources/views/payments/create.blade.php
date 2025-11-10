@extends('layouts.app')

@section('title', 'Catat Pembayaran')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0"><i class="fas fa-money-bill-wave"></i> Catat Pembayaran</h1>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Form Pembayaran</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('payments.store', $booking->id) }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="amount">Jumlah Pembayaran <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number"
                                       class="form-control @error('amount') is-invalid @enderror"
                                       id="amount"
                                       name="amount"
                                       value="{{ old('amount', $booking->service->price) }}"
                                       min="0"
                                       step="0.01"
                                       readonly
                                       style="background-color: #e9ecef; cursor: not-allowed;"
                                       required>
                            </div>
                            @error('amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted"><i class="fas fa-info-circle"></i> Jumlah pembayaran otomatis sesuai harga layanan</small>
                        </div>

                        <div class="form-group">
                            <label for="method">Metode Pembayaran <span class="text-danger">*</span></label>
                            <select class="form-control @error('method') is-invalid @enderror"
                                    id="method"
                                    name="method"
                                    required>
                                <option value="">-- Pilih Metode --</option>
                                <option value="cash" {{ old('method') == 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                                <option value="transfer" {{ old('method') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                <option value="e-wallet" {{ old('method') == 'e-wallet' ? 'selected' : '' }}>E-Wallet (GoPay, OVO, Dana, dll)</option>
                                <option value="debit_card" {{ old('method') == 'debit_card' ? 'selected' : '' }}>Kartu Debit</option>
                                <option value="credit_card" {{ old('method') == 'credit_card' ? 'selected' : '' }}>Kartu Kredit</option>
                            </select>
                            @error('method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="status">Status Pembayaran <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror"
                                    id="status"
                                    name="status"
                                    required>
                                <option value="">-- Pilih Status --</option>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending (Belum Lunas)</option>
                                <option value="paid" {{ old('status', 'paid') == 'paid' ? 'selected' : '' }}>Paid (Lunas)</option>
                                <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>Failed (Gagal)</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Booking</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th width="45%">ID Booking:</th>
                            <td>#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge badge-{{ $booking->status_badge }}">
                                    {{ $booking->status_label }}
                                </span>
                            </td>
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
                            <td><strong class="text-primary">Rp {{ number_format($booking->service->price, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <th>Tanggal:</th>
                            <td>{{ $booking->booking_date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th>Waktu:</th>
                            <td>{{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}</td>
                        </tr>
                        <tr>
                            <th>Stylist:</th>
                            <td>{{ $booking->stylist->user->name }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
