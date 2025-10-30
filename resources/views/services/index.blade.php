@extends('layouts.app')

@section('title', 'Kelola Layanan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Kelola Layanan</h1>
        <a href="{{ route('services.create') }}" class="btn btn-primary">Tambah Layanan</a>
    </div>

    <form method="GET" action="{{ route('services.index') }}" class="form-inline mb-3">
        <div class="form-group mr-2">
            <label class="sr-only" for="search">Cari</label>
            <input
                type="search"
                name="search"
                id="search"
                class="form-control"
                placeholder="Cari layanan..."
                value="{{ $search }}"
            >
        </div>
        <button type="submit" class="btn btn-outline-secondary">Cari</button>
    </form>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Durasi</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($services as $service)
                    <tr>
                        <td>{{ $service->name }}</td>
                        <td>{{ $service->duration_minutes }} menit</td>
                        <td>Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge badge-{{ $service->is_active ? 'success' : 'secondary' }}">
                                {{ $service->is_active ? 'Aktif' : 'Non Aktif' }}
                            </span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('services.edit', $service) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('services.destroy', $service) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus layanan ini?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada layanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $services->links() }}
@endsection
