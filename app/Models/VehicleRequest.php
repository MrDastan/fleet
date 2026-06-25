<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleRequest extends Model
{
    protected $fillable = [
        'requester_user_id', 'vehicle_id', 'request_no',
        'use_date', 'time_start', 'time_end', 'purpose', 'destination',
        'passengers', 'notes', 'status', 'stage',
        'guard_user_id', 'guard_note', 'guard_checklist', 'guard_odometer', 'guard_action_at',
        'fleet_user_id', 'fleet_note', 'fleet_priority', 'fleet_action_at',
        'admin_override_by', 'admin_override_reason', 'admin_override_at',
    ];

    protected function casts(): array
    {
        return [
            'use_date' => 'date',
            'guard_checklist' => 'array',
            'guard_action_at' => 'datetime',
            'fleet_action_at' => 'datetime',
            'admin_override_at' => 'datetime',
        ];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_user_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function guard(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guard_user_id');
    }

    public function fleetOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fleet_user_id');
    }
}
