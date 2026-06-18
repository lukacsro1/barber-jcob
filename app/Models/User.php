<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'phone', 'specialty', 'avatar_url', 'show_in_gallery'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_BARBER = 'barber';
    const ROLE_CUSTOMER = 'customer';

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isBarber(): bool
    {
        return $this->role === self::ROLE_BARBER;
    }

    protected static function booted(): void
    {
        static::created(function ($user) {
            if ($user->isBarber()) {
                $user->seedDefaultSchedule();
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'show_in_gallery' => 'boolean',
        ];
    }

    public function schedules(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BarberSchedule::class, 'user_id');
    }

    public function daysOff(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BarberDayOff::class, 'user_id');
    }

    public function seedDefaultSchedule(): void
    {
        for ($i = 0; $i < 7; $i++) {
            // Monday (1) to Friday (5) are working days by default
            // Saturday (6) and Sunday (0) are off days
            $isWorking = ($i >= 1 && $i <= 5);
            $this->schedules()->updateOrCreate(
                ['day_of_week' => $i],
                [
                    'is_working' => $isWorking,
                    'start_time' => $isWorking ? '09:00:00' : null,
                    'end_time' => $isWorking ? '17:00:00' : null,
                ]
            );
        }
    }
}

