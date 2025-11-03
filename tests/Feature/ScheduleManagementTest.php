<?php

namespace Tests\Feature;

use App\Schedule;
use App\Stylist;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user for testing
        $this->admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admintest',
            'email' => 'admin@test.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        // Create a stylist user and profile for testing
        $stylistUser = User::create([
            'name' => 'Stylist Test',
            'username' => 'stylisttest',
            'email' => 'stylist@test.com',
            'role' => 'stylist',
            'password' => bcrypt('password'),
        ]);

        $this->stylist = Stylist::create([
            'user_id' => $stylistUser->id,
            'specialty' => 'Haircut',
            'bio' => 'Test bio',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_displays_schedule_list()
    {
        $schedule = Schedule::create([
            'stylist_id' => $this->stylist->id,
            'day_of_week' => Schedule::MONDAY,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get('/schedules');

        $response->assertStatus(200);
        $response->assertSee($this->stylist->user->name);
        $response->assertSee('Senin');
    }

    /** @test */
    public function it_creates_a_schedule()
    {
        $response = $this->actingAs($this->admin)->post('/schedules', [
            'stylist_id' => $this->stylist->id,
            'day_of_week' => Schedule::TUESDAY,
            'start_time' => '08:00',
            'end_time' => '16:00',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('schedules.index'));
        $this->assertDatabaseHas('schedules', [
            'stylist_id' => $this->stylist->id,
            'day_of_week' => Schedule::TUESDAY,
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->actingAs($this->admin)->post('/schedules', [
            'stylist_id' => '',
            'day_of_week' => '',
            'start_time' => '',
            'end_time' => '',
        ]);

        $response->assertSessionHasErrors(['stylist_id', 'day_of_week', 'start_time', 'end_time']);
    }

    /** @test */
    public function it_validates_end_time_must_be_after_start_time()
    {
        $response = $this->actingAs($this->admin)->post('/schedules', [
            'stylist_id' => $this->stylist->id,
            'day_of_week' => Schedule::MONDAY,
            'start_time' => '17:00',
            'end_time' => '09:00',
            'is_active' => 1,
        ]);

        $response->assertSessionHasErrors('end_time');
    }

    /** @test */
    public function it_validates_unique_stylist_and_day_combination()
    {
        // Create first schedule
        Schedule::create([
            'stylist_id' => $this->stylist->id,
            'day_of_week' => Schedule::WEDNESDAY,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_active' => true,
        ]);

        // Try to create another schedule for same stylist and day
        $response = $this->actingAs($this->admin)->post('/schedules', [
            'stylist_id' => $this->stylist->id,
            'day_of_week' => Schedule::WEDNESDAY,
            'start_time' => '10:00',
            'end_time' => '18:00',
            'is_active' => 1,
        ]);

        $response->assertSessionHasErrors('day_of_week');
    }

    /** @test */
    public function it_updates_a_schedule()
    {
        $schedule = Schedule::create([
            'stylist_id' => $this->stylist->id,
            'day_of_week' => Schedule::THURSDAY,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put("/schedules/{$schedule->id}", [
            'stylist_id' => $this->stylist->id,
            'day_of_week' => Schedule::THURSDAY,
            'start_time' => '08:00',
            'end_time' => '16:00',
            'is_active' => 0,
        ]);

        $response->assertRedirect(route('schedules.index'));
        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
            'is_active' => false,
        ]);
    }

    /** @test */
    public function it_deletes_a_schedule()
    {
        $schedule = Schedule::create([
            'stylist_id' => $this->stylist->id,
            'day_of_week' => Schedule::FRIDAY,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete("/schedules/{$schedule->id}");

        $response->assertRedirect(route('schedules.index'));
        $this->assertDatabaseMissing('schedules', [
            'id' => $schedule->id,
        ]);
    }

    /** @test */
    public function non_admin_cannot_access_schedules()
    {
        $customer = User::create([
            'name' => 'Customer Test',
            'username' => 'customertest',
            'email' => 'customer@test.com',
            'role' => 'customer',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($customer)->get('/schedules');

        $response->assertStatus(403);
    }

    /** @test */
    public function guest_cannot_access_schedules()
    {
        $response = $this->get('/schedules');

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_can_filter_schedules_by_stylist()
    {
        // Create another stylist
        $anotherStylistUser = User::create([
            'name' => 'Another Stylist',
            'username' => 'anotherstylist',
            'email' => 'another@test.com',
            'role' => 'stylist',
            'password' => bcrypt('password'),
        ]);

        $anotherStylist = Stylist::create([
            'user_id' => $anotherStylistUser->id,
            'specialty' => 'Coloring',
            'is_active' => true,
        ]);

        // Create schedules for both stylists
        Schedule::create([
            'stylist_id' => $this->stylist->id,
            'day_of_week' => Schedule::MONDAY,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_active' => true,
        ]);

        Schedule::create([
            'stylist_id' => $anotherStylist->id,
            'day_of_week' => Schedule::MONDAY,
            'start_time' => '10:00',
            'end_time' => '18:00',
            'is_active' => true,
        ]);

        // Filter by first stylist
        $response = $this->actingAs($this->admin)->get('/schedules?stylist_id=' . $this->stylist->id);

        $response->assertStatus(200);
        $response->assertSee($this->stylist->user->name);
        $response->assertDontSee($anotherStylist->user->name);
    }
}
