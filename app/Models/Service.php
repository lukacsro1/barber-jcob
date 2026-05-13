<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'description', 'price', 'duration_minutes'])]
class Service extends Model
{
    //
}

