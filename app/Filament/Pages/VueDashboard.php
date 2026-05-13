<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard;

class VueDashboard extends Dashboard
{
    protected static ?string $title = 'Vue Dashboard';

    protected string $view = 'filament.pages.vue-dashboard';
}
