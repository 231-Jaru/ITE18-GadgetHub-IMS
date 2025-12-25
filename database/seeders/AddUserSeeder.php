<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AddUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add a single admin user
        DB::table('admins')->insert([
            'Username' => 'testadmin',
            'PasswordHash' => Hash::make('test123'),
            'Role' => 'Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add a single buyer user
        DB::table('buyers')->insert([
            'BuyerName' => 'Test Buyer',
            'Phone' => '+1-555-9999',
            'Email' => 'test.buyer@email.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Test users created successfully!');
        $this->command->info('Admin: Username: testadmin, Password: test123');
        $this->command->info('Buyer: Username: Test Buyer, Password: buyer123');
    }
}
