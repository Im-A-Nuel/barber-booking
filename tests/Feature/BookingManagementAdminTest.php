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

class BookingManagementAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admintest',
            'email' => 'admin@test.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $stylistUser1 = User::create([
            'name' => 'Stylist One',
            'username' => 'stylist1',
            'email' => 'stylist1@test.com',
            'role' => 'stylist',
            'password' => bcrypt('password'),
        ]);

        $stylistUser2 = User::create([
            'name' => 'Stylist Two',
            'username' => 'stylist2',
            'email' => 'stylist2@test.com',
            'role' => 'stylist',
            'password' => bcrypt('password'),
        ]);

        $this->customer = User::create([
            'name' => 'Customer Test',
            'username' => 'customertest',
            'email' => 'customer@test.com',
            'role' => 'customer',
            'password' => bcrypt('password'),
        ]);

        // Create test service
        $this->service = Service::create([
            'name' => 'Haircut',
            'duration_minutes' => 30,
            'price' => 50000,
            'is_active' => true,
        ]);

        // Create test stylists
        $this->stylist1 = Stylist::create([
            'user_id' => $stylistUser1->id,
            'specialty' => 'Hair Cutting',
            'bio' => 'Expert stylist',
            'is_active' => true,
        ]);

        $this->stylist2 = Stylist::create([
            'user_id' => $stylistUser2->id,
            'specialty' => 'Hair Coloring',
            'bio' => 'Color expert',
            'is_active' => true,
        ]);

        // Create schedules
        foreach ([$this->stylist1, $this->stylist2] as $stylist) {
            Schedule::create([
                'stylist_id' => $stylist->id,
                'day_of_week' => 1, // Monday
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'is_active' => true,
            ]);
        }
    }

    /** @test */
    public function admin_can_view_all_bookings()
    {
        // Create bookings for different stylists
        Booking::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist1->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => Booking::STATUS_PENDING,
        ]);

        Booking::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist2->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '11:00:00',
            'end_time' => '11:30:00',
            'status' => Booking::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/bookings');

        $response->assertStatus(200);
        $response->assertSee('Stylist One');
        $response->assertSee('Stylist Two');
    }

    /** @test */
    public function stylist_can_only_view_their_own_bookings()
    {
        // Create booking for stylist1
        Booking::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist1->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => Booking::STATUS_PENDING,
        ]);

        // Create booking for stylist2
        Booking::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist2->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '11:00:00',
            'end_time' => '11:30:00',
            'status' => Booking::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->stylist1->user)->get('/admin/bookings');

        $response->assertStatus(200);
        // Should see their own booking
        $response->assertSee('10:00');
        // Should NOT see other stylist's booking
        $response->assertDontSee('11:00');
    }

    /** @test */
    public function admin_can_confirm_pending_booking()
    {
        $booking = Booking::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist1->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => Booking::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->admin)->patch('/admin/bookings/' . $booking->id . '/confirm');

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => Booking::STATUS_CONFIRMED,
        ]);
    }

    /** @test */
    public function stylist_can_confirm_their_own_booking()
    {
        $booking = Booking::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist1->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => Booking::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->stylist1->user)->patch('/admin/bookings/' . $booking->id . '/confirm');

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => Booking::STATUS_CONFIRMED,
        ]);
    }

    /** @test */
    public function stylist_cannot_confirm_other_stylists_booking()
    {
        $booking = Booking::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist2->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => Booking::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->stylist1->user)->patch('/admin/bookings/' . $booking->id . '/confirm');

        $response->assertStatus(403);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => Booking::STATUS_PENDING,
        ]);
    }

    /** @test */
    public function admin_can_complete_confirmed_booking()
    {
        $booking = Booking::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist1->id,
            'booking_date' => Carbon::yesterday()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => Booking::STATUS_CONFIRMED,
        ]);

        $response = $this->actingAs($this->admin)->patch('/admin/bookings/' . $booking->id . '/complete');

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => Booking::STATUS_COMPLETED,
        ]);
    }

    /** @test */
    public function stylist_can_complete_their_own_booking()
    {
        $booking = Booking::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist1->id,
            'booking_date' => Carbon::yesterday()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => Booking::STATUS_CONFIRMED,
        ]);

        $response = $this->actingAs($this->stylist1->user)->patch('/admin/bookings/' . $booking->id . '/complete');

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => Booking::STATUS_COMPLETED,
        ]);
    }

    /** @test */
    public function admin_can_cancel_booking()
    {
        $booking = Booking::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist1->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => Booking::STATUS_CONFIRMED,
        ]);

        $response = $this->actingAs($this->admin)->patch('/admin/bookings/' . $booking->id . '/cancel');

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => Booking::STATUS_CANCELLED,
        ]);
    }

    /** @test */
    public function stylist_can_cancel_their_own_booking()
    {
        $booking = Booking::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist1->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => Booking::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->stylist1->user)->patch('/admin/bookings/' . $booking->id . '/cancel');

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => Booking::STATUS_CANCELLED,
        ]);
    }

    /** @test */
    public function cannot_confirm_non_pending_booking()
    {
        $booking = Booking::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist1->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => Booking::STATUS_CONFIRMED,
        ]);

        $response = $this->actingAs($this->admin)->patch('/admin/bookings/' . $booking->id . '/confirm');

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function cannot_complete_non_confirmed_booking()
    {
        $booking = Booking::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist1->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => Booking::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->admin)->patch('/admin/bookings/' . $booking->id . '/complete');

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function cannot_cancel_completed_booking()
    {
        $booking = Booking::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist1->id,
            'booking_date' => Carbon::yesterday()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => Booking::STATUS_COMPLETED,
        ]);

        $response = $this->actingAs($this->admin)->patch('/admin/bookings/' . $booking->id . '/cancel');

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => Booking::STATUS_COMPLETED,
        ]);
    }

    /** @test */
    public function customer_cannot_access_admin_bookings()
    {
        $response = $this->actingAs($this->customer)->get('/admin/bookings');

        $response->assertStatus(403);
    }

    /** @test */
    public function guest_cannot_access_admin_bookings()
    {
        $response = $this->get('/admin/bookings');

        $response->assertRedirect('/login');
    }
}
