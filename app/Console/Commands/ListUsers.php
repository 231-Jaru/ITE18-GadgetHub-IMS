<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all admin users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== ADMIN USERS ===');
        $admins = DB::table('admins')->select('Username', 'Role', 'created_at')->get();
        
        if ($admins->isEmpty()) {
            $this->warn('No admin users found.');
        } else {
            $adminData = $admins->map(function($admin) {
                return [
                    $admin->Username,
                    $admin->Role,
                    $admin->created_at
                ];
            })->toArray();
            
            $this->table(
                ['Username', 'Role', 'Created At'],
                $adminData
            );
        }

        $this->info('=== LOGIN CREDENTIALS ===');
        $this->info('Admin users: Use their username and the password you set when creating them.');
    }
}