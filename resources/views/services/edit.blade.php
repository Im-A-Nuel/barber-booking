@extends('layouts.app')

@section('title', 'Edit Layanan')

@section('content')
    <div class="mb-3">
        <h1 class="h3"><i class="fas fa-edit"></i> Edit Layanan</h1>
        <a href="{{ route('services.index') }}" class="btn btn-link px-0">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('services.update', $service) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('services._form', ['service' => $service])

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Perbarui
        </button>
    </form>
@endsection
