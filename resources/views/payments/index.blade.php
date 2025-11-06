@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0"><i class="fas fa-money-bill-wave"></i> Riwayat Pembayaran</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" action="{{ route('payments.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control form-control-sm">
                                <option value="">Semua Status</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="method">Metode</label>
                            <select name="method" id="method" class="form-control form-control-sm">
                                <option value="">Semua Metode</option>
                                <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                                <option value="transfer" {{ request('method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                <option value="e-wallet" {{ request('method') == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                                <option value="debit_card" {{ request('method') == 'debit_card' ? 'selected' : '' }}>Debit</option>
                                <option value="credit_card" {{ request('method') == 'credit_card' ? 'selected' : '' }}>Kredit</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_from">Dari Tanggal</label>
                            <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_to">Sampai Tanggal</label>
                            <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary btn-sm btn-block">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                @if(request()->anyFilled(['status', 'method', 'date_from', 'date_to']))
                                    <a href="{{ route('payments.index') }}" class="btn btn-secondary btn-sm btn-block mt-1">
                                        <i class="fas fa-redo"></i> Reset
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            @if ($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tanggal</th>
                                @if (auth()->user()->isAdmin())
                                    <th>Customer</th>
                                @endif
                                <th>Booking</th>
                                <th>Layanan</th>
                                <th>Jumlah</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td>#{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        {{ $payment->created_at->format('d M Y') }}<br>
                                        <small class="text-muted">{{ $payment->created_at->format('H:i') }}</small>
                                    </td>
                                    @if (auth()->user()->isAdmin())
                                        <td>{{ $payment->booking->customer->name }}</td>
                                    @endif
                                    <td>#{{ str_pad($payment->booking_id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        {{ $payment->booking->service->name }}<br>
                                        <small class="text-muted">
                                            <i class="fas fa-user-circle"></i> {{ $payment->booking->stylist->user->name }}
                                        </small>
                                    </td>
                                    <td><strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong></td>
                                    <td>
                                        @switch($payment->method)
                                            @case('cash')
                                                <span class="badge badge-success">Tunai</span>
                                                @break
                                            @case('transfer')
                                                <span class="badge badge-info">Transfer</span>
                                                @break
                                            @case('e-wallet')
                                                <span class="badge badge-primary">E-Wallet</span>
                                                @break
                                            @case('debit_card')
                                                <span class="badge badge-secondary">Debit</span>
                                                @break
                                            @case('credit_card')
                                                <span class="badge badge-warning">Kredit</span>
                                                @break
                                            @default
                                                {{ ucfirst($payment->method) }}
                                        @endswitch
                                    </td>
                                    <td>
                                        @switch($payment->status)
                                            @case('paid')
                                                <span class="badge badge-success">Lunas</span>
                                                @if ($payment->paid_at)
                                                    <br><small class="text-muted">{{ $payment->paid_at->format('d M Y') }}</small>
                                                @endif
                                                @break
                                            @case('pending')
                                                <span class="badge badge-warning">Pending</span>
                                                @break
                                            @case('failed')
                                                <span class="badge badge-danger">Gagal</span>
                                                @break
                                            @default
                                                {{ ucfirst($payment->status) }}
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('bookings.show', $payment->booking_id) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Lihat Detail Booking">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(in_array($payment->status, ['pending', 'failed']))
                                                <a href="{{ route('payments.edit', $payment->id) }}"
                                                   class="btn btn-sm btn-outline-warning"
                                                   title="Update Status Pembayaran">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('payments.receipt', $payment->id) }}"
                                               class="btn btn-sm btn-outline-success"
                                               title="Lihat Bukti Pembayaran">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $payments->links() }}
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i> Belum ada riwayat pembayaran.
                </div>
            @endif
        </div>
    </div>
@endsection
