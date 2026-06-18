<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Tuns',
                'description' => 'Tuns, spălat, aranjat, styling',
                'price' => 95.00,
                'duration_minutes' => 45,
            ],
            [
                'name' => 'Tuns copil (12 ani)',
                'description' => 'Tuns, styling',
                'price' => 95.00,
                'duration_minutes' => 30,
            ],
            [
                'name' => 'Tuns, stilizat barbă',
                'description' => 'Tuns, spălat, aranjat, styling. Tuns barbă, contur, spălat, styling',
                'price' => 140.00,
                'duration_minutes' => 75,
            ],
            [
                'name' => 'Tuns Barba',
                'description' => 'Tuns barbă, stilizat, spălat',
                'price' => 50.00,
                'duration_minutes' => 30,
            ],
            [
                'name' => 'Ondulare permanent pt. bărbați',
                'description' => 'Ondularea părului cu soluție permanentă (nu include tuns)',
                'price' => 180.00,
                'duration_minutes' => 90,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['name' => $service['name']],
                [
                    'description' => $service['description'],
                    'price' => $service['price'],
                    'duration_minutes' => $service['duration_minutes'],
                ]
            );
        }
    }
}
