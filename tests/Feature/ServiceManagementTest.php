<?php

namespace Tests\Feature;

use App\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_service_list()
    {
        $service = Service::create([
            'name' => 'Test Cut',
            'duration_minutes' => 30,
            'price' => 40000,
            'is_active' => true,
        ]);

        $response = $this->get('/services');

        $response->assertStatus(200);
        $response->assertSee($service->name);
    }

    /** @test */
    public function it_creates_a_service()
    {
        $response = $this->post('/services', [
            'name' => 'New Style',
            'duration_minutes' => 60,
            'price' => 75000,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('services.index'));
        $this->assertDatabaseHas('services', [
            'name' => 'New Style',
            'duration_minutes' => 60,
            'price' => 75000,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_updates_a_service()
    {
        $service = Service::create([
            'name' => 'Basic Cut',
            'duration_minutes' => 30,
            'price' => 50000,
            'is_active' => true,
        ]);

        $response = $this->put("/services/{$service->id}", [
            'name' => 'Basic Cut Plus',
            'duration_minutes' => 45,
            'price' => 65000,
            'is_active' => '0',
        ]);

        $response->assertRedirect(route('services.index'));
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Basic Cut Plus',
            'duration_minutes' => 45,
            'price' => 65000,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function it_deletes_a_service()
    {
        $service = Service::create([
            'name' => 'To Be Removed',
            'duration_minutes' => 30,
            'price' => 45000,
            'is_active' => true,
        ]);

        $response = $this->delete("/services/{$service->id}");

        $response->assertRedirect(route('services.index'));
        $this->assertDatabaseMissing('services', [
            'id' => $service->id,
        ]);
    }
}
