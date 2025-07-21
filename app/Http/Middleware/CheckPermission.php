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
        if (!$permission) {
            return $next($request);
        }

        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Authentication required');
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
