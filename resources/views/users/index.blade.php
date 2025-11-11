@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3 mb-0"><i class="fas fa-users"></i> Kelola User</h1>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah User
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    {{-- Filters --}}
                    <form method="GET" action="{{ route('users.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="search">Cari User</label>
                                    <input
                                        type="text"
                                        name="search"
                                        id="search"
                                        class="form-control"
                                        placeholder="Nama, email, atau username..."
                                        value="{{ $search }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="role">Filter by Role</label>
                                    <select name="role" id="role" class="form-control">
                                        <option value="">Semua Role</option>
                                        @foreach($roleOptions as $value => $label)
                                            <option value="{{ $value }}" {{ $roleFilter == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i> Cari
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if($users->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Tidak ada user ditemukan.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Foto</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>
                                                @if($user->image)
                                                    <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->name }}" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $user->name }}</strong>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <code>{{ $user->username }}</code>
                                            </td>
                                            <td>
                                                @if($user->isAdmin())
                                                    <span class="badge badge-danger">Admin</span>
                                                @elseif($user->isStylist())
                                                    <span class="badge badge-warning">Stylist</span>
                                                @else
                                                    <span class="badge badge-info">Customer</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $user->created_at->format('d M Y H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    @if($user->id !== auth()->id())
                                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning mr-1" title="Edit">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                                <i class="fas fa-trash"></i> Hapus
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted">
                                                            <small>(Akun Anda sendiri)</small>
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $users->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
