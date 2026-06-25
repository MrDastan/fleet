<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Vehicle extends Model
{
    protected $fillable = [
        'plat', 'model', 'type', 'year', 'color', 'engine_no', 'chassis_no',
        'department', 'odometer_km', 'status', 'emoji',
        'roadtax_expiry', 'insurance_expiry', 'puspakom_expiry',
        'next_service_date', 'next_service_km', 'qr_code_token',
    ];

    protected function casts(): array
    {
        return [
            'roadtax_expiry' => 'date',
            'insurance_expiry' => 'date',
            'puspakom_expiry' => 'date',
            'next_service_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Vehicle $vehicle) {
            if (!$vehicle->qr_code_token) {
                $vehicle->qr_code_token = Str::uuid()->toString();
            }
        });
    }

    public function getRoadtaxDaysAttribute(): int
    {
        return $this->roadtax_expiry ? (int) now()->diffInDays($this->roadtax_expiry, false) : 999;
    }

    public function getInsuranceDaysAttribute(): int
    {
        return $this->insurance_expiry ? (int) now()->diffInDays($this->insurance_expiry, false) : 999;
    }

    public function serviceRecords(): HasMany
    {
        return $this->hasMany(ServiceRecord::class);
    }

    public function fuelRecords(): HasMany
    {
        return $this->hasMany(FuelRecord::class);
    }

    public function movementLogs(): HasMany
    {
        return $this->hasMany(MovementLog::class);
    }

    public function samanRecords(): HasMany
    {
        return $this->hasMany(SamanRecord::class);
    }

    public function vehicleRequests(): HasMany
    {
        return $this->hasMany(VehicleRequest::class);
    }

    public function roadtaxRecords(): HasMany
    {
        return $this->hasMany(RoadtaxRecord::class);
    }

    public function anomalyRecords(): HasMany
    {
        return $this->hasMany(AnomalyRecord::class);
    }
}
