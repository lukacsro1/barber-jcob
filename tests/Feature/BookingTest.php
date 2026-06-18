<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a barber
        User::create([
            'name' => 'Barber Joe',
            'email' => 'joe@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_BARBER,
        ]);
    }
    public function test_successful_booking()
    {
        $barber = User::where('role', User::ROLE_BARBER)->first();

        $response = $this->post('/book', [
            'user_id' => $barber->id,
            'customer_name' => 'Test Customer',
            'customer_phone' => '0712345678', // 10 chars, valid
            'service' => 'Tuns',
            'start_at' => \Carbon\Carbon::parse('next Tuesday 10:00:00')->format('Y-m-d H:i:s'), // weekday, valid
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('appointments', [
            'user_id' => $barber->id,
            'customer_name' => 'Test Customer',
            'customer_phone' => '0712345678',
            'service' => 'Tuns',
        ]);
    }

    public function test_booking_fails_when_phone_too_short()
    {
        $barber = User::where('role', User::ROLE_BARBER)->first();

        $response = $this->post('/book', [
            'user_id' => $barber->id,
            'customer_name' => 'Test Customer',
            'customer_phone' => '123', // too short, invalid
            'service' => 'Tuns',
            'start_at' => \Carbon\Carbon::parse('next Tuesday 10:00:00')->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['customer_phone']);
        
        // Assert old input is preserved
        $response->assertSessionHas('_old_input.customer_phone', '123');
        $response->assertSessionHas('_old_input.customer_name', 'Test Customer');
        
        $this->assertDatabaseEmpty('appointments');
    }

    public function test_booking_fails_when_date_in_past()
    {
        $barber = User::where('role', User::ROLE_BARBER)->first();

        $response = $this->post('/book', [
            'user_id' => $barber->id,
            'customer_name' => 'Test Customer',
            'customer_phone' => '0712345678',
            'service' => 'Tuns',
            'start_at' => now()->subDay()->format('Y-m-d H:i:s'), // in the past, invalid
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['start_at']);
        
        $this->assertDatabaseEmpty('appointments');
    }

    public function test_booking_fails_on_non_working_day()
    {
        $barber = User::where('role', User::ROLE_BARBER)->first();

        // Sunday is non-working by default
        $response = $this->post('/book', [
            'user_id' => $barber->id,
            'customer_name' => 'Test Customer',
            'customer_phone' => '0712345678',
            'service' => 'Tuns',
            'start_at' => \Carbon\Carbon::parse('next Sunday 12:00:00')->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['start_at']);
        $this->assertDatabaseEmpty('appointments');
    }

    public function test_booking_fails_on_day_off()
    {
        $barber = User::where('role', User::ROLE_BARBER)->first();
        $date = \Carbon\Carbon::parse('next Tuesday');

        // Mark Tuesday as day off
        $barber->daysOff()->create([
            'date' => $date->toDateString(),
            'reason' => 'Holiday',
        ]);

        $response = $this->post('/book', [
            'user_id' => $barber->id,
            'customer_name' => 'Test Customer',
            'customer_phone' => '0712345678',
            'service' => 'Tuns',
            'start_at' => $date->setTime(12, 0, 0)->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['start_at']);
        $this->assertDatabaseEmpty('appointments');
    }

    public function test_booking_fails_out_of_hours()
    {
        $barber = User::where('role', User::ROLE_BARBER)->first();

        // Booking at 5:00 AM (default hours are 09:00 - 17:00)
        $response = $this->post('/book', [
            'user_id' => $barber->id,
            'customer_name' => 'Test Customer',
            'customer_phone' => '0712345678',
            'service' => 'Tuns',
            'start_at' => \Carbon\Carbon::parse('next Tuesday 05:00:00')->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['start_at']);
        $this->assertDatabaseEmpty('appointments');
    }
}
