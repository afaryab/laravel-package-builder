<?php

namespace LaravelApp\Http\Controllers;

use Illuminate\Http\Request;
use LaravelApp\Models\User;
use LaravelApp\Models\Role;
use LaravelApp\Models\Permission;

class AccessControlController extends Controller
{
    /**
     * Display the access control dashboard
     */
    public function index()
    {
        $totalUsers = User::count();
        $totalRoles = Role::where('is_active', true)->count();
        $totalPermissions = Permission::where('is_active', true)->count();
        
        $recentUsers = User::with('role')
            ->latest()
            ->take(5)
            ->get();

        $rolesWithUserCount = Role::withCount('users')
            ->where('is_active', true)
            ->orderBy('display_name')
            ->get();

        return view('settings.access-control.index', compact(
            'totalUsers', 
            'totalRoles', 
            'totalPermissions', 
            'recentUsers', 
            'rolesWithUserCount'
        ));
    }

    /**
     * Display user role assignments
     */
    public function users()
    {
        $users = User::with('role')->paginate(15);
        $roles = Role::where('is_active', true)->orderBy('display_name')->get();

        return view('settings.access-control.users', compact('users', 'roles'));
    }

    /**
     * Update user's role
     */
    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $user->update(['role_id' => $request->role_id]);

        return redirect()->back()->with('success', 'User role updated successfully.');
    }

    /**
     * Display role permissions matrix
     */
    public function permissions()
    {
        $roles = Role::with('permissions')
            ->where('is_active', true)
            ->orderBy('display_name')
            ->get();

        $permissions = Permission::where('is_active', true)
            ->orderBy('group')
            ->orderBy('display_name')
            ->get()
            ->groupBy('group');

        return view('settings.access-control.permissions', compact('roles', 'permissions'));
    }

    /**
     * Update role permissions
     */
    public function updateRolePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->back()->with('success', 'Role permissions updated successfully.');
    }

    /**
     * Bulk update multiple role permissions
     */
    public function bulkUpdatePermissions(Request $request)
    {
        $request->validate([
            'role_permissions' => 'required|array'
        ]);

        foreach ($request->role_permissions as $roleId => $permissions) {
            $role = Role::find($roleId);
            if ($role && is_array($permissions)) {
                // Validate each permission ID
                $validPermissions = Permission::whereIn('id', $permissions)->pluck('id')->toArray();
                $role->permissions()->sync($validPermissions);
            }
        }

        return redirect()->back()->with('success', 'All role permissions updated successfully.');
    }

    /**
     * Toggle permission status
     */
    public function togglePermission(Permission $permission)
    {
        $permission->update(['is_active' => !$permission->is_active]);

        return redirect()->back()->with('success', 'Permission status updated successfully.');
    }

    /**
     * Toggle role status
     */
    public function toggleRole(Role $role)
    {
        if ($role->is_default) {
            return redirect()->back()->with('error', 'Cannot deactivate default role.');
        }

        $role->update(['is_active' => !$role->is_active]);

        return redirect()->back()->with('success', 'Role status updated successfully.');
    }
}
