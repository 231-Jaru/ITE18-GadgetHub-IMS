<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AddUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:add {username} {password} {--role=Staff}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $username = $this->argument('username');
        $password = $this->argument('password');
        $role = $this->option('role');

            DB::table('admins')->insert([
                'Username' => $username,
                'PasswordHash' => Hash::make($password),
                'Role' => $role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->info("Admin user '{$username}' created successfully!");
            $this->info("Username: {$username}");
            $this->info("Password: {$password}");
            $this->info("Role: {$role}");

        return 0;
    }
}