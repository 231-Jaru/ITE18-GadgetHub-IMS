<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class FixUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:fix-password {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix a user password (useful for fixing double-hashed passwords)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return 1;
        }

        // Set the password - the 'hashed' cast will automatically hash it
        $user->password = $password;
        $user->save();

        $this->info("Password for user '{$email}' has been updated successfully!");
        $this->info("You can now login with this password.");

        return 0;
    }
}

