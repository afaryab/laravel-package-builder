<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use LaravelApp\Models\Role;
use LaravelApp\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->seedPermissions();
        $this->seedRoles();
        $this->assignPermissionsToRoles();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all role-permission associations
        DB::table('role_permissions')->truncate();
        
        // Remove all roles except those that might have users
        Role::whereNotIn('name', ['admin', 'user'])->delete();
        
        // Optionally remove permissions (comment out if you want to keep them)
        Permission::truncate();
    }

    /**
     * Seed all permissions into the database.
     */
    private function seedPermissions(): void
    {
        $permissions = [
            // User Management
            ['name' => 'users.view', 'display_name' => 'View Users', 'description' => 'View user list and details', 'group' => 'users'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'description' => 'Create new users', 'group' => 'users'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'description' => 'Edit user information', 'group' => 'users'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'description' => 'Delete users', 'group' => 'users'],
            
            // Role Management
            ['name' => 'roles.view', 'display_name' => 'View Roles', 'description' => 'View roles and permissions', 'group' => 'roles'],
            ['name' => 'roles.create', 'display_name' => 'Create Roles', 'description' => 'Create new roles', 'group' => 'roles'],
            ['name' => 'roles.edit', 'display_name' => 'Edit Roles', 'description' => 'Edit roles and permissions', 'group' => 'roles'],
            ['name' => 'roles.delete', 'display_name' => 'Delete Roles', 'description' => 'Delete roles', 'group' => 'roles'],
            
            // Token Management
            ['name' => 'tokens.view', 'display_name' => 'View Tokens', 'description' => 'View API tokens', 'group' => 'tokens'],
            ['name' => 'tokens.create', 'display_name' => 'Create Tokens', 'description' => 'Generate new API tokens', 'group' => 'tokens'],
            ['name' => 'tokens.edit', 'display_name' => 'Edit Tokens', 'description' => 'Edit API tokens', 'group' => 'tokens'],
            ['name' => 'tokens.delete', 'display_name' => 'Delete Tokens', 'description' => 'Revoke API tokens', 'group' => 'tokens'],
            
            // Dashboard Access
            ['name' => 'dashboard.view', 'display_name' => 'View Dashboard', 'description' => 'Access admin dashboard', 'group' => 'dashboard'],
            ['name' => 'dashboard.activities', 'display_name' => 'View Activities', 'description' => 'View system activities', 'group' => 'dashboard'],
            
            // Settings
            ['name' => 'settings.view', 'display_name' => 'View Settings', 'description' => 'Access settings pages', 'group' => 'settings'],
            ['name' => 'settings.edit', 'display_name' => 'Edit Settings', 'description' => 'Modify system settings', 'group' => 'settings'],
            ['name' => 'settings.access', 'display_name' => 'Access Settings', 'description' => 'Access advanced settings', 'group' => 'settings'],
            
            // Admin Access
            ['name' => 'admin.access', 'display_name' => 'Admin Access', 'description' => 'Access admin panel', 'group' => 'admin'],
            ['name' => 'admin.dashboard', 'display_name' => 'Admin Dashboard', 'description' => 'Access admin dashboard', 'group' => 'admin'],
            
            // API Access
            ['name' => 'api.access', 'display_name' => 'API Access', 'description' => 'General API access', 'group' => 'api'],
            ['name' => 'api.users', 'display_name' => 'API User Access', 'description' => 'Access user data via API', 'group' => 'api'],
            ['name' => 'api.tokens', 'display_name' => 'API Token Management', 'description' => 'Manage tokens via API', 'group' => 'api'],
            
            // Profile Management
            ['name' => 'profile.view', 'display_name' => 'View Profile', 'description' => 'View own profile', 'group' => 'profile'],
            ['name' => 'profile.edit', 'display_name' => 'Edit Profile', 'description' => 'Edit own profile', 'group' => 'profile'],
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                [
                    'display_name' => $permissionData['display_name'],
                    'description' => $permissionData['description'],
                    'group' => $permissionData['group'],
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * Seed default roles into the database.
     */
    private function seedRoles(): void
    {
        // Create Admin Role
        Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Full system access with all permissions',
                'is_default' => false,
                'is_active' => true,
            ]
        );

        // Create User Role
        Role::firstOrCreate(
            ['name' => 'user'],
            [
                'display_name' => 'Regular User',
                'description' => 'Basic user with limited permissions',
                'is_default' => true,
                'is_active' => true,
            ]
        );

        // Create API User Role
        Role::firstOrCreate(
            ['name' => 'api_user'],
            [
                'display_name' => 'API User',
                'description' => 'User with API access permissions',
                'is_default' => false,
                'is_active' => true,
            ]
        );
    }

    /**
     * Assign permissions to roles.
     */
    private function assignPermissionsToRoles(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();
        $apiRole = Role::where('name', 'api_user')->first();

        // Admin gets ALL permissions
        if ($adminRole) {
            $allPermissions = Permission::where('is_active', true)->pluck('id')->toArray();
            $adminRole->permissions()->sync($allPermissions);
        }

        // Regular User gets basic permissions
        if ($userRole) {
            $userPermissions = Permission::whereIn('name', [
                'dashboard.view',
                'dashboard.activities',
                'settings.view',
                'tokens.view',
                'tokens.create',
                'profile.view',
                'profile.edit',
            ])->pluck('id')->toArray();
            $userRole->permissions()->sync($userPermissions);
        }

        // API User gets API-related permissions
        if ($apiRole) {
            $apiPermissions = Permission::whereIn('name', [
                'api.access',
                'api.users',
                'api.tokens',
                'tokens.view',
                'tokens.create',
                'tokens.edit',
                'dashboard.view',
                'profile.view',
                'profile.edit',
            ])->pluck('id')->toArray();
            $apiRole->permissions()->sync($apiPermissions);
        }
    }
};
