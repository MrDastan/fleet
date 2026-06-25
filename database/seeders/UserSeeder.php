<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Ahmad Daud',
                'email' => 'admin@msd.com.my',
                'password' => Hash::make('password'),
                'employee_no' => 'MSD001',
                'department' => 'IT',
                'position' => 'Pengurus IT',
                'pin' => '123456',
                'avatar_initials' => 'AD',
                'role' => 'admin',
            ],
            [
                'name' => 'Faridah Zainol',
                'email' => 'fleet@msd.com.my',
                'password' => Hash::make('password'),
                'employee_no' => 'MSD002',
                'department' => 'Pentadbiran',
                'position' => 'Pegawai Fleet',
                'pin' => '123456',
                'avatar_initials' => 'FZ',
                'role' => 'fleet',
            ],
            [
                'name' => 'En. Razif',
                'email' => 'staff@msd.com.my',
                'password' => Hash::make('password'),
                'employee_no' => 'MSD003',
                'department' => 'Kewangan',
                'position' => 'Pengurus Kewangan',
                'pin' => '123456',
                'avatar_initials' => 'ER',
                'role' => 'staff',
            ],
            [
                'name' => 'Ali bin Musa',
                'email' => 'guard@msd.com.my',
                'password' => Hash::make('password'),
                'employee_no' => 'MSD004',
                'department' => 'Operasi',
                'position' => 'Penjaga Kenderaan',
                'pin' => '123456',
                'avatar_initials' => 'AM',
                'role' => 'guard',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            $user->assignRole($role);
        }
    }
}
