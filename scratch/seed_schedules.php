<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$barbers = User::where('role', User::ROLE_BARBER)->get();
foreach ($barbers as $barber) {
    $barber->seedDefaultSchedule();
    echo "Seeded default schedule for barber: {$barber->name}\n";
}
