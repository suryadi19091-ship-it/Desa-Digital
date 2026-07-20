<?php

namespace App\Gates;

use App\Models\User;

class ServiceGate
{
    public function manageContactMessages(?User $user)
    {
        if (! $user || ! $user->isActive()) {
            return false;
        }

        // Check database permission first
        if (method_exists($user, 'hasPermission') && $user->hasPermission('manage-contact-messages')) {
            return true;
        }

        // Fallback to role-based check
        return $user->role === 'super_admin' || in_array($user->role, ['admin', 'cs_officer']);
    }

    public function replyContactMessages(?User $user)
    {
        return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin', 'cs_officer']));
    }

    public function sendNotifications(?User $user)
    {
        return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin']));
    }

    public function manageServices(?User $user)
    {
        return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin', 'service_officer']));
    }

    public function processServiceRequests(?User $user)
    {
        return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin', 'service_officer']));
    }

    public function manageLetterTemplates(?User $user)
    {
        return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin']));
    }

    public function letterTemplatesView(?User $user)
    {
        return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin', 'editor']));
    }

    public function letterTemplatesCreate(?User $user)
    {
        return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin']));
    }

    public function letterTemplatesEdit(?User $user)
    {
        return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin']));
    }

    public function letterTemplatesDelete(?User $user)
    {
        return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin']));
    }

    public function generateReports(?User $user)
    {
        return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin', 'report_officer']));
    }

    public function exportData(?User $user)
    {
        return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin']));
    }
}
