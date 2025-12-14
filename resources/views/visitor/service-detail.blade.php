@extends('layouts.visitor')

@section('title', $service->name . ' - Barber Booking')

@section('content')
<div class="container">
    <!-- Tombol Kembali -->
    <div class="mb-4">
        <a href="{{ route('visitor.search') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Layanan
        </a>
    </div>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('visitor.search') }}">
                    <i class="fas fa-home"></i> Layanan
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $service->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Service Image -->
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm">
                @if($service->image)
                <img src="{{ asset('storage/' . $service->image) }}" class="card-img-top" alt="{{ $service->name }}"
                     style="width: 100%; height: 400px; object-fit: cover;">
                @else
                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center"
                     style="height: 400px;">
                    <i class="fas fa-cut fa-5x text-white"></i>
                </div>
                @endif
            </div>
        </div>

        <!-- Service Details -->
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title mb-3">{{ $service->name }}</h2>

                    <!-- Price -->
                    <div class="mb-4">
                        <h3 class="mb-0" style="color: #2c3e50;">
                            <i class="fas fa-tag"></i>
                            Rp {{ number_format($service->price, 0, ',', '.') }}
                        </h3>
                    </div>

                    <!-- Duration -->
                    <div class="mb-4">
                        <h5><i class="fas fa-clock"></i> Durasi</h5>
                        <p class="lead">{{ $service->duration_minutes }} menit</p>
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <h5><i class="fas fa-info-circle"></i> Status</h5>
                        @if($service->is_active)
                        <span class="badge badge-success badge-lg px-3 py-2">
                            <i class="fas fa-check-circle"></i> Tersedia
                        </span>
                        @else
                        <span class="badge badge-danger badge-lg px-3 py-2">
                            <i class="fas fa-times-circle"></i> Tidak Tersedia
                        </span>
                        @endif
                    </div>

                    <hr>

                    <!-- Action Buttons -->
                    <div class="mt-4">
                        @guest
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Ingin booking layanan ini?</strong>
                            <br>
                            Silakan login atau daftar terlebih dahulu untuk melakukan booking.
                        </div>
                        <div class="btn-group btn-block" role="group">
                            <a href="{{ route('login') }}" class="btn btn-lg" style="background-color: #2c3e50; color: white;">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-lg" style="background-color: #28a745; color: white;">
                                <i class="fas fa-user-plus"></i> Daftar
                            </a>
                        </div>
                        @else
                            @if(Auth::user()->isCustomer())
                            <a href="{{ route('bookings.create') }}" class="btn btn-lg btn-block" style="background-color: #0056b3; color: white;">
                                <i class="fas fa-calendar-plus"></i> Booking Sekarang
                            </a>
                            @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Booking hanya tersedia untuk akun customer.
                            </div>
                            @endif
                        @endguest

                        <a href="{{ route('visitor.search') }}" class="btn btn-lg btn-block mt-2" style="background-color: transparent; color: #2c3e50; border: 1px solid #2c3e50;">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Layanan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Additional Info Card -->
            <div class="card shadow-sm mt-4">
                <div class="card-header" style="background-color: #2c3e50; color: white;">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Tambahan</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            Dikerjakan oleh stylist profesional
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            Menggunakan peralatan berkualitas tinggi
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            Harga sudah termasuk konsultasi
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            Bisa pilih stylist favorit
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success"></i>
                            Jadwal fleksibel sesuai ketersediaan
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Services Section -->
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">
                <i class="fas fa-list"></i> Layanan Lainnya
            </h3>
        </div>
    </div>

    <div class="row">
        @php
            $relatedServices = \App\Service::where('is_active', true)
                ->where('id', '!=', $service->id)
                ->inRandomOrder()
                ->limit(3)
                ->get();
        @endphp

        @forelse($relatedServices as $related)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm hover-card">
                @if($related->image)
                <img src="{{ asset('storage/' . $related->image) }}" class="card-img-top" alt="{{ $related->name }}"
                     style="height: 200px; object-fit: cover;">
                @else
                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center"
                     style="height: 200px;">
                    <i class="fas fa-cut fa-4x text-white"></i>
                </div>
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $related->name }}</h5>
                    <div class="mb-2">
                        <span class="badge" style="background-color: #2c3e50; color: white;">
                            <i class="fas fa-clock"></i> {{ $related->duration_minutes }} menit
                        </span>
                    </div>
                    <h4 class="mb-3" style="color: #2c3e50;">
                        <i class="fas fa-tag"></i> Rp {{ number_format($related->price, 0, ',', '.') }}
                    </h4>
                    <a href="{{ route('visitor.service.detail', $related->id) }}" class="btn btn-block" style="background-color: #0056b3; color: white;">
                        <i class="fas fa-info-circle"></i> Lihat Detail
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <p class="text-muted text-center">Tidak ada layanan lain yang tersedia.</p>
        </div>
        @endforelse
    </div>
</div>

<style>
.hover-card {
    transition: transform 0.3s, box-shadow 0.3s;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
}

.badge-lg {
    font-size: 1rem;
}

.card-img-top {
    border-top-left-radius: calc(0.25rem - 1px);
    border-top-right-radius: calc(0.25rem - 1px);
}

.breadcrumb {
    background-color: #f8f9fa;
}

.breadcrumb-item a {
    color: #007bff;
    text-decoration: none;
}

.breadcrumb-item a:hover {
    text-decoration: underline;
}
</style>
@endsection
