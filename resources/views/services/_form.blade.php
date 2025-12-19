<div class="form-group">
    <label for="name">Nama Layanan</label>
    <input
        type="text"
        class="form-control @error('name') is-invalid @enderror"
        id="name"
        name="name"
        value="{{ old('name', optional($service)->name) }}"
        required
    >
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="duration_minutes">Durasi (menit)</label>
    <input
        type="number"
        class="form-control @error('duration_minutes') is-invalid @enderror"
        id="duration_minutes"
        name="duration_minutes"
        min="10"
        max="480"
        step="5"
        value="{{ old('duration_minutes', optional($service)->duration_minutes ?? 30) }}"
        required
    >
    @error('duration_minutes')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="price">Harga (Rupiah)</label>
    <input
        type="number"
        class="form-control @error('price') is-invalid @enderror"
        id="price"
        name="price"
        min="0"
        step="5000"
        value="{{ old('price', optional($service)->price ?? 0) }}"
        required
    >
    @error('price')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="image">Gambar Layanan</label>
    @if(isset($service) && $service->image)
        <div class="mb-2">
            <img src="{{ asset('storage/' . $service->image) }}" alt="Service Image" class="img-thumbnail" style="max-width: 200px;">
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

<div class="form-group form-check">
    <input type="hidden" name="is_active" value="0">
    <input
        type="checkbox"
        class="form-check-input @error('is_active') is-invalid @enderror"
        id="is_active"
        name="is_active"
        value="1"
        {{ old('is_active', optional($service)->is_active ?? true) ? 'checked' : '' }}
    >
    <label class="form-check-label" for="is_active">Aktif</label>
    @error('is_active')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
