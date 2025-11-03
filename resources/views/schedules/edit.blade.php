@extends('layouts.app')

@section('title', 'Edit Jadwal')

@section('content')
    <div class="mb-3">
        <h1 class="h3"><i class="fas fa-edit"></i> Edit Jadwal</h1>
        <a href="{{ route('schedules.index') }}" class="btn btn-link px-0">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('schedules.update', $schedule) }}">
        @csrf
        @method('PUT')
        @include('schedules._form', ['schedule' => $schedule, 'stylists' => [], 'dayOptions' => $dayOptions])

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Perbarui
        </button>
    </form>
@endsection
