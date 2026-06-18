<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'day_of_week', 'is_working', 'start_time', 'end_time'])]
class BarberSchedule extends Model
{
    protected $casts = [
        'is_working' => 'boolean',
    ];

    public function barber(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
