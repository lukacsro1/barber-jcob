<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'customer_name', 'service', 'start_at', 'status'])]
class Appointment extends Model
{
    public function barber(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

