<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\View\Composers\SidebarComposer;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogFailedLogin;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register view composer for sidebar
        View::composer('frontend.layout.sidebar-right', SidebarComposer::class);

        Event::listen(
            Login::class,
            LogSuccessfulLogin::class,
        );

        Event::listen(
            Failed::class,
            LogFailedLogin::class,
        );

        // Configure Rate Limiters
        $this->configureRateLimiting();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(1000)->by($request->ip());
        });

        RateLimiter::for('uploads', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}
