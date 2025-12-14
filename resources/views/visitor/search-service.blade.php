@extends('layouts.visitor')

@section('title', 'Cari Layanan - Barber Booking')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="text-center mb-4">
                <h1 class="display-4">
                    <i class="fas fa-search"></i> Cari Layanan Barber
                </h1>
                <p class="lead text-muted">Temukan layanan terbaik untuk penampilan Anda</p>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header" style="background-color: #2c3e50; color: white;">
                    <h5 class="mb-0"><i class="fas fa-filter"></i> Filter Pencarian</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('visitor.search') }}" id="searchForm">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="search" class="form-label">Cari Nama Layanan</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="search" name="search"
                                           value="{{ request('search') }}" placeholder="Contoh: Haircut, Shaving...">
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="min_price" class="form-label">Harga Minimum</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="number" class="form-control" id="min_price" name="min_price"
                                           value="{{ request('min_price') }}" placeholder="0" min="0" step="1000">
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="max_price" class="form-label">Harga Maksimum</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="number" class="form-control" id="max_price" name="max_price"
                                           value="{{ request('max_price') }}" placeholder="1000000" min="0" step="1000">
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-block" style="background-color: #2c3e50; color: white;">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                            </div>
                        </div>
                        @if(request('search') || request('min_price') || request('max_price'))
                        <div class="row">
                            <div class="col-12">
                                <a href="{{ route('visitor.search') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-times"></i> Reset Filter
                                </a>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="row">
        @forelse($services as $service)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm hover-card">
                @if($service->image)
                <img src="{{ asset('storage/' . $service->image) }}" class="card-img-top" alt="{{ $service->name }}"
                     style="height: 200px; object-fit: cover;">
                @else
                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center"
                     style="height: 200px;">
                    <i class="fas fa-cut fa-4x text-white"></i>
                </div>
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $service->name }}</h5>
                    <div class="mb-2">
                        <span class="badge" style="background-color: #2c3e50; color: white;">
                            <i class="fas fa-clock"></i> {{ $service->duration_minutes }} menit
                        </span>
                    </div>
                    <h4 class="mb-3" style="color: #2c3e50;">
                        <i class="fas fa-tag"></i> Rp {{ number_format($service->price, 0, ',', '.') }}
                    </h4>
                    <a href="{{ route('visitor.service.detail', $service->id) }}" class="btn btn-block" style="background-color: #0056b3; color: white;">
                        <i class="fas fa-info-circle"></i> Lihat Detail
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Tidak Ada Layanan Ditemukan</h4>
                    <p class="text-muted">
                        @if(request('search') || request('min_price') || request('max_price'))
                            Tidak ada layanan yang sesuai dengan filter pencarian Anda.
                            <br>
                            <a href="{{ route('visitor.search') }}" class="btn mt-3" style="background-color: transparent; color: #2c3e50; border: 1px solid #2c3e50;">
                                <i class="fas fa-redo"></i> Lihat Semua Layanan
                            </a>
                        @else
                            Belum ada layanan yang tersedia saat ini.
                        @endif
                    </p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($services->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $services->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    @endif

    <!-- Info Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-info-circle"></i> Informasi</h5>
                    <p class="mb-2">
                        <i class="fas fa-check-circle text-success"></i>
                        Untuk melakukan booking, silakan <a href="{{ route('login') }}">login</a>
                        atau <a href="{{ route('register') }}">daftar</a> terlebih dahulu.
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-check-circle text-success"></i>
                        Semua layanan dilakukan oleh stylist profesional dan berpengalaman.
                    </p>
                </div>
            </div>
        </div>
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

.card-img-top {
    border-top-left-radius: calc(0.25rem - 1px);
    border-top-right-radius: calc(0.25rem - 1px);
}

.input-group-text {
    background-color: #f8f9fa;
}

.form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
}
</style>
@endsection
