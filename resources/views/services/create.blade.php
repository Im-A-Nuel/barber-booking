@extends('layouts.app')

@section('title', 'Tambah Layanan')

@section('content')
    <div class="mb-3">
        <h1 class="h3">Tambah Layanan</h1>
        <a href="{{ route('services.index') }}" class="btn btn-link px-0">&larr; Kembali</a>
    </div>

    <form method="POST" action="{{ route('services.store') }}">
        @csrf
        @include('services._form', ['service' => null])

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
@endsection
