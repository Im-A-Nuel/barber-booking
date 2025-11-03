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
    <label for="stylist_id">Stylist <span class="text-danger">*</span></label>
    @if ($schedule)
        <input type="text" class="form-control" value="{{ $schedule->stylist->user->name }}" disabled>
        <input type="hidden" name="stylist_id" value="{{ $schedule->stylist_id }}">
        <small class="form-text text-muted">Stylist tidak dapat diubah setelah jadwal dibuat.</small>
    @else
        <select name="stylist_id" id="stylist_id" class="form-control @error('stylist_id') is-invalid @enderror" required>
            <option value="">-- Pilih Stylist --</option>
            @foreach ($stylists as $stylist)
                <option value="{{ $stylist->id }}" {{ old('stylist_id') == $stylist->id ? 'selected' : '' }}>
                    {{ $stylist->user->name }}
                </option>
            @endforeach
        </select>
        @error('stylist_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    @endif
</div>

<div class="form-group">
    <label for="day_of_week">Hari <span class="text-danger">*</span></label>
    @if ($schedule)
        <input type="text" class="form-control" value="{{ $schedule->day_name }}" disabled>
        <input type="hidden" name="day_of_week" value="{{ $schedule->day_of_week }}">
        <small class="form-text text-muted">Hari tidak dapat diubah setelah jadwal dibuat.</small>
    @else
        <select name="day_of_week" id="day_of_week" class="form-control @error('day_of_week') is-invalid @enderror" required>
            <option value="">-- Pilih Hari --</option>
            @foreach ($dayOptions as $value => $label)
                <option value="{{ $value }}" {{ old('day_of_week') == $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('day_of_week')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    @endif
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="start_time">Jam Mulai <span class="text-danger">*</span></label>
        <input
            type="time"
            name="start_time"
            id="start_time"
            class="form-control @error('start_time') is-invalid @enderror"
            value="{{ old('start_time', $schedule ? substr($schedule->start_time, 0, 5) : '') }}"
            required
        >
        @error('start_time')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">Format: 08:00</small>
    </div>

    <div class="form-group col-md-6">
        <label for="end_time">Jam Selesai <span class="text-danger">*</span></label>
        <input
            type="time"
            name="end_time"
            id="end_time"
            class="form-control @error('end_time') is-invalid @enderror"
            value="{{ old('end_time', $schedule ? substr($schedule->end_time, 0, 5) : '') }}"
            required
        >
        @error('end_time')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">Format: 17:00</small>
    </div>
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
            {{ old('is_active', $schedule ? $schedule->is_active : true) ? 'checked' : '' }}
        >
        <label class="form-check-label" for="is_active">
            Aktif
        </label>
        @error('is_active')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <small class="form-text text-muted">Jadwal yang non-aktif tidak akan tersedia untuk booking.</small>
</div>
