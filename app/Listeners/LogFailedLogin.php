<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
        try {
            if ($event->user) {
                $user = $event->user;
                $user->increment('login_attempts');
                
                // Optional: handle permanent lockout if attempts exceed a threshold
                // Example: if ($user->login_attempts >= 10) { $user->update(['status' => 'suspended']); }
            }
        } catch (\Exception $e) {
            // Fail silently
        }
    }
}
