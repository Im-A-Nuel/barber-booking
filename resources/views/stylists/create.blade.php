@extends('layouts.app')

@section('title', 'Tambah Stylist')

@section('content')
    <div class="mb-3">
        <h1 class="h3"><i class="fas fa-plus"></i> Tambah Stylist</h1>
        <a href="{{ route('stylists.index') }}" class="btn btn-link px-0">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('stylists.store') }}">
        @csrf
        @include('stylists._form', ['stylist' => null])

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Simpan
        </button>
    </form>
@endsection
