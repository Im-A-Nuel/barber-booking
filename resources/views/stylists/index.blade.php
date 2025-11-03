@extends('layouts.app')

@section('title', 'Kelola Stylist')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0"><i class="fas fa-user-tie"></i> Kelola Stylist</h1>
        <a href="{{ route('stylists.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Stylist
        </a>
    </div>

    <form method="GET" action="{{ route('stylists.index') }}" class="form-inline mb-3">
        <div class="form-group mr-2">
            <label class="sr-only" for="search">Cari</label>
            <input
                type="search"
                name="search"
                id="search"
                class="form-control"
                placeholder="Cari stylist..."
                value="{{ $search }}"
            >
        </div>
        <button type="submit" class="btn btn-outline-secondary">
            <i class="fas fa-search"></i> Cari
        </button>
    </form>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Spesialisasi</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stylists as $stylist)
                    <tr>
                        <td>{{ $stylist->user->name }}</td>
                        <td>{{ $stylist->user->email }}</td>
                        <td>{{ $stylist->specialty ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $stylist->is_active ? 'success' : 'secondary' }}">
                                {{ $stylist->is_active ? 'Aktif' : 'Non Aktif' }}
                            </span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('stylists.edit', $stylist) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('stylists.destroy', $stylist) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus stylist ini?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada stylist.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $stylists->links() }}
@endsection
