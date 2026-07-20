<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Add your model policies here
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        $this->registerGates();
    }

    /**
     * Register custom gates for authorization.
     */
    protected function registerGates(): void
    {
        // Database-driven permissions system
        Gate::before(function ($user, $ability) {
            // Allow if no user (for public access)
            if (! $user) {
                return null; // Continue to individual gates
            }

            // User must be active for most operations
            if (! $user->isActive()) {
                // But allow some basic operations even for inactive users
                $allowedForInactive = ['account-active', 'access-system', 'view-profile'];
                if (! in_array($ability, $allowedForInactive)) {
                    return false;
                }
            }

            // Check if user has explicit permission in database first
            if (method_exists($user, 'hasPermission')) {
                try {
                    $userPermission = $user->permissions()
                        ->where('permissions.name', $ability)
                        ->first();

                    if ($userPermission) {
                        // If explicitly granted or denied, use that
                        if ($userPermission->pivot->type === 'grant') {
                            return true;
                        }

                        if ($userPermission->pivot->type === 'deny') {
                            return false;
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Permission check failed: '.$e->getMessage());
                }
            }

            // Super admin fallback - has access unless explicitly denied
            if (isset($user->role) && $user->role === 'super_admin') {
                return true;
            }

            // Continue to check individual gates below for fallback
            return null;
        });

        // Define database permissions if they don't exist
        $this->defineDefaultPermissions();

        // Assign default permissions to roles
        $this->assignDefaultRolePermissions();

        // Legacy gates (fallback for role-based system)
        // System Access Gates
        Gate::define('access-system', [\App\Gates\SystemGate::class, 'accessSystem']);

        Gate::define('account-active', [\App\Gates\SystemGate::class, 'accountActive']);

        Gate::define('account-verified', [\App\Gates\SystemGate::class, 'accountVerified']);

        // Registration Gates
        Gate::define('register-account', function () {
            // Allow registration by default, but can be configured
            return config('auth.allow_registration', true);
        });

        // Role-based Access Gates
        Gate::define('access-admin-panel', [\App\Gates\UserGate::class, 'accessAdminPanel']);

        Gate::define('access-user-dashboard', [\App\Gates\UserGate::class, 'accessUserDashboard']);

        Gate::define('is-admin', [\App\Gates\UserGate::class, 'isAdmin']);

        Gate::define('is-super-admin', [\App\Gates\UserGate::class, 'isSuperAdmin']);

        Gate::define('is-user', [\App\Gates\UserGate::class, 'isUser']);

        // Profile Management Gates
        Gate::define('view-profile', [\App\Gates\UserGate::class, 'viewProfile']);

        Gate::define('update-profile', [\App\Gates\UserGate::class, 'updateProfile']);

        Gate::define('change-password', [\App\Gates\UserGate::class, 'changePassword']);

        // User Management Gates
        Gate::define('manage-users', [\App\Gates\UserGate::class, 'manageUsers']);

        Gate::define('create-user', [\App\Gates\UserGate::class, 'createUser']);

        Gate::define('update-user', [\App\Gates\UserGate::class, 'updateUser']);

        Gate::define('delete-user', [\App\Gates\UserGate::class, 'deleteUser']);

        Gate::define('approve-user', [\App\Gates\UserGate::class, 'approveUser']);

        Gate::define('bulk-delete-users', [\App\Gates\UserGate::class, 'bulkDeleteUsers']);

        Gate::define('manage-user-status', [\App\Gates\UserGate::class, 'manageUserStatus']);

        Gate::define('view-user', [\App\Gates\UserGate::class, 'viewUser']);

        Gate::define('edit-user', [\App\Gates\UserGate::class, 'editUser']);

        Gate::define('ban-user', [\App\Gates\UserGate::class, 'banUser']);

        // Role Assignment Gates
        Gate::define('assign-super-admin-role', [\App\Gates\UserGate::class, 'assignSuperAdminRole']);

        Gate::define('assign-admin-role', [\App\Gates\UserGate::class, 'assignAdminRole']);

        Gate::define('assign-operator-role', [\App\Gates\UserGate::class, 'assignOperatorRole']);

        // Export Gates
        Gate::define('export-users', [\App\Gates\UserGate::class, 'exportUsers']);

        Gate::define('export-content', [\App\Gates\ContentGate::class, 'exportContent']);

        // Content Management Gates
        Gate::define('manage-content', [\App\Gates\ContentGate::class, 'manageContent']);

        Gate::define('publish-content', [\App\Gates\ContentGate::class, 'publishContent']);

        Gate::define('moderate-content', [\App\Gates\ContentGate::class, 'moderateContent']);

        Gate::define('view-content', [\App\Gates\ContentGate::class, 'viewContent']);

        Gate::define('edit-content', [\App\Gates\ContentGate::class, 'editContent']);

        Gate::define('delete-content', [\App\Gates\ContentGate::class, 'deleteContent']);

        // Data Management Gates
        Gate::define('manage-village-data', [\App\Gates\DataGate::class, 'manageVillageData']);

        Gate::define('manage-population-data', [\App\Gates\DataGate::class, 'managePopulationData']);

        Gate::define('manage-budget-data', [\App\Gates\DataGate::class, 'manageBudgetData']);

        Gate::define('view-sensitive-data', [\App\Gates\DataGate::class, 'viewSensitiveData']);

        // Communication Gates
        Gate::define('manage-contact-messages', [\App\Gates\ServiceGate::class, 'manageContactMessages']);

        Gate::define('reply-contact-messages', [\App\Gates\ServiceGate::class, 'replyContactMessages']);

        Gate::define('send-notifications', [\App\Gates\ServiceGate::class, 'sendNotifications']);

        // System Configuration Gates
        Gate::define('manage-settings', [\App\Gates\SystemGate::class, 'manageSettings']);

        Gate::define('manage-system-backup', [\App\Gates\SystemGate::class, 'manageSystemBackup']);

        Gate::define('view-system-logs', [\App\Gates\SystemGate::class, 'viewSystemLogs']);

        Gate::define('manage-permissions', [\App\Gates\SystemGate::class, 'managePermissions']);

        // Activity Logging Gates
        Gate::define('log-user-activity', function () {
            // Always allow activity logging for security
            return true;
        });

        Gate::define('view-activity-logs', [\App\Gates\SystemGate::class, 'viewActivityLogs']);

        // Service Management Gates
        Gate::define('manage-services', [\App\Gates\ServiceGate::class, 'manageServices']);

        Gate::define('process-service-requests', [\App\Gates\ServiceGate::class, 'processServiceRequests']);

        // Letter Template Management Gates
        Gate::define('manage.letter_templates', [\App\Gates\ServiceGate::class, 'manageLetterTemplates']);

        Gate::define('letter_templates.view', [\App\Gates\ServiceGate::class, 'letterTemplatesView']);

        Gate::define('letter_templates.create', [\App\Gates\ServiceGate::class, 'letterTemplatesCreate']);

        Gate::define('letter_templates.edit', [\App\Gates\ServiceGate::class, 'letterTemplatesEdit']);

        Gate::define('letter_templates.delete', [\App\Gates\ServiceGate::class, 'letterTemplatesDelete']);

        // Report Generation Gates
        Gate::define('generate-reports', [\App\Gates\ServiceGate::class, 'generateReports']);

        Gate::define('export-data', [\App\Gates\ServiceGate::class, 'exportData']);

        // Special Permission Gates
        Gate::define('impersonate-user', [\App\Gates\SystemGate::class, 'impersonateUser']);

        Gate::define('access-maintenance-mode', [\App\Gates\SystemGate::class, 'accessMaintenanceMode']);

        // Time-based Gates
        Gate::define('login-during-maintenance', [\App\Gates\SystemGate::class, 'loginDuringMaintenance']);

        // IP-based Gates (example)
        Gate::define('admin-ip-restriction', [\App\Gates\SystemGate::class, 'adminIpRestriction']);

        // Logs and Monitoring Gates
        Gate::define('view-logs', [\App\Gates\SystemGate::class, 'viewLogs']);

        Gate::define('view-activity-logs', [\App\Gates\SystemGate::class, 'viewActivityLogs']);

        Gate::define('view-system-info', [\App\Gates\SystemGate::class, 'viewSystemInfo']);

        Gate::define('clear-logs', [\App\Gates\SystemGate::class, 'clearLogs']);

        // Additional gates for specific features
        Gate::define('manage-locations', [\App\Gates\DataGate::class, 'manageLocations']);

        Gate::define('manage-village-budget', [\App\Gates\DataGate::class, 'manageVillageBudget']);
    }

    /**
     * Define default permissions in database if they don't exist
     */
    protected function defineDefaultPermissions(): void
    {
        if (! class_exists(Permission::class)) {
            return;
        }

        try {
            $defaultPermissions = $this->getDefaultPermissions();

            foreach ($defaultPermissions as $category => $permissions) {
                foreach ($permissions as $permission) {
                    Permission::firstOrCreate(
                        ['name' => $permission['name']],
                        [
                            'display_name' => $permission['display_name'],
                            'description' => $permission['description'],
                            'category' => $category,
                            'is_active' => true,
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            // Silently fail if database is not ready
            \Log::error('Failed to define default permissions: '.$e->getMessage());
        }
    }

    /**
     * Get default permission definitions
     */
    protected function getDefaultPermissions(): array
    {
        return [
            'system' => [
                ['name' => 'access-admin-panel', 'display_name' => 'Akses Panel Admin', 'description' => 'Akses ke panel administrasi'],
                ['name' => 'manage-settings', 'display_name' => 'Kelola Pengaturan', 'description' => 'Mengelola pengaturan sistem'],
                ['name' => 'view-system-info', 'display_name' => 'Lihat Info Sistem', 'description' => 'Melihat informasi sistem'],
                ['name' => 'manage-system-backup', 'display_name' => 'Kelola Backup Sistem', 'description' => 'Mengelola backup sistem'],
                ['name' => 'view-logs', 'display_name' => 'Lihat Log Sistem', 'description' => 'Melihat log sistem'],
                ['name' => 'clear-logs', 'display_name' => 'Hapus Log', 'description' => 'Menghapus log sistem'],
            ],
            'users' => [
                ['name' => 'manage-users', 'display_name' => 'Kelola Pengguna', 'description' => 'Mengelola data pengguna'],
                ['name' => 'create-user', 'display_name' => 'Buat Pengguna', 'description' => 'Membuat pengguna baru'],
                ['name' => 'update-user', 'display_name' => 'Update Pengguna', 'description' => 'Mengupdate data pengguna'],
                ['name' => 'delete-user', 'display_name' => 'Hapus Pengguna', 'description' => 'Menghapus pengguna'],
                ['name' => 'view-user', 'display_name' => 'Lihat Pengguna', 'description' => 'Melihat detail pengguna'],
                ['name' => 'manage-user-status', 'display_name' => 'Kelola Status Pengguna', 'description' => 'Mengubah status pengguna'],
                ['name' => 'export-users', 'display_name' => 'Ekspor Pengguna', 'description' => 'Mengekspor data pengguna'],
            ],
            'content' => [
                ['name' => 'manage-content', 'display_name' => 'Kelola Konten', 'description' => 'Mengelola konten website'],
                ['name' => 'publish-content', 'display_name' => 'Publikasi Konten', 'description' => 'Mempublikasikan konten'],
                ['name' => 'moderate-content', 'display_name' => 'Moderasi Konten', 'description' => 'Melakukan moderasi konten'],
                ['name' => 'view-content', 'display_name' => 'Lihat Konten', 'description' => 'Melihat konten'],
                ['name' => 'edit-content', 'display_name' => 'Edit Konten', 'description' => 'Mengedit konten'],
                ['name' => 'delete-content', 'display_name' => 'Hapus Konten', 'description' => 'Menghapus konten'],
            ],
            'village_data' => [
                ['name' => 'manage-village-data', 'display_name' => 'Kelola Data Desa', 'description' => 'Mengelola data desa'],
                ['name' => 'manage-population-data', 'display_name' => 'Kelola Data Penduduk', 'description' => 'Mengelola data penduduk'],
                ['name' => 'manage-village-budget', 'display_name' => 'Kelola Anggaran Desa', 'description' => 'Mengelola anggaran desa'],
                ['name' => 'manage-locations', 'display_name' => 'Kelola Lokasi', 'description' => 'Mengelola data lokasi'],
                ['name' => 'view-sensitive-data', 'display_name' => 'Lihat Data Sensitif', 'description' => 'Melihat data sensitif'],
            ],
            'communication' => [
                ['name' => 'manage-contact-messages', 'display_name' => 'Kelola Pesan Kontak', 'description' => 'Mengelola pesan kontak'],
                ['name' => 'reply-contact-messages', 'display_name' => 'Balas Pesan Kontak', 'description' => 'Membalas pesan kontak'],
                ['name' => 'send-notifications', 'display_name' => 'Kirim Notifikasi', 'description' => 'Mengirim notifikasi'],
            ],
            'services' => [
                ['name' => 'manage-services', 'display_name' => 'Kelola Layanan', 'description' => 'Mengelola layanan desa'],
                ['name' => 'process-service-requests', 'display_name' => 'Proses Permintaan Layanan', 'description' => 'Memproses permintaan layanan'],
                ['name' => 'manage-letter-templates', 'display_name' => 'Kelola Template Surat', 'description' => 'Mengelola template surat'],
            ],
            'reports' => [
                ['name' => 'generate-reports', 'display_name' => 'Generate Laporan', 'description' => 'Membuat laporan'],
                ['name' => 'export-data', 'display_name' => 'Ekspor Data', 'description' => 'Mengekspor data'],
                ['name' => 'view-activity-logs', 'display_name' => 'Lihat Log Aktivitas', 'description' => 'Melihat log aktivitas'],
            ],
            'permissions' => [
                ['name' => 'manage-permissions', 'display_name' => 'Kelola Permission', 'description' => 'Mengelola hak akses pengguna'],
                ['name' => 'assign-super-admin-role', 'display_name' => 'Assign Super Admin', 'description' => 'Memberikan role super admin'],
                ['name' => 'assign-admin-role', 'display_name' => 'Assign Admin', 'description' => 'Memberikan role admin'],
            ],
        ];
    }

    /**
     * Assign default permissions to roles
     */
    protected function assignDefaultRolePermissions(): void
    {
        try {
            Role::firstOrCreate(
                ['name' => 'super_admin'],
                [
                    'display_name' => 'Super Administrator',
                    'description' => 'Has access to all system features',
                    'is_active' => true,
                ]
            );

            $adminRole = Role::firstOrCreate(
                ['name' => 'admin'],
                [
                    'display_name' => 'Administrator',
                    'description' => 'Has access to most administrative features',
                    'is_active' => true,
                ]
            );

            // Super Admin gets all permissions (handled by Gate::before)
            // Admin gets specific permissions
            $adminPermissions = [
                'access-admin-panel', 'manage-users', 'create-user', 'view-user', 'update-user',
                'manage-content', 'publish-content', 'view-content', 'edit-content',
                'manage-village-data', 'manage-population-data', 'manage-locations',
                'manage-contact-messages', 'reply-contact-messages',
                'generate-reports', 'export-data', 'view-activity-logs',
            ];

            $permissions = Permission::whereIn('name', $adminPermissions)->get();
            $adminRole->permissions()->syncWithoutDetaching($permissions);
        } catch (\Exception $e) {
            \Log::error('Failed to assign default role permissions: '.$e->getMessage());
        }
    }
}
