<?php

namespace App\Models;

use App\Traits\HasFileUploads;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelRecord extends Model
{
    use HasFileUploads;
    protected $fillable = [
        'vehicle_id', 'user_id', 'datetime', 'station', 'fuel_type',
        'liters', 'price_per_liter', 'total_cost', 'odometer_km', 'consumption_l100km',
    ];

    protected function casts(): array
    {
        return [
            'datetime' => 'datetime',
            'liters' => 'decimal:2',
            'price_per_liter' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'consumption_l100km' => 'decimal:1',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
