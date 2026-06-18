<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminPasswordGenerated;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'lrxdeveloper@gmail.com';
        $password = Str::password(12);

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => Hash::make($password),
                'role' => User::ROLE_ADMIN,
            ]
        );

        if ($user->wasRecentlyCreated || $user->wasChanged('password')) {
            Mail::to($email)->send(new AdminPasswordGenerated($email, $password));
        }
    }
}
