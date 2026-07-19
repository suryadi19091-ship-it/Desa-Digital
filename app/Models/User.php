<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, LogsActivity, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'nik',
        'phone',
        'address',
        'avatar',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];



    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly(['last_login_at', 'password']);
    }

    /**
     * Get user's role relationship
     */
    public function roleObject()
    {
        return $this->belongsTo(Role::class, 'role', 'name');
    }

    /**
     * Get user's direct permissions
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
            ->withPivot('type')
            ->withTimestamps();
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        // Super admin has all permissions unless explicitly denied
        if ($this->role === 'super_admin') {
            $userPermission = $this->permissions()
                ->where('permissions.name', $permission)
                ->first();

            if ($userPermission && $userPermission->pivot->type === 'deny') {
                return false; // Explicit deny overrides super admin
            }

            return true; // Super admin default
        }

        // Check direct user permissions first (explicit grant/deny)
        $userPermission = $this->permissions()
            ->where('permissions.name', $permission)
            ->first();

        if ($userPermission) {
            return $userPermission->pivot->type === 'grant';
        }

        // Check role permissions
        $roleObject = $this->roleObject;
        if ($roleObject) {
            return $roleObject->hasPermission($permission);
        }

        return false;
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Grant permission to user
     */
    public function grantPermission(string $permissionName)
    {
        $permission = Permission::where('name', $permissionName)->first();
        if ($permission) {
            $this->permissions()->syncWithoutDetaching([
                $permission->id => ['type' => 'grant'],
            ]);
        }
    }

    /**
     * Deny permission to user
     */
    public function denyPermission(string $permissionName)
    {
        $permission = Permission::where('name', $permissionName)->first();
        if ($permission) {
            $this->permissions()->syncWithoutDetaching([
                $permission->id => ['type' => 'deny'],
            ]);
        }
    }

    /**
     * Remove permission from user
     */
    public function removePermission(string $permissionName)
    {
        $permission = Permission::where('name', $permissionName)->first();
        if ($permission) {
            $this->permissions()->detach($permission->id);
        }
    }

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return Storage::url($this->avatar);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }
}
