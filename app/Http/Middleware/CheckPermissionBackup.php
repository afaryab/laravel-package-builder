<?php

namespace LaravelApp\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LaravelApp\Models\Permission;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        // If auth is disabled, check if this is an auth-related route that should be blocked
        if (config('auth.type') === 'none') {
            if ($this->isAuthRelatedRoute($request, $permission)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'This functionality is disabled when authentication is turned off.',
                        'error' => 'AUTH_DISABLED'
                    ], 403);
                }
                
                return redirect()->route('dashboard')->with('error', 
                    'User management and access control features are disabled when authentication is turned off.'
                );
            }
            
            // Allow all other requests when auth is disabled
            return $next($request);
        }

        if (!$permission) {
            return $next($request);
        }

        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Access denied');
        }
    }
    
    /**
     * Check if the route is auth-related and should be blocked when auth=none
     */
    private function isAuthRelatedRoute(Request $request, ?string $permission): bool
    {
        $route = $request->route();
        $routeName = $route ? $route->getName() : '';
        $routeUri = $request->getRequestUri();
        
        // Check if permission is auth-related
        $authRelatedPermissions = [
            'users.view',
            'users.create', 
            'users.edit',
            'users.delete',
            'roles.view',
            'roles.edit',
            'roles.create',
            'roles.delete',
        ];
        
        if ($permission && in_array($permission, $authRelatedPermissions)) {
            return true;
        }
        
        // Check route names
        $blockedRouteNames = [
            'users.index',
            'users.create', 
            'users.store',
            'users.show',
            'users.edit',
            'users.update',
            'users.destroy',
            'users.password',
            'users.reset-password',
            'admin.users',
            'access-control.index',
            'access-control.users',
            'access-control.roles',
            'access-control.permissions',
            'access-control.users.role',
        ];
        
        if (in_array($routeName, $blockedRouteNames)) {
            return true;
        }
        
        // Check URI patterns
        $blockedUriPatterns = [
            '/settings/users',
            '/settings/access-control',
            '/admin/users',
        ];
        
        foreach ($blockedUriPatterns as $pattern) {
            if (str_starts_with($routeUri, $pattern)) {
                return true;
            }
        }
        
        return false;
    }
        }

        // Auto-create permission if it doesn't exist (as requested)
        if (!Permission::where('name', $permission)->exists()) {
            Permission::findOrCreate(
                $permission,
                ucwords(str_replace(['.', '_'], ' ', $permission)),
                'Auto-generated permission',
                explode('.', $permission)[0] ?? 'general'
            );
        }

        if (!$user->hasPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Insufficient permissions',
                    'required_permission' => $permission
                ], 403);
            }

            return redirect()->back()->with('error', 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
