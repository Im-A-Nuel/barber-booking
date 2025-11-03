@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-group">
    <label for="user_id">User <span class="text-danger">*</span></label>
    @if ($stylist)
        <input type="text" class="form-control" value="{{ $stylist->user->name }} ({{ $stylist->user->email }})" disabled>
        <input type="hidden" name="user_id" value="{{ $stylist->user_id }}">
        <small class="form-text text-muted">User tidak dapat diubah setelah stylist dibuat.</small>
    @else
        <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
            <option value="">-- Pilih User --</option>
            @foreach ($availableUsers as $user)
                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }} ({{ $user->email }})
                </option>
            @endforeach
        </select>
        @error('user_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">Hanya user dengan role 'stylist' yang dapat dipilih.</small>
    @endif
</div>

<div class="form-group">
    <label for="specialty">Spesialisasi</label>
    <input
        type="text"
        name="specialty"
        id="specialty"
        class="form-control @error('specialty') is-invalid @enderror"
        value="{{ old('specialty', $stylist->specialty ?? '') }}"
        placeholder="Contoh: Haircut, Beard Trim, Coloring"
    >
    @error('specialty')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="bio">Bio</label>
    <textarea
        name="bio"
        id="bio"
        class="form-control @error('bio') is-invalid @enderror"
        rows="4"
        placeholder="Ceritakan tentang pengalaman dan keahlian stylist..."
    >{{ old('bio', $stylist->bio ?? '') }}</textarea>
    @error('bio')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <div class="form-check">
        <input
            type="hidden"
            name="is_active"
            value="0"
        >
        <input
            type="checkbox"
            name="is_active"
            id="is_active"
            class="form-check-input @error('is_active') is-invalid @enderror"
            value="1"
            {{ old('is_active', $stylist->is_active ?? true) ? 'checked' : '' }}
        >
        <label class="form-check-label" for="is_active">
            Aktif
        </label>
        @error('is_active')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <small class="form-text text-muted">Stylist yang non-aktif tidak akan muncul di daftar booking.</small>
</div>
