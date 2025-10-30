@extends('layouts.app')

@section('title', 'Edit Layanan')

@section('content')
    <div class="mb-3">
        <h1 class="h3">Edit Layanan</h1>
        <a href="{{ route('services.index') }}" class="btn btn-link px-0">&larr; Kembali</a>
    </div>

    <form method="POST" action="{{ route('services.update', $service) }}">
        @csrf
        @method('PUT')
        @include('services._form', ['service' => $service])

        <button type="submit" class="btn btn-primary">Perbarui</button>
    </form>
@endsection
