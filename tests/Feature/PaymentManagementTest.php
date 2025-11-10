<?php

namespace Tests\Feature;

use App\Booking;
use App\Payment;
use App\Service;
use App\Stylist;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class PaymentManagementTest extends TestCase
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

        // Create a confirmed booking
        $this->booking = Booking::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => 'confirmed',
            'notes' => 'Test booking',
        ]);
    }

    /** @test */
    public function customer_can_view_payment_form_for_their_booking()
    {
        $response = $this->actingAs($this->customer)
            ->get(route('payments.create', $this->booking->id));

        $response->assertStatus(200);
        $response->assertViewIs('payments.create');
        $response->assertViewHas('booking');
    }

    /** @test */
    public function customer_can_create_payment_for_their_booking()
    {
        $paymentData = [
            'amount' => 50000,
            'method' => 'cash',
            'status' => 'paid',
        ];

        $response = $this->actingAs($this->customer)
            ->post(route('payments.store', $this->booking->id), $paymentData);

        $response->assertRedirect(route('bookings.show', $this->booking->id));
        $this->assertDatabaseHas('payments', [
            'booking_id' => $this->booking->id,
            'amount' => 50000,
            'method' => 'cash',
            'status' => 'paid',
        ]);
    }

    /** @test */
    public function customer_cannot_create_payment_for_pending_booking()
    {
        $pendingBooking = Booking::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '11:00:00',
            'end_time' => '11:30:00',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->customer)
            ->get(route('payments.create', $pendingBooking->id));

        $response->assertRedirect(route('bookings.show', $pendingBooking->id));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function customer_cannot_create_duplicate_payment()
    {
        // Create first payment
        Payment::create([
            'booking_id' => $this->booking->id,
            'amount' => 50000,
            'method' => 'cash',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Try to create another payment
        $paymentData = [
            'amount' => 50000,
            'method' => 'transfer',
            'status' => 'paid',
        ];

        $response = $this->actingAs($this->customer)
            ->post(route('payments.store', $this->booking->id), $paymentData);

        $response->assertRedirect(route('bookings.show', $this->booking->id));
        $response->assertSessionHas('error');

        // Ensure only one payment exists
        $this->assertEquals(1, Payment::where('booking_id', $this->booking->id)->count());
    }

    /** @test */
    public function customer_can_view_their_payment_history()
    {
        // Create payment
        Payment::create([
            'booking_id' => $this->booking->id,
            'amount' => 50000,
            'method' => 'cash',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->customer)
            ->get(route('payments.index'));

        $response->assertStatus(200);
        $response->assertViewIs('payments.index');
        $response->assertSee('50.000');
    }

    /** @test */
    public function admin_can_view_all_payments()
    {
        // Create payment
        Payment::create([
            'booking_id' => $this->booking->id,
            'amount' => 50000,
            'method' => 'cash',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('payments.index'));

        $response->assertStatus(200);
        $response->assertViewIs('payments.index');
        $response->assertSee('50.000');
    }

    /** @test */
    public function payment_validation_requires_all_fields()
    {
        $response = $this->actingAs($this->customer)
            ->post(route('payments.store', $this->booking->id), []);

        $response->assertSessionHasErrors(['amount', 'method', 'status']);
    }

    /** @test */
    public function payment_method_must_be_valid()
    {
        $paymentData = [
            'amount' => 50000,
            'method' => 'invalid_method',
            'status' => 'paid',
        ];

        $response = $this->actingAs($this->customer)
            ->post(route('payments.store', $this->booking->id), $paymentData);

        $response->assertSessionHasErrors(['method']);
    }

    /** @test */
    public function customer_cannot_create_payment_for_other_customer_booking()
    {
        $otherCustomer = User::create([
            'name' => 'Other Customer',
            'username' => 'othercustomer',
            'email' => 'other@test.com',
            'role' => 'customer',
            'password' => bcrypt('password'),
        ]);

        $otherBooking = Booking::create([
            'customer_id' => $otherCustomer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '12:00:00',
            'end_time' => '12:30:00',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($this->customer)
            ->get(route('payments.create', $otherBooking->id));

        $response->assertStatus(403);
    }

    /** @test */
    public function payment_amount_must_equal_service_price()
    {
        $paymentData = [
            'amount' => 60000, // Wrong amount (service price is 50000)
            'method' => 'cash',
            'status' => 'paid',
        ];

        $response = $this->actingAs($this->customer)
            ->post(route('payments.store', $this->booking->id), $paymentData);

        $response->assertSessionHasErrors(['amount']);
    }

    /** @test */
    public function booking_status_auto_updated_when_payment_is_paid()
    {
        $this->assertEquals('confirmed', $this->booking->status);

        $paymentData = [
            'amount' => 50000,
            'method' => 'cash',
            'status' => 'paid',
        ];

        $response = $this->actingAs($this->customer)
            ->post(route('payments.store', $this->booking->id), $paymentData);

        $this->booking->refresh();
        $this->assertEquals('completed', $this->booking->status);
    }

    /** @test */
    public function customer_can_view_payment_receipt()
    {
        $payment = Payment::create([
            'booking_id' => $this->booking->id,
            'amount' => 50000,
            'method' => 'cash',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->customer)
            ->get(route('payments.receipt', $payment->id));

        $response->assertStatus(200);
        $response->assertViewIs('payments.receipt');
        $response->assertSee('BUKTI PEMBAYARAN');
    }

    /** @test */
    public function customer_cannot_view_other_customer_payment_receipt()
    {
        $otherCustomer = User::create([
            'name' => 'Other Customer 2',
            'username' => 'othercustomer2',
            'email' => 'other2@test.com',
            'role' => 'customer',
            'password' => bcrypt('password'),
        ]);

        $otherBooking = Booking::create([
            'customer_id' => $otherCustomer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '12:00:00',
            'end_time' => '12:30:00',
            'status' => 'confirmed',
        ]);

        $payment = Payment::create([
            'booking_id' => $otherBooking->id,
            'amount' => 50000,
            'method' => 'cash',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->customer)
            ->get(route('payments.receipt', $payment->id));

        $response->assertStatus(403);
    }

    /** @test */
    public function payment_list_can_be_filtered_by_status()
    {
        Payment::create([
            'booking_id' => $this->booking->id,
            'amount' => 50000,
            'method' => 'cash',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->customer)
            ->get(route('payments.index', ['status' => 'paid']));

        $response->assertStatus(200);
        $response->assertSee('Lunas');
    }

    /** @test */
    public function customer_can_update_pending_payment_to_paid()
    {
        // Create pending payment
        $payment = Payment::create([
            'booking_id' => $this->booking->id,
            'amount' => 50000,
            'method' => 'transfer',
            'status' => 'pending',
        ]);

        $this->assertEquals('pending', $payment->status);
        $this->assertNull($payment->paid_at);

        $updateData = [
            'status' => 'paid',
            'method' => 'transfer',
        ];

        $response = $this->actingAs($this->customer)
            ->put(route('payments.update', $payment->id), $updateData);

        $response->assertRedirect(route('payments.index'));

        $payment->refresh();
        $this->assertEquals('paid', $payment->status);
        $this->assertNotNull($payment->paid_at);
    }

    /** @test */
    public function booking_auto_completed_when_payment_updated_to_paid()
    {
        // Ensure booking is confirmed
        $this->booking->status = 'confirmed';
        $this->booking->save();

        // Create pending payment
        $payment = Payment::create([
            'booking_id' => $this->booking->id,
            'amount' => 50000,
            'method' => 'cash',
            'status' => 'pending',
        ]);

        $updateData = [
            'status' => 'paid',
            'method' => 'cash',
        ];

        $response = $this->actingAs($this->customer)
            ->put(route('payments.update', $payment->id), $updateData);

        $this->booking->refresh();
        $this->assertEquals('completed', $this->booking->status);
    }

    /** @test */
    public function customer_can_view_edit_payment_form()
    {
        $payment = Payment::create([
            'booking_id' => $this->booking->id,
            'amount' => 50000,
            'method' => 'cash',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->customer)
            ->get(route('payments.edit', $payment->id));

        $response->assertStatus(200);
        $response->assertViewIs('payments.edit');
        $response->assertSee('Update Status Pembayaran');
    }

    /** @test */
    public function customer_cannot_edit_other_customer_payment()
    {
        $otherCustomer = User::create([
            'name' => 'Other Customer 3',
            'username' => 'othercustomer3',
            'email' => 'other3@test.com',
            'role' => 'customer',
            'password' => bcrypt('password'),
        ]);

        $otherBooking = Booking::create([
            'customer_id' => $otherCustomer->id,
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '13:00:00',
            'end_time' => '13:30:00',
            'status' => 'confirmed',
        ]);

        $payment = Payment::create([
            'booking_id' => $otherBooking->id,
            'amount' => 50000,
            'method' => 'cash',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->customer)
            ->get(route('payments.edit', $payment->id));

        $response->assertStatus(403);
    }
}
