<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        try {
            // Update user login details
            $user = $event->user;
            $user->forceFill([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
                'login_attempts' => 0,
            ])->save();

            // Log activity
            activity('auth')
                ->causedBy($user)
                ->withProperties([
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ])
                ->log('Berhasil Login ke sistem');
        } catch (\Exception $e) {
            // Fail silently
        }
    }
}
