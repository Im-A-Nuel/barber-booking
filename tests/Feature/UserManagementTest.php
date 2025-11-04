<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test admin
        $this->admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admintest',
            'email' => 'admin@test.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        // Create test non-admin user
        $this->customer = User::create([
            'name' => 'Customer Test',
            'username' => 'customertest',
            'email' => 'customer@test.com',
            'role' => 'customer',
            'password' => bcrypt('password'),
        ]);
    }

    /** @test */
    public function admin_can_view_users_list()
    {
        $response = $this->actingAs($this->admin)->get('/users');

        $response->assertStatus(200);
        $response->assertSee('Kelola User');
        $response->assertSee($this->customer->name);
    }

    /** @test */
    public function admin_can_view_create_user_form()
    {
        $response = $this->actingAs($this->admin)->get('/users/create');

        $response->assertStatus(200);
        $response->assertSee('Tambah User');
        $response->assertSee('Customer');
        $response->assertSee('Stylist');
        $response->assertSee('Admin');
    }

    /** @test */
    public function admin_can_create_customer_user()
    {
        $response = $this->actingAs($this->admin)->post('/users', [
            'name' => 'New Customer',
            'username' => 'newcustomer',
            'email' => 'newcustomer@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'customer',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'New Customer',
            'username' => 'newcustomer',
            'email' => 'newcustomer@test.com',
            'role' => 'customer',
        ]);
    }

    /** @test */
    public function admin_can_create_stylist_user()
    {
        $response = $this->actingAs($this->admin)->post('/users', [
            'name' => 'John Stylist',
            'username' => 'johnstylist',
            'email' => 'john@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'stylist',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'John Stylist',
            'username' => 'johnstylist',
            'email' => 'john@test.com',
            'role' => 'stylist',
        ]);
    }

    /** @test */
    public function admin_can_create_admin_user()
    {
        $response = $this->actingAs($this->admin)->post('/users', [
            'name' => 'New Admin',
            'username' => 'newadmin',
            'email' => 'newadmin@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'New Admin',
            'username' => 'newadmin',
            'email' => 'newadmin@test.com',
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function admin_can_edit_user()
    {
        $response = $this->actingAs($this->admin)->get('/users/' . $this->customer->id . '/edit');

        $response->assertStatus(200);
        $response->assertSee($this->customer->name);
    }

    /** @test */
    public function admin_can_update_user_role()
    {
        $response = $this->actingAs($this->admin)->put('/users/' . $this->customer->id, [
            'name' => $this->customer->name,
            'username' => $this->customer->username,
            'email' => $this->customer->email,
            'role' => 'stylist',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $this->customer->id,
            'role' => 'stylist',
        ]);
    }

    /** @test */
    public function admin_can_delete_user()
    {
        $newUser = User::create([
            'name' => 'User to Delete',
            'username' => 'usertodelete',
            'email' => 'delete@test.com',
            'role' => 'customer',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($this->admin)->delete('/users/' . $newUser->id);

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', [
            'id' => $newUser->id,
        ]);
    }

    /** @test */
    public function admin_cannot_delete_own_account()
    {
        $response = $this->actingAs($this->admin)->delete('/users/' . $this->admin->id);

        $response->assertStatus(302); // Redirect
        $this->assertDatabaseHas('users', [
            'id' => $this->admin->id,
        ]);
    }

    /** @test */
    public function admin_cannot_edit_own_account_from_user_management()
    {
        $response = $this->actingAs($this->admin)->get('/users/' . $this->admin->id . '/edit');

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function customer_cannot_access_user_management()
    {
        $response = $this->actingAs($this->customer)->get('/users');

        $response->assertStatus(403);
    }

    /** @test */
    public function guest_cannot_access_user_management()
    {
        $response = $this->get('/users');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function username_must_be_unique()
    {
        $response = $this->actingAs($this->admin)->post('/users', [
            'name' => 'Duplicate Username',
            'username' => $this->customer->username, // Duplicate
            'email' => 'duplicate@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'customer',
        ]);

        $response->assertSessionHasErrors('username');
    }

    /** @test */
    public function email_must_be_unique()
    {
        $response = $this->actingAs($this->admin)->post('/users', [
            'name' => 'Duplicate Email',
            'username' => 'newemail',
            'email' => $this->customer->email, // Duplicate
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'customer',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function password_must_be_minimum_8_characters()
    {
        $response = $this->actingAs($this->admin)->post('/users', [
            'name' => 'Short Password',
            'username' => 'shortpass',
            'email' => 'short@test.com',
            'password' => '12345',
            'password_confirmation' => '12345',
            'role' => 'customer',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function can_search_users_by_name()
    {
        $response = $this->actingAs($this->admin)->get('/users?search=Customer');

        $response->assertStatus(200);
        $response->assertSee($this->customer->name);
    }

    /** @test */
    public function can_filter_users_by_role()
    {
        $response = $this->actingAs($this->admin)->get('/users?role=customer');

        $response->assertStatus(200);
        $response->assertSee($this->customer->name);
    }
}
