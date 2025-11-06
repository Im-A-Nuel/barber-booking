@extends('layouts.app')

@section('title', 'Update Status Pembayaran')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0"><i class="fas fa-edit"></i> Update Status Pembayaran</h1>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Form Update Pembayaran</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('payments.update', $payment->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Update status pembayaran untuk menyelesaikan pembayaran pending atau mengubah metode pembayaran.
                        </div>

                        <div class="form-group">
                            <label>Jumlah Pembayaran</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text"
                                       class="form-control"
                                       value="{{ number_format($payment->amount, 0, ',', '.') }}"
                                       readonly
                                       style="background-color: #e9ecef;">
                            </div>
                            <small class="form-text text-muted">Jumlah pembayaran tidak dapat diubah</small>
                        </div>

                        <div class="form-group">
                            <label for="method">Metode Pembayaran</label>
                            <select class="form-control @error('method') is-invalid @enderror"
                                    id="method"
                                    name="method">
                                <option value="cash" {{ old('method', $payment->method) == 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                                <option value="transfer" {{ old('method', $payment->method) == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                <option value="e-wallet" {{ old('method', $payment->method) == 'e-wallet' ? 'selected' : '' }}>E-Wallet (GoPay, OVO, Dana, dll)</option>
                                <option value="debit_card" {{ old('method', $payment->method) == 'debit_card' ? 'selected' : '' }}>Kartu Debit</option>
                                <option value="credit_card" {{ old('method', $payment->method) == 'credit_card' ? 'selected' : '' }}>Kartu Kredit</option>
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
                                <option value="pending" {{ old('status', $payment->status) == 'pending' ? 'selected' : '' }}>Pending (Belum Lunas)</option>
                                <option value="paid" {{ old('status', $payment->status) == 'paid' ? 'selected' : '' }}>Paid (Lunas)</option>
                                <option value="failed" {{ old('status', $payment->status) == 'failed' ? 'selected' : '' }}>Failed (Gagal)</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-lightbulb"></i>
                                Ubah status menjadi "Paid" setelah pembayaran dikonfirmasi.
                                Status booking akan otomatis berubah menjadi "Selesai".
                            </small>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Pembayaran</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th width="45%">Payment ID:</th>
                            <td>#PAY-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</td>
                        </tr>
                        <tr>
                            <th>Booking ID:</th>
                            <td>#{{ str_pad($payment->booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                        </tr>
                        <tr>
                            <th>Status Saat Ini:</th>
                            <td>
                                @switch($payment->status)
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
                            <th>Customer:</th>
                            <td>{{ $payment->booking->customer->name }}</td>
                        </tr>
                        <tr>
                            <th>Layanan:</th>
                            <td>{{ $payment->booking->service->name }}</td>
                        </tr>
                        <tr>
                            <th>Stylist:</th>
                            <td>{{ $payment->booking->stylist->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal:</th>
                            <td>{{ $payment->booking->booking_date->format('d M Y') }}</td>
                        </tr>
                        @if($payment->paid_at)
                            <tr>
                                <th>Dibayar Pada:</th>
                                <td>{{ $payment->paid_at->format('d M Y H:i') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
