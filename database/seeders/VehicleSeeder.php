<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            [
                'plat' => 'WXY 5541',
                'model' => 'Toyota Hilux',
                'type' => 'Trak Pikap',
                'year' => 2021,
                'color' => 'Putih',
                'department' => 'Operasi',
                'odometer_km' => 88456,
                'status' => 'aktif',
                'emoji' => '🚚',
                'roadtax_expiry' => '2026-06-29',
                'insurance_expiry' => '2026-12-22',
                'next_service_date' => '2026-08-10',
                'next_service_km' => 90000,
            ],
            [
                'plat' => 'ABC 1234',
                'model' => 'Honda Civic',
                'type' => 'Sedan',
                'year' => 2022,
                'color' => 'Kelabu',
                'department' => 'Kewangan',
                'odometer_km' => 45780,
                'status' => 'aktif',
                'emoji' => '🚗',
                'roadtax_expiry' => '2026-12-15',
                'insurance_expiry' => '2026-06-29',
                'next_service_date' => '2026-07-05',
                'next_service_km' => 50000,
            ],
            [
                'plat' => 'VBM 3310',
                'model' => 'Proton X70',
                'type' => 'SUV',
                'year' => 2022,
                'color' => 'Hitam',
                'department' => 'Pengurusan',
                'odometer_km' => 61240,
                'status' => 'servis',
                'emoji' => '🚙',
                'roadtax_expiry' => '2027-03-01',
                'insurance_expiry' => '2027-01-20',
                'next_service_date' => '2026-07-06',
                'next_service_km' => 65000,
            ],
            [
                'plat' => 'QRS 9981',
                'model' => 'Perodua Myvi',
                'type' => 'Hatchback',
                'year' => 2020,
                'color' => 'Merah',
                'department' => 'Pemasaran',
                'odometer_km' => 28312,
                'status' => 'aktif',
                'emoji' => '🚘',
                'roadtax_expiry' => '2026-07-20',
                'insurance_expiry' => '2026-09-22',
                'next_service_date' => '2026-07-13',
                'next_service_km' => 30000,
            ],
            [
                'plat' => 'KLM 7722',
                'model' => 'Toyota Vios',
                'type' => 'Sedan',
                'year' => 2021,
                'color' => 'Silver',
                'department' => 'IT',
                'odometer_km' => 34128,
                'status' => 'aktif',
                'emoji' => '🚗',
                'roadtax_expiry' => '2026-07-10',
                'insurance_expiry' => '2026-10-22',
                'next_service_date' => '2026-08-25',
                'next_service_km' => 40000,
            ],
            [
                'plat' => 'DEF 8811',
                'model' => 'Proton Saga',
                'type' => 'Sedan',
                'year' => 2019,
                'color' => 'Biru',
                'department' => 'HR',
                'odometer_km' => 52400,
                'status' => 'aktif',
                'emoji' => '🚗',
                'roadtax_expiry' => '2026-09-22',
                'insurance_expiry' => '2026-11-15',
                'next_service_date' => '2026-07-01',
                'next_service_km' => 55000,
            ],
        ];

        foreach ($vehicles as $data) {
            Vehicle::firstOrCreate(['plat' => $data['plat']], $data);
        }
    }
}
