<?php

namespace App\Gates;

use App\Models\User;

class DataGate
{
    public function manageVillageData(?User $user)
    {
            if (! $user || ! $user->isActive()) {
                return false;
            }

            // Check database permission first
            if (method_exists($user, 'hasPermission') && $user->hasPermission('manage-village-data')) {
                return true;
            }

            // Fallback to role-based check
            return $user->role === 'super_admin' || in_array($user->role, ['admin', 'village_officer']);
    }

    public function managePopulationData(?User $user)
    {
            if (! $user || ! $user->isActive()) {
                return false;
            }

            // Check database permission first
            if (method_exists($user, 'hasPermission') && $user->hasPermission('manage-population-data')) {
                return true;
            }

            // Fallback to role-based check
            return $user->role === 'super_admin' || in_array($user->role, ['admin', 'population_officer']);
    }

    public function manageBudgetData(?User $user)
    {
            return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin', 'finance_officer']));
    }

    public function viewSensitiveData(?User $user)
    {
            return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin']));
    }

    public function manageLocations(?User $user)
    {
            if (! $user || ! $user->isActive()) {
                return false;
            }

            // Check database permission first
            if (method_exists($user, 'hasPermission') && $user->hasPermission('manage-locations')) {
                return true;
            }

            // Fallback to role-based check
            return $user->role === 'super_admin' || in_array($user->role, ['admin']);
    }

    public function manageVillageBudget(?User $user)
    {
            if (! $user || ! $user->isActive()) {
                return false;
            }

            // Check database permission first
            if (method_exists($user, 'hasPermission') && $user->hasPermission('manage-village-budget')) {
                return true;
            }

            // Fallback to role-based check
            return $user->role === 'super_admin' || in_array($user->role, ['admin', 'finance_officer']);
    }

}
