<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use LaravelApp\Models\Role;
use LaravelApp\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions
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
            ['name' => 'tokens.delete', 'display_name' => 'Delete Tokens', 'description' => 'Revoke API tokens', 'group' => 'tokens'],
            
            // Dashboard Access
            ['name' => 'dashboard.view', 'display_name' => 'View Dashboard', 'description' => 'Access admin dashboard', 'group' => 'dashboard'],
            ['name' => 'dashboard.activities', 'display_name' => 'View Activities', 'description' => 'View system activities', 'group' => 'dashboard'],
            
            // Settings
            ['name' => 'settings.view', 'display_name' => 'View Settings', 'description' => 'Access settings pages', 'group' => 'settings'],
            ['name' => 'settings.edit', 'display_name' => 'Edit Settings', 'description' => 'Modify system settings', 'group' => 'settings'],
            
            // API Access
            ['name' => 'api.users', 'display_name' => 'API User Access', 'description' => 'Access user data via API', 'group' => 'api'],
            ['name' => 'api.tokens', 'display_name' => 'API Token Management', 'description' => 'Manage tokens via API', 'group' => 'api'],
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate(
                $permission['name'],
                $permission['display_name'],
                $permission['description'],
                $permission['group']
            );
        }

        // Create Roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Full system access with all permissions',
                'is_default' => false,
                'is_active' => true,
            ]
        );

        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            [
                'display_name' => 'Regular User',
                'description' => 'Basic user with limited permissions',
                'is_default' => true,
                'is_active' => true,
            ]
        );

        $apiRole = Role::firstOrCreate(
            ['name' => 'api_user'],
            [
                'display_name' => 'API User',
                'description' => 'User with API access permissions',
                'is_default' => false,
                'is_active' => true,
            ]
        );

        // Assign all permissions to admin role
        $allPermissions = Permission::all();
        $adminRole->syncPermissions($allPermissions->pluck('id')->toArray());

        // Assign basic permissions to user role
        $userPermissions = Permission::whereIn('name', [
            'dashboard.view',
            'dashboard.activities',
            'settings.view',
            'tokens.view',
            'tokens.create',
        ])->get();
        $userRole->syncPermissions($userPermissions->pluck('id')->toArray());

        // Assign API permissions to api_user role
        $apiPermissions = Permission::whereIn('name', [
            'api.users',
            'api.tokens',
            'tokens.view',
            'tokens.create',
        ])->get();
        $apiRole->syncPermissions($apiPermissions->pluck('id')->toArray());
    }
}
