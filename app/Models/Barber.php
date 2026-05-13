<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'email', 'phone', 'specialty', 'is_active'])]
class Barber extends Model
{
    /** @use HasFactory<BarberFactory> */
    use HasFactory;
}
