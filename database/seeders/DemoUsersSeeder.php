<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add demo admin users
        $admins = [
            [
                'Username' => 'admin',
                'PasswordHash' => Hash::make('admin123'),
                'Role' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Username' => 'manager',
                'PasswordHash' => Hash::make('manager123'),
                'Role' => 'Staff',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Username' => 'staff',
                'PasswordHash' => Hash::make('staff123'),
                'Role' => 'Staff',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('admins')->insert($admins);

        // Add demo buyer users
        $buyers = [
            [
                'BuyerName' => 'John Smith',
                'Phone' => '+1-555-0101',
                'Email' => 'john.smith@email.com',
                'PasswordHash' => Hash::make('buyer123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'BuyerName' => 'Sarah Johnson',
                'Phone' => '+1-555-0102',
                'Email' => 'sarah.johnson@email.com',
                'PasswordHash' => Hash::make('buyer123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'BuyerName' => 'Mike Wilson',
                'Phone' => '+1-555-0103',
                'Email' => 'mike.wilson@email.com',
                'PasswordHash' => Hash::make('buyer123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'BuyerName' => 'Emily Davis',
                'Phone' => '+1-555-0104',
                'Email' => 'emily.davis@email.com',
                'PasswordHash' => Hash::make('buyer123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'BuyerName' => 'David Brown',
                'Phone' => '+1-555-0105',
                'Email' => 'david.brown@email.com',
                'PasswordHash' => Hash::make('buyer123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('buyers')->insert($buyers);

        $this->command->info('Demo users created successfully!');
        $this->command->info('Admin credentials:');
        $this->command->info('- Username: admin, Password: admin123');
        $this->command->info('- Username: manager, Password: manager123');
        $this->command->info('- Username: staff, Password: staff123');
        $this->command->info('Buyer credentials:');
        $this->command->info('- Username: John Smith, Password: buyer123');
        $this->command->info('- Username: Sarah Johnson, Password: buyer123');
        $this->command->info('- Username: Mike Wilson, Password: buyer123');
        $this->command->info('- Username: Emily Davis, Password: buyer123');
        $this->command->info('- Username: David Brown, Password: buyer123');
    }
}
