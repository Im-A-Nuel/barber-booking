@extends('layouts.app')

@section('title', 'Buat Booking - Pilih Layanan')

@section('content')
    <div class="mb-3">
        <h1 class="h3"><i class="fas fa-calendar-plus"></i> Buat Booking Baru</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">1. Pilih Layanan</li>
                <li class="breadcrumb-item text-muted">2. Pilih Stylist</li>
                <li class="breadcrumb-item text-muted">3. Pilih Tanggal & Waktu</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('bookings.select-stylist') }}" method="GET">
        <div class="form-group">
            <label for="service_id">Pilih Layanan <span class="text-danger">*</span></label>
            <div class="row">
                @foreach ($services as $service)
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 {{ old('service_id') == $service->id ? 'border-primary' : '' }}">
                            <div class="card-body">
                                <div class="custom-control custom-radio">
                                    <input
                                        type="radio"
                                        id="service{{ $service->id }}"
                                        name="service_id"
                                        class="custom-control-input"
                                        value="{{ $service->id }}"
                                        {{ old('service_id') == $service->id ? 'checked' : '' }}
                                        required
                                    >
                                    <label class="custom-control-label w-100" for="service{{ $service->id }}">
                                        <strong>{{ $service->name }}</strong><br>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i> {{ $service->duration_minutes }} menit<br>
                                            <i class="fas fa-money-bill-wave"></i> Rp {{ number_format($service->price, 0, ',', '.') }}
                                        </small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @error('service_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <button type="submit" class="btn btn-primary">
                Selanjutnya <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </form>
@endsection
