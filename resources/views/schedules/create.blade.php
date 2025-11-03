@extends('layouts.app')

@section('title', 'Tambah Jadwal')

@section('content')
    <div class="mb-3">
        <h1 class="h3"><i class="fas fa-plus"></i> Tambah Jadwal</h1>
        <a href="{{ route('schedules.index') }}" class="btn btn-link px-0">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('schedules.store') }}">
        @csrf
        @include('schedules._form', ['schedule' => null])

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Simpan
        </button>
    </form>
@endsection
