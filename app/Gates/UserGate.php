<?php

namespace App\Gates;

use App\Models\User;

class UserGate
{
    public function accessAdminPanel(?User $user)
    {
            if (! $user) {
                return false;
            }

            // Check if user is active
            if (! $user->isActive()) {
                return false;
            }

            // Allow admin and super_admin roles
            if (isset($user->role)) {
                return in_array($user->role, ['admin', 'super_admin']);
            }

            return false;
    }

    public function accessUserDashboard(?User $user)
    {
            return $user && in_array($user->role, ['user', 'member', 'resident']);
    }

    public function isAdmin(?User $user)
    {
            return $user && in_array($user->role, ['admin', 'super_admin']);
    }

    public function isSuperAdmin(?User $user)
    {
            return $user && $user->role === 'super_admin';
    }

    public function isUser(?User $user)
    {
            return $user && in_array($user->role, ['user', 'member', 'resident']);
    }

    public function viewProfile(?User $user, ?User $targetUser = null)
    {
            if (! $user) {
                return false;
            }

            // Users can view their own profile
            if ($targetUser && $user->id === $targetUser->id) {
                return true;
            }

            // Admins can view any profile
            if (in_array($user->role, ['admin', 'super_admin'])) {
                return true;
            }

            // Default: can view own profile
            return $targetUser === null;
    }

    public function updateProfile(?User $user, ?User $targetUser = null)
    {
            if (! $user) {
                return false;
            }

            // Users can update their own profile
            if ($targetUser && $user->id === $targetUser->id) {
                return $user->is_active === true;
            }

            // Admins can update any profile
            if (in_array($user->role, ['admin', 'super_admin'])) {
                return true;
            }

            // Default: can update own profile if active
            return $targetUser === null && $user->is_active === true;
    }

    public function changePassword(?User $user, ?User $targetUser = null)
    {
            if (! $user) {
                return false;
            }

            // Users can change their own password
            if ($targetUser && $user->id === $targetUser->id) {
                return $user->is_active === true;
            }

            // Admins can change any password except super admin
            if ($user->role === 'admin') {
                return $targetUser === null || $targetUser->role !== 'super_admin';
            }

            // Super admins can change any password
            if ($user->role === 'super_admin') {
                return true;
            }

            // Default: can change own password if active
            return $targetUser === null && $user->is_active === true;
    }

    public function manageUsers(?User $user)
    {
            if (! $user || ! $user->isActive()) {
                return false;
            }

            // Check database permission first
            if (method_exists($user, 'hasPermission') && $user->hasPermission('manage-users')) {
                return true;
            }

            // Fallback to role-based check
            return $user->role === 'super_admin' || in_array($user->role, ['admin']);
    }

    public function createUser(?User $user)
    {
            return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin']));
    }

    public function updateUser(?User $user, User $targetUser)
    {
            if (! $user || ! in_array($user->role, ['admin', 'super_admin'])) {
                return false;
            }

            // Super admin can update anyone
            if ($user->role === 'super_admin') {
                return true;
            }

            // Admin cannot update super admin or other admins
            if ($user->role === 'admin') {
                return ! in_array($targetUser->role, ['admin', 'super_admin']);
            }

            return false;
    }

    public function deleteUser(?User $user, User $targetUser)
    {
            if (! $user || ! in_array($user->role, ['admin', 'super_admin'])) {
                return false;
            }

            // Cannot delete self
            if ($user->id === $targetUser->id) {
                return false;
            }

            // Super admin can delete anyone except other super admins
            if ($user->role === 'super_admin') {
                return $targetUser->role !== 'super_admin';
            }

            // Admin can only delete regular users
            if ($user->role === 'admin') {
                return in_array($targetUser->role, ['user', 'member', 'resident']);
            }

            return false;
    }

    public function approveUser(?User $user)
    {
            return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin']));
    }

    public function bulkDeleteUsers(?User $user)
    {
            return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin']));
    }

    public function manageUserStatus(?User $user, ?User $targetUser = null)
    {
            if (! $user || ! in_array($user->role, ['admin', 'super_admin'])) {
                return false;
            }

            // If no target user specified, allow for bulk operations
            if (! $targetUser) {
                return true;
            }

            // Cannot change own status
            if ($user->id === $targetUser->id) {
                return false;
            }

            // Super admin can change anyone's status except other super admins
            if ($user->role === 'super_admin') {
                return $targetUser->role !== 'super_admin';
            }

            // Admin can only change regular user status
            if ($user->role === 'admin') {
                return in_array($targetUser->role, ['user', 'member', 'resident']);
            }

            return false;
    }

    public function viewUser(?User $user, ?User $targetUser = null)
    {
            if (! $user) {
                return false;
            }

            // Users can view their own profile
            if ($targetUser && $user->id === $targetUser->id) {
                return true;
            }

            // Admins can view any user
            return in_array($user->role, ['admin', 'super_admin']);
    }

    public function editUser(?User $user, User $targetUser)
    {
            if (! $user || ! in_array($user->role, ['admin', 'super_admin'])) {
                return false;
            }

            // Cannot edit self through this interface
            if ($user->id === $targetUser->id) {
                return false;
            }

            // Super admin can edit anyone except other super admins
            if ($user->role === 'super_admin') {
                return $targetUser->role !== 'super_admin';
            }

            // Admin can only edit regular users
            if ($user->role === 'admin') {
                return in_array($targetUser->role, ['user', 'member', 'resident']);
            }

            return false;
    }

    public function banUser(?User $user, User $targetUser)
    {
            if (! $user || ! in_array($user->role, ['admin', 'super_admin'])) {
                return false;
            }

            // Cannot ban self
            if ($user->id === $targetUser->id) {
                return false;
            }

            // Super admin can ban anyone except other super admins
            if ($user->role === 'super_admin') {
                return $targetUser->role !== 'super_admin';
            }

            // Admin can only ban regular users
            return in_array($targetUser->role, ['user', 'member', 'resident']);
    }

    public function assignSuperAdminRole(?User $user)
    {
            return $user && $user->role === 'super_admin';
    }

    public function assignAdminRole(?User $user)
    {
            return $user && in_array($user->role, ['super_admin']);
    }

    public function assignOperatorRole(?User $user)
    {
            return $user && in_array($user->role, ['admin', 'super_admin']);
    }

    public function exportUsers(?User $user)
    {
            return $user && ($user->role === 'super_admin' || in_array($user->role, ['admin']));
    }

}
