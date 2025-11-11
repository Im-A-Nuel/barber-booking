@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Validasi Error:</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-group">
    <label for="name">Nama <span class="text-danger">*</span></label>
    <input
        type="text"
        name="name"
        id="name"
        class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', $user->name ?? '') }}"
        placeholder="Masukkan nama lengkap"
        required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="username">Username <span class="text-danger">*</span></label>
    <input
        type="text"
        name="username"
        id="username"
        class="form-control @error('username') is-invalid @enderror"
        value="{{ old('username', $user->username ?? '') }}"
        placeholder="Contoh: john_stylist"
        required>
    @error('username')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">Username hanya boleh berisi huruf, angka, dash, dan underscore.</small>
</div>

<div class="form-group">
    <label for="email">Email <span class="text-danger">*</span></label>
    <input
        type="email"
        name="email"
        id="email"
        class="form-control @error('email') is-invalid @enderror"
        value="{{ old('email', $user->email ?? '') }}"
        placeholder="contoh@email.com"
        required>
    @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="image">Foto Profil</label>
    @if(isset($user) && $user->image)
        <div class="mb-2">
            <img src="{{ asset('storage/' . $user->image) }}" alt="User Image" class="img-thumbnail" style="max-width: 200px;">
        </div>
    @endif
    <input
        type="file"
        class="form-control-file @error('image') is-invalid @enderror"
        id="image"
        name="image"
        accept="image/*"
    >
    <small class="form-text text-muted">Format: JPG, PNG, JPEG. Maksimal 2MB.</small>
    @error('image')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="role">Role <span class="text-danger">*</span></label>
    <select
        name="role"
        id="role"
        class="form-control @error('role') is-invalid @enderror"
        required>
        <option value="">-- Pilih Role --</option>
        @foreach($roleOptions as $value => $label)
            <option value="{{ $value }}" {{ old('role', $user->role ?? '') == $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
    @error('role')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">
        <strong>Customer:</strong> User biasa yang bisa buat booking<br>
        <strong>Stylist:</strong> User yang mengelola booking dan jadwal<br>
        <strong>Admin:</strong> User dengan akses penuh ke semua fitur
    </small>
</div>

<div class="form-group">
    <label for="password">
        Password
        @if($user ?? false)
            <span class="text-muted">(Kosongkan jika tidak ingin mengubah)</span>
        @else
            <span class="text-danger">*</span>
        @endif
    </label>
    <input
        type="password"
        name="password"
        id="password"
        class="form-control @error('password') is-invalid @enderror"
        placeholder="Masukkan password"
        {{ !($user ?? false) ? 'required' : '' }}>
    @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">Minimal 8 karakter.</small>
</div>

<div class="form-group">
    <label for="password_confirmation">Konfirmasi Password</label>
    <input
        type="password"
        name="password_confirmation"
        id="password_confirmation"
        class="form-control"
        placeholder="Ulangi password">
    <small class="form-text text-muted">Harus sama dengan password di atas.</small>
</div>
