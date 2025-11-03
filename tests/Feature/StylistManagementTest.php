<?php

namespace Tests\Feature;

use App\Stylist;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StylistManagementTest extends TestCase
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

        // Create a stylist user for testing
        $this->stylistUser = User::create([
            'name' => 'Stylist Test',
            'username' => 'stylisttest',
            'email' => 'stylist@test.com',
            'role' => 'stylist',
            'password' => bcrypt('password'),
        ]);
    }

    /** @test */
    public function it_displays_stylist_list()
    {
        $stylist = Stylist::create([
            'user_id' => $this->stylistUser->id,
            'specialty' => 'Haircut',
            'bio' => 'Test bio',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get('/stylists');

        $response->assertStatus(200);
        $response->assertSee($stylist->user->name);
        $response->assertSee($stylist->specialty);
    }

    /** @test */
    public function it_creates_a_stylist()
    {
        $response = $this->actingAs($this->admin)->post('/stylists', [
            'user_id' => $this->stylistUser->id,
            'specialty' => 'Beard Trim',
            'bio' => 'Expert in beard styling',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('stylists.index'));
        $this->assertDatabaseHas('stylists', [
            'user_id' => $this->stylistUser->id,
            'specialty' => 'Beard Trim',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_validates_user_id_is_required()
    {
        $response = $this->actingAs($this->admin)->post('/stylists', [
            'specialty' => 'Haircut',
            'is_active' => 1,
        ]);

        $response->assertSessionHasErrors('user_id');
    }

    /** @test */
    public function it_validates_user_id_is_unique()
    {
        // Create first stylist
        Stylist::create([
            'user_id' => $this->stylistUser->id,
            'specialty' => 'Haircut',
            'is_active' => true,
        ]);

        // Try to create another stylist with same user_id
        $response = $this->actingAs($this->admin)->post('/stylists', [
            'user_id' => $this->stylistUser->id,
            'specialty' => 'Beard Trim',
            'is_active' => 1,
        ]);

        $response->assertSessionHasErrors('user_id');
    }

    /** @test */
    public function it_updates_a_stylist()
    {
        $stylist = Stylist::create([
            'user_id' => $this->stylistUser->id,
            'specialty' => 'Haircut',
            'bio' => 'Original bio',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put("/stylists/{$stylist->id}", [
            'user_id' => $this->stylistUser->id,
            'specialty' => 'Haircut & Coloring',
            'bio' => 'Updated bio',
            'is_active' => 0,
        ]);

        $response->assertRedirect(route('stylists.index'));
        $this->assertDatabaseHas('stylists', [
            'id' => $stylist->id,
            'specialty' => 'Haircut & Coloring',
            'bio' => 'Updated bio',
            'is_active' => false,
        ]);
    }

    /** @test */
    public function it_deletes_a_stylist()
    {
        $stylist = Stylist::create([
            'user_id' => $this->stylistUser->id,
            'specialty' => 'Haircut',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete("/stylists/{$stylist->id}");

        $response->assertRedirect(route('stylists.index'));
        $this->assertDatabaseMissing('stylists', [
            'id' => $stylist->id,
        ]);
    }

    /** @test */
    public function non_admin_cannot_access_stylists()
    {
        $customer = User::create([
            'name' => 'Customer Test',
            'username' => 'customertest',
            'email' => 'customer@test.com',
            'role' => 'customer',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($customer)->get('/stylists');

        $response->assertStatus(403);
    }

    /** @test */
    public function guest_cannot_access_stylists()
    {
        $response = $this->get('/stylists');

        $response->assertRedirect(route('login'));
    }
}
