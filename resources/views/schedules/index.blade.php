@extends('layouts.app')

@section('title', 'Kelola Jadwal')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0"><i class="fas fa-calendar-alt"></i> Kelola Jadwal</h1>
        <a href="{{ route('schedules.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Jadwal
        </a>
    </div>

    <form method="GET" action="{{ route('schedules.index') }}" class="form-inline mb-3">
        <div class="form-group mr-2">
            <label class="sr-only" for="stylist_id">Filter Stylist</label>
            <select name="stylist_id" id="stylist_id" class="form-control">
                <option value="">-- Semua Stylist --</option>
                @foreach ($stylists as $stylist)
                    <option value="{{ $stylist->id }}" {{ $stylistId == $stylist->id ? 'selected' : '' }}>
                        {{ $stylist->user->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-outline-secondary">
            <i class="fas fa-filter"></i> Filter
        </button>
        @if($stylistId)
            <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary ml-2">
                <i class="fas fa-times"></i> Reset
            </a>
        @endif
    </form>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Stylist</th>
                    <th>Hari</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($schedules as $schedule)
                    <tr>
                        <td>{{ $schedule->stylist->user->name }}</td>
                        <td>{{ $schedule->day_name }}</td>
                        <td>{{ substr($schedule->start_time, 0, 5) }}</td>
                        <td>{{ substr($schedule->end_time, 0, 5) }}</td>
                        <td>
                            <span class="badge badge-{{ $schedule->is_active ? 'success' : 'secondary' }}">
                                {{ $schedule->is_active ? 'Aktif' : 'Non Aktif' }}
                            </span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('schedules.destroy', $schedule) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus jadwal ini?');">
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
                        <td colspan="6" class="text-center">Belum ada jadwal.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $schedules->links() }}
@endsection
