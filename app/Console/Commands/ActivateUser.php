<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ActivateUser extends Command
{
    protected $signature = 'user:activate {email}';

    protected $description = 'Activate user and set as admin if needed';

    public function handle()
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User with email '{$email}' not found!");

            return 1;
        }

        // Activate user
        $user->is_active = true;
        $user->email_verified_at = $user->email_verified_at ?? now();

        // Ask if should set as admin
        if ($this->confirm('Set user as admin?', true)) {
            $user->role = 'admin';
        }

        $user->save();

        $this->info('✅ User activated successfully!');
        $this->info("Name: {$user->name}");
        $this->info("Email: {$user->email}");
        $this->info("Role: {$user->role}");
        $this->info('Active: '.($user->is_active ? 'Yes' : 'No'));
        $this->info('Email Verified: '.($user->email_verified_at ? 'Yes' : 'No'));

        return 0;
    }
}
