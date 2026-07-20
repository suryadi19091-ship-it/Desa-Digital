<?php

namespace App\Gates;

use App\Models\User;

class ContentGate
{
    public function exportContent(?User $user)
    {
        return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin']));
    }

    public function manageContent(?User $user)
    {
        if (! $user || ! $user->isActive()) {
            return false;
        }

        // Check database permission first
        if (method_exists($user, 'hasPermission') && $user->hasPermission('manage-content')) {
            return true;
        }

        // Fallback to role-based check
        return $user->role === 'super_admin' || in_array($user->role, ['admin', 'editor']);
    }

    public function publishContent(?User $user)
    {
        return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin']));
    }

    public function moderateContent(?User $user)
    {
        return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin', 'moderator']));
    }

    public function viewContent(?User $user)
    {
        return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin', 'editor', 'moderator']));
    }

    public function editContent(?User $user)
    {
        return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin', 'editor']));
    }

    public function deleteContent(?User $user)
    {
        return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin']));
    }
}
