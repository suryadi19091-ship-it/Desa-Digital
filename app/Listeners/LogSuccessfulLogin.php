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
            activity('auth')
                ->causedBy($event->user)
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
