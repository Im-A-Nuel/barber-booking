<?php

namespace Tests\Feature;

use App\Booking;
use App\Service;
use App\Stylist;
use App\User;
use App\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class BookingManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->customer = User::create([
            'name' => 'Customer Test',
            'username' => 'customertest',
            'email' => 'customer@test.com',
            'role' => 'customer',
            'password' => bcrypt('password'),
        ]);

        $this->admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admintest',
            'email' => 'admin@test.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $stylistUser = User::create([
            'name' => 'Stylist Test',
            'username' => 'stylisttest',
            'email' => 'stylist@test.com',
            'role' => 'stylist',
            'password' => bcrypt('password'),
        ]);

        // Create test service
        $this->service = Service::create([
            'name' => 'Haircut',
            'duration_minutes' => 30,
            'price' => 50000,
            'is_active' => true,
        ]);

        // Create test stylist
        $this->stylist = Stylist::create([
            'user_id' => $stylistUser->id,
            'specialty' => 'Hair Cutting',
            'bio' => 'Expert stylist',
            'is_active' => true,
        ]);

        // Create schedule for stylist (Monday 09:00-17:00)
        $this->schedule = Schedule::create([
            'stylist_id' => $this->stylist->id,
            'day_of_week' => 1, // Monday
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function customer_can_view_bookings_list()
    {
        $booking = Booking::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => Booking::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->customer)->get('/bookings');

        $response->assertStatus(200);
        $response->assertSee($this->service->name);
    }

    /** @test */
    public function customer_can_view_create_booking_page()
    {
        $response = $this->actingAs($this->customer)->get('/bookings/create');

        $response->assertStatus(200);
        $response->assertSee($this->service->name);
    }

    /** @test */
    public function customer_can_select_stylist()
    {
        $response = $this->actingAs($this->customer)->get('/bookings/select-stylist?service_id=' . $this->service->id);

        $response->assertStatus(200);
        $response->assertSee($this->stylist->user->name);
    }

    /** @test */
    public function customer_can_view_datetime_selection()
    {
        $response = $this->actingAs($this->customer)->get('/bookings/select-datetime?service_id=' . $this->service->id . '&stylist_id=' . $this->stylist->id);

        $response->assertStatus(200);
        $response->assertSee('Pilih Tanggal & Waktu');
    }

    /** @test */
    public function customer_can_get_available_slots()
    {
        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->format('Y-m-d');

        $response = $this->actingAs($this->customer)->get('/bookings/available-slots', [
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist->id,
            'date' => $nextMonday,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure(['success', 'slots']);
    }

    /** @test */
    public function customer_can_create_booking()
    {
        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->format('Y-m-d');

        $response = $this->actingAs($this->customer)->post('/bookings', [
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist->id,
            'booking_date' => $nextMonday,
            'start_time' => '10:00',
            'end_time' => '10:30',
            'notes' => 'Test booking',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist->id,
            'booking_date' => $nextMonday,
            'status' => Booking::STATUS_PENDING,
        ]);
    }

    /** @test */
    public function customer_can_view_booking_details()
    {
        $booking = Booking::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => Booking::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->customer)->get('/bookings/' . $booking->id);

        $response->assertStatus(200);
        $response->assertSee($this->service->name);
        $response->assertSee($this->stylist->user->name);
    }

    /** @test */
    public function customer_can_cancel_pending_booking()
    {
        $booking = Booking::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => Booking::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->customer)->post('/bookings/' . $booking->id . '/cancel');

        $response->assertRedirect(route('bookings.index'));
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => Booking::STATUS_CANCELLED,
        ]);
    }

    /** @test */
    public function customer_cannot_cancel_completed_booking()
    {
        $booking = Booking::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist->id,
            'booking_date' => Carbon::yesterday()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => Booking::STATUS_COMPLETED,
        ]);

        $response = $this->actingAs($this->customer)->post('/bookings/' . $booking->id . '/cancel');

        $response->assertRedirect(route('bookings.show', $booking));
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => Booking::STATUS_COMPLETED,
        ]);
    }

    /** @test */
    public function customer_cannot_view_other_customers_bookings()
    {
        $otherCustomer = User::create([
            'name' => 'Other Customer',
            'username' => 'othercustomer',
            'email' => 'other@test.com',
            'role' => 'customer',
            'password' => bcrypt('password'),
        ]);

        $booking = Booking::create([
            'customer_id' => $otherCustomer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => Booking::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->customer)->get('/bookings/' . $booking->id);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_cannot_access_customer_booking_routes()
    {
        $response = $this->actingAs($this->admin)->get('/bookings');

        $response->assertStatus(403);
    }

    /** @test */
    public function guest_cannot_access_bookings()
    {
        $response = $this->get('/bookings');

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function cannot_book_on_day_without_schedule()
    {
        // Tuesday - no schedule for this day
        $nextTuesday = Carbon::now()->next(Carbon::TUESDAY)->format('Y-m-d');

        $response = $this->actingAs($this->customer)->post('/bookings', [
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist->id,
            'booking_date' => $nextTuesday,
            'start_time' => '10:00',
            'end_time' => '10:30',
        ]);

        $response->assertSessionHasErrors('booking_date');
    }

    /** @test */
    public function cannot_book_overlapping_time_slot()
    {
        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->format('Y-m-d');

        // Create existing booking
        Booking::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist->id,
            'booking_date' => $nextMonday,
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => Booking::STATUS_CONFIRMED,
        ]);

        // Try to book overlapping time
        $response = $this->actingAs($this->customer)->post('/bookings', [
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist->id,
            'booking_date' => $nextMonday,
            'start_time' => '10:15',
            'end_time' => '10:45',
        ]);

        $response->assertSessionHasErrors('end_time');
    }
}
