@extends('layouts.app')

@section('title', 'Buat Booking - Pilih Tanggal & Waktu')

@section('content')
    <div class="mb-3">
        <h1 class="h3"><i class="fas fa-calendar-plus"></i> Buat Booking Baru</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">1. Pilih Layanan</li>
                <li class="breadcrumb-item">2. Pilih Stylist</li>
                <li class="breadcrumb-item active">3. Pilih Tanggal & Waktu</li>
            </ol>
        </nav>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <div class="alert alert-info">
                <strong>Layanan:</strong> {{ $service->name }}<br>
                <strong>Durasi:</strong> {{ $service->duration_minutes }} menit<br>
                <strong>Harga:</strong> Rp {{ number_format($service->price, 0, ',', '.') }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="alert alert-info">
                <strong>Stylist:</strong> {{ $stylist->user->name }}<br>
                <strong>Spesialisasi:</strong> {{ $stylist->specialty }}
            </div>
        </div>
    </div>

    <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
        @csrf
        <input type="hidden" name="service_id" value="{{ $service->id }}">
        <input type="hidden" name="stylist_id" value="{{ $stylist->id }}">

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="booking_date">Tanggal Booking <span class="text-danger">*</span></label>
                    <input
                        type="date"
                        name="booking_date"
                        id="booking_date"
                        class="form-control @error('booking_date') is-invalid @enderror"
                        value="{{ old('booking_date') }}"
                        min="{{ date('Y-m-d') }}"
                        required
                    >
                    @error('booking_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div id="timeSlotsContainer" style="display: none;">
            <div class="form-group">
                <label>Pilih Waktu <span class="text-danger">*</span></label>
                <div id="timeSlots" class="row"></div>
                @error('start_time')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                @error('end_time')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="notes">Catatan (Opsional)</label>
                <textarea
                    name="notes"
                    id="notes"
                    class="form-control @error('notes') is-invalid @enderror"
                    rows="3"
                    placeholder="Tambahkan catatan untuk stylist..."
                >{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('bookings.select-stylist') }}?service_id={{ $service->id }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                    <i class="fas fa-check"></i> Buat Booking
                </button>
            </div>
        </div>

        <div id="loadingMessage" style="display: none;">
            <div class="alert alert-info">
                <i class="fas fa-spinner fa-spin"></i> Memuat jadwal yang tersedia...
            </div>
        </div>

        <div id="noSlotsMessage" style="display: none;">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Tidak ada waktu yang tersedia untuk tanggal ini. Silakan pilih tanggal lain.
            </div>
        </div>

        <input type="hidden" name="start_time" id="start_time">
        <input type="hidden" name="end_time" id="end_time">
    </form>
@endsection

@section('scripts')
<script>
    const serviceId = {{ $service->id }};
    const stylistId = {{ $stylist->id }};
    const dateInput = document.getElementById('booking_date');
    const timeSlotsContainer = document.getElementById('timeSlotsContainer');
    const timeSlotsDiv = document.getElementById('timeSlots');
    const loadingMessage = document.getElementById('loadingMessage');
    const noSlotsMessage = document.getElementById('noSlotsMessage');
    const submitBtn = document.getElementById('submitBtn');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');

    dateInput.addEventListener('change', function() {
        const selectedDate = this.value;
        if (!selectedDate) return;

        // Show loading
        timeSlotsContainer.style.display = 'none';
        noSlotsMessage.style.display = 'none';
        loadingMessage.style.display = 'block';
        submitBtn.disabled = true;

        // Fetch available slots
        fetch(`{{ route('bookings.available-slots') }}?service_id=${serviceId}&stylist_id=${stylistId}&date=${selectedDate}`)
            .then(response => response.json())
            .then(data => {
                loadingMessage.style.display = 'none';

                if (data.success && data.slots.length > 0) {
                    // Display time slots
                    timeSlotsDiv.innerHTML = '';
                    data.slots.forEach((slot, index) => {
                        const slotHtml = `
                            <div class="col-md-4 col-sm-6 mb-2">
                                <div class="card time-slot-card">
                                    <div class="card-body p-2">
                                        <div class="custom-control custom-radio">
                                            <input
                                                type="radio"
                                                id="slot${index}"
                                                name="time_slot"
                                                class="custom-control-input"
                                                value="${slot.start_time}|${slot.end_time}"
                                                required
                                            >
                                            <label class="custom-control-label w-100" for="slot${index}">
                                                <i class="fas fa-clock"></i> ${slot.display}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        timeSlotsDiv.innerHTML += slotHtml;
                    });

                    // Add event listeners to time slot radios
                    document.querySelectorAll('input[name="time_slot"]').forEach(radio => {
                        radio.addEventListener('change', function() {
                            const [start, end] = this.value.split('|');
                            startTimeInput.value = start;
                            endTimeInput.value = end;
                            submitBtn.disabled = false;

                            // Highlight selected card
                            document.querySelectorAll('.time-slot-card').forEach(card => {
                                card.classList.remove('border-primary');
                            });
                            this.closest('.time-slot-card').classList.add('border-primary');
                        });
                    });

                    timeSlotsContainer.style.display = 'block';
                } else {
                    noSlotsMessage.style.display = 'block';
                }
            })
            .catch(error => {
                loadingMessage.style.display = 'none';
                alert('Terjadi kesalahan saat memuat jadwal. Silakan coba lagi.');
                console.error('Error:', error);
            });
    });
</script>

<style>
    .time-slot-card {
        cursor: pointer;
        transition: all 0.2s;
    }
    .time-slot-card:hover {
        border-color: #007bff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .time-slot-card.border-primary {
        border-width: 2px;
    }
</style>
@endsection
