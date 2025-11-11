@extends('layouts.app')

@section('title', 'Tambah Layanan')

@section('content')
    <div class="mb-3">
        <h1 class="h3"><i class="fas fa-plus"></i> Tambah Layanan</h1>
        <a href="{{ route('services.index') }}" class="btn btn-link px-0">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('services.store') }}" enctype="multipart/form-data">
        @csrf
        @include('services._form', ['service' => null])

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Simpan
        </button>
    </form>
@endsection
