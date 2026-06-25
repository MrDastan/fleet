<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovementLog extends Model
{
    protected $fillable = [
        'vehicle_id', 'driver_user_id', 'department', 'purpose', 'destination',
        'checkout_time', 'checkin_time', 'km_out', 'km_in', 'guard_notes', 'status',
    ];

    protected function casts(): array
    {
        return [
            'checkout_time' => 'datetime',
            'checkin_time' => 'datetime',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_user_id');
    }
}
