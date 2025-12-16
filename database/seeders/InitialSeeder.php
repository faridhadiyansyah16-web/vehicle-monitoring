<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::updateOrCreate(['email' => 'approver1@example.com'], [
            'name' => 'Approver 1',
            'password' => Hash::make('password'),
            'role' => 'approver',
            'is_active' => true,
        ]);

        User::updateOrCreate(['email' => 'approver2@example.com'], [
            'name' => 'Approver 2',
            'password' => Hash::make('password'),
            'role' => 'approver',
            'is_active' => true,
        ]);

        Vehicle::updateOrCreate(['plate_number' => 'B 1234 ABC'], [
            'type' => 'Pickup',
            'capacity' => 2,
            'fuel_type' => 'diesel',
            'is_company_owned' => true,
            'status' => 'available',
            'odometer' => 12000,
        ]);

        Vehicle::updateOrCreate(['plate_number' => 'B 5678 XYZ'], [
            'type' => 'SUV',
            'capacity' => 7,
            'fuel_type' => 'gasoline',
            'is_company_owned' => true,
            'status' => 'available',
            'odometer' => 45000,
        ]);

        Driver::updateOrCreate(['name' => 'Budi'], [
            'phone' => '081234567890',
            'license_number' => 'SIM-12345',
            'status' => 'active',
        ]);

        Driver::updateOrCreate(['name' => 'Siti'], [
            'phone' => '081298765432',
            'license_number' => 'SIM-54321',
            'status' => 'active',
        ]);
    }
}

