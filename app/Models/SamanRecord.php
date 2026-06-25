<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SamanRecord extends Model
{
    protected $fillable = [
        'vehicle_id', 'driver_user_id', 'saman_no', 'saman_type',
        'offense', 'offense_detail', 'date', 'time', 'location', 'location_detail',
        'amount', 'due_date', 'status', 'responsibility',
        'payment_date', 'receipt_no', 'receipt_file', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'due_date' => 'date',
            'payment_date' => 'date',
            'amount' => 'decimal:2',
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
