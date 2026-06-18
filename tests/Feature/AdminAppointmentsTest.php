<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentNotification;
use Tests\TestCase;

class AdminAppointmentsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $barber1;
    protected User $barber2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        $this->barber1 = User::create([
            'name' => 'Barber One',
            'email' => 'barber1@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_BARBER,
        ]);

        $this->barber2 = User::create([
            'name' => 'Barber Two',
            'email' => 'barber2@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_BARBER,
        ]);
    }

    public function test_admin_can_create_appointment_for_any_barber()
    {
        Mail::fake();

        $response = $this->actingAs($this->admin)->postJson('/admin/appointments', [
            'user_id' => $this->barber1->id,
            'customer_name' => 'John Doe',
            'customer_phone' => '0712345678',
            'service' => 'Haircut',
            'start_at' => '2026-06-10 10:00:00',
            'status' => 'scheduled',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('appointments', [
            'user_id' => $this->barber1->id,
            'customer_name' => 'John Doe',
        ]);

        Mail::assertSent(AppointmentNotification::class, function ($mail) {
            return $mail->hasTo($this->barber1->email) && $mail->action === 'booked';
        });
    }

    public function test_barber_can_create_appointment_for_themselves()
    {
        Mail::fake();

        $response = $this->actingAs($this->barber1)->postJson('/admin/appointments', [
            'user_id' => $this->barber1->id,
            'customer_name' => 'John Doe',
            'customer_phone' => '0712345678',
            'service' => 'Haircut',
            'start_at' => '2026-06-10 10:00:00',
            'status' => 'scheduled',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('appointments', [
            'user_id' => $this->barber1->id,
            'customer_name' => 'John Doe',
        ]);

        Mail::assertSent(AppointmentNotification::class, function ($mail) {
            return $mail->hasTo($this->barber1->email) && $mail->action === 'booked';
        });
    }

    public function test_barber_cannot_create_appointment_for_another_barber()
    {
        Mail::fake();

        $response = $this->actingAs($this->barber1)->postJson('/admin/appointments', [
            'user_id' => $this->barber2->id,
            'customer_name' => 'John Doe',
            'customer_phone' => '0712345678',
            'service' => 'Haircut',
            'start_at' => '2026-06-10 10:00:00',
            'status' => 'scheduled',
        ]);

        $response->assertStatus(403);
        Mail::assertNotSent(AppointmentNotification::class);
    }

    public function test_admin_can_update_any_appointment()
    {
        Mail::fake();

        $appointment = Appointment::create([
            'user_id' => $this->barber1->id,
            'customer_name' => 'John Doe',
            'customer_phone' => '0712345678',
            'service' => 'Haircut',
            'start_at' => '2026-06-10 10:00:00',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($this->admin)->postJson("/admin/appointments/{$appointment->id}", [
            'user_id' => $this->barber2->id, // Admin changes barber
            'customer_name' => 'John Doe Updated',
            'customer_phone' => '0787654321',
            'service' => 'Beard Trim',
            'start_at' => '2026-06-11 11:00:00',
            'status' => 'completed',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'user_id' => $this->barber2->id,
            'customer_name' => 'John Doe Updated',
            'status' => 'completed',
        ]);

        Mail::assertSent(AppointmentNotification::class, function ($mail) {
            return $mail->hasTo($this->barber2->email) && $mail->action === 'updated';
        });
    }

    public function test_barber_can_update_own_appointment()
    {
        Mail::fake();

        $appointment = Appointment::create([
            'user_id' => $this->barber1->id,
            'customer_name' => 'John Doe',
            'customer_phone' => '0712345678',
            'service' => 'Haircut',
            'start_at' => '2026-06-10 10:00:00',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($this->barber1)->postJson("/admin/appointments/{$appointment->id}", [
            'user_id' => $this->barber1->id,
            'customer_name' => 'John Doe Updated',
            'customer_phone' => '0712345678',
            'service' => 'Haircut',
            'start_at' => '2026-06-10 10:00:00',
            'status' => 'completed',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'customer_name' => 'John Doe Updated',
            'status' => 'completed',
        ]);

        Mail::assertSent(AppointmentNotification::class, function ($mail) {
            return $mail->hasTo($this->barber1->email) && $mail->action === 'updated';
        });
    }

    public function test_barber_cannot_update_another_barbers_appointment()
    {
        Mail::fake();

        $appointment = Appointment::create([
            'user_id' => $this->barber2->id,
            'customer_name' => 'John Doe',
            'customer_phone' => '0712345678',
            'service' => 'Haircut',
            'start_at' => '2026-06-10 10:00:00',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($this->barber1)->postJson("/admin/appointments/{$appointment->id}", [
            'user_id' => $this->barber2->id,
            'customer_name' => 'John Doe Attempt',
            'customer_phone' => '0712345678',
            'service' => 'Haircut',
            'start_at' => '2026-06-10 10:00:00',
            'status' => 'scheduled',
        ]);

        $response->assertStatus(403);
        Mail::assertNotSent(AppointmentNotification::class);
    }

    public function test_admin_can_delete_any_appointment()
    {
        Mail::fake();

        $appointment = Appointment::create([
            'user_id' => $this->barber1->id,
            'customer_name' => 'John Doe',
            'customer_phone' => '0712345678',
            'service' => 'Haircut',
            'start_at' => '2026-06-10 10:00:00',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($this->admin)->deleteJson("/admin/appointments/{$appointment->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('appointments', [
            'id' => $appointment->id,
        ]);

        Mail::assertSent(AppointmentNotification::class, function ($mail) {
            return $mail->hasTo($this->barber1->email) && $mail->action === 'cancelled';
        });
    }

    public function test_barber_can_delete_own_appointment()
    {
        Mail::fake();

        $appointment = Appointment::create([
            'user_id' => $this->barber1->id,
            'customer_name' => 'John Doe',
            'customer_phone' => '0712345678',
            'service' => 'Haircut',
            'start_at' => '2026-06-10 10:00:00',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($this->barber1)->deleteJson("/admin/appointments/{$appointment->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('appointments', [
            'id' => $appointment->id,
        ]);

        Mail::assertSent(AppointmentNotification::class, function ($mail) {
            return $mail->hasTo($this->barber1->email) && $mail->action === 'cancelled';
        });
    }

    public function test_barber_cannot_delete_another_barbers_appointment()
    {
        Mail::fake();

        $appointment = Appointment::create([
            'user_id' => $this->barber2->id,
            'customer_name' => 'John Doe',
            'customer_phone' => '0712345678',
            'service' => 'Haircut',
            'start_at' => '2026-06-10 10:00:00',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($this->barber1)->deleteJson("/admin/appointments/{$appointment->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
        ]);
        Mail::assertNotSent(AppointmentNotification::class);
    }
}
