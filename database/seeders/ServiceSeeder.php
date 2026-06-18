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
            // Frizerie și barber shop
            [
                'name' => 'Tuns',
                'category' => 'Frizerie și barber shop',
                'description' => 'Tuns, spălat, aranjat, styling',
                'price' => 95.00,
                'duration_minutes' => 45,
            ],
            [
                'name' => 'Tuns copil (12 ani)',
                'category' => 'Frizerie și barber shop',
                'description' => 'Tuns, styling',
                'price' => 95.00,
                'duration_minutes' => 30,
            ],
            [
                'name' => 'Tuns, stilizat barbă',
                'category' => 'Frizerie și barber shop',
                'description' => 'Tuns, spălat, aranjat, styling. Tuns barbă, contur, spălat, styling',
                'price' => 140.00,
                'duration_minutes' => 75,
            ],
            [
                'name' => 'Tuns Barba',
                'category' => 'Frizerie și barber shop',
                'description' => 'Tuns barba, stilizat, spalat',
                'price' => 50.00,
                'duration_minutes' => 30,
            ],
            [
                'name' => 'Ondulare permanent pt. barbati',
                'category' => 'Frizerie și barber shop',
                'description' => 'Ondularea parului cu solutie permanenta (nu include tuns)',
                'price' => 180.00,
                'duration_minutes' => 90,
            ],

            // Coafor și hairstyling
            [
                'name' => 'Tuns par scurt',
                'category' => 'Coafor și hairstyling',
                'description' => 'Spalat, Tuns, Aranjat',
                'price' => 110.00,
                'duration_minutes' => 30,
            ],
            [
                'name' => 'Tuns par mediu',
                'category' => 'Coafor și hairstyling',
                'description' => 'Spalat, Tuns, Aranjat',
                'price' => 130.00,
                'duration_minutes' => 50,
            ],
            [
                'name' => 'Tuns par lung',
                'category' => 'Coafor și hairstyling',
                'description' => 'Spalat, Tuns, Aranjat',
                'price' => 180.00,
                'duration_minutes' => 60,
            ],
            [
                'name' => 'Tuns par extra lung',
                'category' => 'Coafor și hairstyling',
                'description' => 'Spalat, Tuns, Aranjat',
                'price' => 210.00,
                'duration_minutes' => 60,
            ],
            [
                'name' => 'Spalat, Aranjat Par Mediu',
                'category' => 'Coafor și hairstyling',
                'description' => '',
                'price' => 110.00,
                'duration_minutes' => 50,
            ],
            [
                'name' => 'Spalat, Aranjat Par Lung',
                'category' => 'Coafor și hairstyling',
                'description' => '',
                'price' => 160.00,
                'duration_minutes' => 60,
            ],
            [
                'name' => 'Spalat coafat extensii',
                'category' => 'Coafor și hairstyling',
                'description' => 'Spalat, coafat, masca pt. extensii, uscat din perie, coafat indreptat ori ondulat',
                'price' => 210.00,
                'duration_minutes' => 60,
            ],
            [
                'name' => 'Tuns breton',
                'category' => 'Coafor și hairstyling',
                'description' => '',
                'price' => 15.00,
                'duration_minutes' => 10,
            ],
            [
                'name' => 'Rooth colouring (vopsit radacini, par scurt)',
                'category' => 'Coafor și hairstyling',
                'description' => '',
                'price' => 170.00,
                'duration_minutes' => 60,
            ],
            [
                'name' => 'Rooth colouring (vopsit radacini par mediu)',
                'category' => 'Coafor și hairstyling',
                'description' => '',
                'price' => 230.00,
                'duration_minutes' => 60,
            ],
            [
                'name' => 'Rooth colouring (vopsit radacini par lung)',
                'category' => 'Coafor și hairstyling',
                'description' => '',
                'price' => 250.00,
                'duration_minutes' => 60,
            ],
            [
                'name' => 'Global colouring (vopsit global par scurt)',
                'category' => 'Coafor și hairstyling',
                'description' => '',
                'price' => 250.00,
                'duration_minutes' => 60,
            ],
            [
                'name' => 'Global colouring (vopsit global, par mediu)',
                'category' => 'Coafor și hairstyling',
                'description' => '',
                'price' => 280.00,
                'duration_minutes' => 60,
            ],
            [
                'name' => 'Global colouring (vopsit global, par lung)',
                'category' => 'Coafor și hairstyling',
                'description' => '',
                'price' => 310.00,
                'duration_minutes' => 60,
            ],
            [
                'name' => 'Global colourchange with bleaching (decolorare+nuantare par scurt)',
                'category' => 'Coafor și hairstyling',
                'description' => '',
                'price' => 350.00,
                'duration_minutes' => 60,
            ],
            [
                'name' => 'Global colourchange with bleaching (decolorare+nuantare par mediu)',
                'category' => 'Coafor și hairstyling',
                'description' => '',
                'price' => 450.00,
                'duration_minutes' => 60,
            ],
            [
                'name' => 'Global colourchange with bleaching (decolorare+nuantare par lung)',
                'category' => 'Coafor și hairstyling',
                'description' => '',
                'price' => 650.00,
                'duration_minutes' => 60,
            ],
            [
                'name' => 'Hair botox',
                'category' => 'Coafor și hairstyling',
                'description' => 'Este ideal pentru tine dacă: Ai părul deteriorat de vopsiri, decolorări sau coafări termice frecvente. Părul tău este uscat, tern, casant sau fără volum. Îți dorești un tratament care să nu îndrepte părul, ci să-l repare și să-i redea vitalitatea',
                'price' => 350.00,
                'duration_minutes' => 90,
            ],
            [
                'name' => 'Spalat aranjat par extra lung des',
                'category' => 'Coafor și hairstyling',
                'description' => 'Spalat aranjat par styling placa ondulator',
                'price' => 180.00,
                'duration_minutes' => 60,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['name' => $service['name']],
                [
                    'category' => $service['category'],
                    'description' => $service['description'],
                    'price' => $service['price'],
                    'duration_minutes' => $service['duration_minutes'],
                ]
            );
        }
    }
}
