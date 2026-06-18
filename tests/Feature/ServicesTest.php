<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $barber;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        $this->barber = User::create([
            'name' => 'Barber User',
            'email' => 'barber@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_BARBER,
        ]);
    }

    public function test_non_admin_cannot_access_services_page()
    {
        $response = $this->actingAs($this->barber)->get('/admin/services');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_services_page()
    {
        $response = $this->actingAs($this->admin)->get('/admin/services');
        $response->assertStatus(200);
        $response->assertViewHas('services');
    }

    public function test_admin_can_create_service()
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/services', [
            'name' => 'Haircut and Shave',
            'price' => 50,
            'duration_minutes' => 45,
            'description' => 'Premium service',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('services', [
            'name' => 'Haircut and Shave',
            'price' => 50,
            'duration_minutes' => 45,
        ]);
    }

    public function test_non_admin_cannot_create_service()
    {
        $response = $this->actingAs($this->barber)->postJson('/admin/services', [
            'name' => 'Haircut and Shave',
            'price' => 50,
            'duration_minutes' => 45,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseEmpty('services');
    }

    public function test_service_validation()
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/services', [
            'name' => '',
            'price' => -10,
            'duration_minutes' => 0,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'price', 'duration_minutes']);
    }

    public function test_admin_can_delete_service()
    {
        $service = Service::create([
            'name' => 'Quick Trim',
            'price' => 20,
            'duration_minutes' => 15,
        ]);

        $response = $this->actingAs($this->admin)->deleteJson("/admin/services/{$service->id}");
        
        $response->assertStatus(200);
        $this->assertDatabaseMissing('services', [
            'id' => $service->id,
        ]);
    }

    public function test_non_admin_cannot_delete_service()
    {
        $service = Service::create([
            'name' => 'Quick Trim',
            'price' => 20,
            'duration_minutes' => 15,
        ]);

        $response = $this->actingAs($this->barber)->deleteJson("/admin/services/{$service->id}");
        
        $response->assertStatus(403);
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
        ]);
    }
}
