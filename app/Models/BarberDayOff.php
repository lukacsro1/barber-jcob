<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'date', 'reason'])]
class BarberDayOff extends Model
{
    protected $table = 'barber_days_off';

    protected $casts = [
        'date' => 'date',
    ];

    public function barber(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
