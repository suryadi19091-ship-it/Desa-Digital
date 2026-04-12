<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class LogUserActivityMiddleware
{
    /**
     * Paths/Keywords to ignore from logging to prevent DB spam.
     */
    protected $ignoredPaths = [
        'api/*',
        'admin/system-info',
        'admin/dashboard/stats',
        '_ignition/*',
        'telescope/*',
        'broadcasting/auth'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Auth::check() && $this->shouldLog($request)) {
            $user = Auth::user();
            $path = $request->path();
            $method = $request->method();

            // Determine action type
            $action = 'Membuka Halaman';
            $description = "Mengakses URL: /{$path}";

            if (str_contains($path, 'export') || str_contains($path, 'download') || str_contains($path, 'template')) {
                $action = 'Unduh Data';
                $description = "Mengunduh file dari: /{$path}";
            } elseif ($method === 'POST') {
                // If it's a POST and not downloading, it might be a submission not caught by Eloquent logs
                $action = 'Eksekusi Aksi';
                $description = "Menjalankan [$method] di URL: /{$path}";
                // Skip generic POST logs because Spatie Eloquent will catch actual CRUD
                // return $response; // Uncomment to disable POST generic logging
            }

            try {
                activity('system')
                    ->causedBy($user)
                    ->withProperties([
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'method' => $method,
                        'url' => $request->fullUrl()
                    ])
                    ->log("{$action} - {$description}");
            } catch (\Exception $e) {
                // Fail silently so logging doesn't break the app
            }
        }

        return $response;
    }

    protected function shouldLog(Request $request)
    {
        // Don't log AJAX requests (except specific actions if needed)
        if ($request->ajax()) {
            return false;
        }

        // Check if the current path matches any ignored patterns
        foreach ($this->ignoredPaths as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }

        return true;
    }
}
