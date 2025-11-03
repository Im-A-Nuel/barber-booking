@extends('layouts.app')

@section('title', 'Edit Stylist')

@section('content')
    <div class="mb-3">
        <h1 class="h3"><i class="fas fa-edit"></i> Edit Stylist</h1>
        <a href="{{ route('stylists.index') }}" class="btn btn-link px-0">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('stylists.update', $stylist) }}">
        @csrf
        @method('PUT')
        @include('stylists._form', ['stylist' => $stylist, 'availableUsers' => []])

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Perbarui
        </button>
    </form>
@endsection
