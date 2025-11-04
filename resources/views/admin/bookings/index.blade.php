@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        @if(auth()->user()->isAdmin())
                            Kelola Semua Booking
                        @else
                            Kelola Booking Saya
                        @endif
                    </h4>
                </div>

                <div class="card-body">
                    {{-- Filters --}}
                    <form method="GET" action="{{ route('admin.bookings.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">Filter by Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Semua Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date">Filter by Date</label>
                                    <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if($bookings->isEmpty())
                        <div class="alert alert-info">
                            Tidak ada booking ditemukan.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Customer</th>
                                        @if(auth()->user()->isAdmin())
                                            <th>Stylist</th>
                                        @endif
                                        <th>Service</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings as $booking)
                                        <tr>
                                            <td>{{ $booking->id }}</td>
                                            <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</td>
                                            <td>{{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}</td>
                                            <td>{{ $booking->customer->name ?? '-' }}</td>
                                            @if(auth()->user()->isAdmin())
                                                <td>{{ $booking->stylist->user->name ?? '-' }}</td>
                                            @endif
                                            <td>{{ $booking->service->name ?? '-' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $booking->status_badge }}">
                                                    {{ $booking->status_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    @if($booking->status === 'pending')
                                                        <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST" class="mr-1">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-success btn-sm" title="Konfirmasi">
                                                                <i class="fa fa-check"></i> Confirm
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if($booking->status === 'confirmed')
                                                        <form action="{{ route('admin.bookings.complete', $booking) }}" method="POST" class="mr-1">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-primary btn-sm" title="Selesaikan">
                                                                <i class="fa fa-check-circle"></i> Complete
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if($booking->status !== 'completed' && $booking->status !== 'cancelled')
                                                        <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan booking ini?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Batalkan">
                                                                <i class="fa fa-times"></i> Cancel
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if($booking->status === 'completed' || $booking->status === 'cancelled')
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $bookings->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
