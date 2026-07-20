<?php

namespace App\Gates;

use App\Models\User;

class SystemGate
{
    public function accessSystem(?User $user)
    {
        return $user && $user->is_active === true;
    }

    public function accountActive(?User $user)
    {
        return $user && $user->is_active === true;
    }

    public function accountVerified(?User $user)
    {
        return $user && $user->email_verified_at !== null;
    }

    public function manageSettings(?User $user)
    {
        return $user && $user->role === 'super_admin';
    }

    public function manageSystemBackup(?User $user)
    {
        return $user && $user->role === 'super_admin';
    }

    public function viewSystemLogs(?User $user)
    {
        return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin']));
    }

    public function managePermissions(?User $user)
    {
        if (! $user || ! $user->isActive()) {
            return false;
        }

        // Check database permission first
        if (method_exists($user, 'hasPermission') && $user->hasPermission('manage-permissions')) {
            return true;
        }

        // Fallback to role-based check
        return $user->role === 'super_admin';
    }

    public function impersonateUser(?User $user, User $targetUser)
    {
        if (! $user || $user->role !== 'super_admin') {
            return false;
        }

        // Cannot impersonate self or other super admins
        return $user->id !== $targetUser->id && $targetUser->role !== 'super_admin';
    }

    public function accessMaintenanceMode(?User $user)
    {
        return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin']));
    }

    public function loginDuringMaintenance(?User $user)
    {
        if (! $user) {
            return false;
        }

        // Allow super_admin and admins to login during maintenance
        if ($user->role === 'super_admin' || in_array($user->role, ['admin'])) {
            return true;
        }

        // Check if system is in maintenance mode
        return ! app()->isDownForMaintenance();
    }

    public function adminIpRestriction(?User $user)
    {
        if (! $user || ! ($user->role === 'super_admin' || in_array($user->role, ['admin']))) {
            return false;
        }

        // Super admin bypasses IP restrictions
        if ($user->role === 'super_admin') {
            return true;
        }

        // Check if IP restriction is enabled for regular admins
        $allowedIps = config('auth.admin_allowed_ips', []);

        if ($allowedIps === []) {
            return true; // No restriction
        }

        $currentIp = request()->ip();

        return in_array($currentIp, $allowedIps);
    }

    public function viewLogs(?User $user)
    {
        return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin']));
    }

    public function viewActivityLogs(?User $user)
    {
        return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin']));
    }

    public function viewSystemInfo(?User $user)
    {
        if (! $user || ! $user->isActive()) {
            return false;
        }

        // Check database permission first
        if (method_exists($user, 'hasPermission') && $user->hasPermission('view-system-info')) {
            return true;
        }

        // Fallback to role-based check
        return $user->role === 'super_admin' || in_array($user->role, ['admin']);
    }

    public function clearLogs(?User $user)
    {
        return $user && $user->role === 'super_admin';
    }
}
