@extends('layouts.app')

@section('title', 'Buat Booking - Pilih Stylist')

@section('content')
    <div class="mb-3">
        <h1 class="h3"><i class="fas fa-calendar-plus"></i> Buat Booking Baru</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">1. Pilih Layanan</li>
                <li class="breadcrumb-item active">2. Pilih Stylist</li>
                <li class="breadcrumb-item text-muted">3. Pilih Tanggal & Waktu</li>
            </ol>
        </nav>
    </div>

    <div class="alert alert-info mb-3">
        <strong>Layanan dipilih:</strong> {{ $service->name }}
        ({{ $service->duration_minutes }} menit - Rp {{ number_format($service->price, 0, ',', '.') }})
    </div>

    <form action="{{ route('bookings.select-datetime') }}" method="GET">
        <input type="hidden" name="service_id" value="{{ $service->id }}">

        <div class="form-group">
            <label for="stylist_id">Pilih Stylist <span class="text-danger">*</span></label>
            <div class="row">
                @forelse ($stylists as $stylist)
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 {{ old('stylist_id') == $stylist->id ? 'border-primary' : '' }}">
                            <div class="card-body">
                                <div class="custom-control custom-radio">
                                    <input
                                        type="radio"
                                        id="stylist{{ $stylist->id }}"
                                        name="stylist_id"
                                        class="custom-control-input"
                                        value="{{ $stylist->id }}"
                                        {{ old('stylist_id') == $stylist->id ? 'checked' : '' }}
                                        required
                                    >
                                    <label class="custom-control-label w-100" for="stylist{{ $stylist->id }}">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-circle fa-2x mr-2"></i>
                                            <div>
                                                <strong>{{ $stylist->user->name }}</strong><br>
                                                <small class="text-muted">
                                                    <i class="fas fa-star"></i> {{ $stylist->specialty }}
                                                </small>
                                            </div>
                                        </div>
                                        @if ($stylist->bio)
                                            <p class="mt-2 mb-0 small text-muted">{{ $stylist->bio }}</p>
                                        @endif
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Tidak ada stylist yang tersedia saat ini.
                        </div>
                    </div>
                @endforelse
            </div>
            @error('stylist_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('bookings.create') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            @if ($stylists->isNotEmpty())
                <button type="submit" class="btn btn-primary">
                    Selanjutnya <i class="fas fa-arrow-right"></i>
                </button>
            @endif
        </div>
    </form>
@endsection
