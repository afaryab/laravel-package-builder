<?php

namespace LaravelApp\Http\Controllers\Api;

use LaravelApp\Http\Controllers\Controller;
use LaravelApp\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    /**
     * Get all users (admin only)
     */
    public function getUsers(): JsonResponse
    {
        Gate::authorize('viewApiDocs'); // Using the same gate for admin access
        
        $users = User::select('id', 'name', 'email', 'auth_type', 'created_at')->get();
        
        return response()->json([
            'success' => true,
            'data' => $users,
            'meta' => [
                'total' => $users->count(),
                'timestamp' => now()->toISOString()
            ]
        ]);
    }
    
    /**
     * Get authenticated user's information
     */
    public function getAuthenticatedUser(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Auth::user(),
            'meta' => [
                'timestamp' => now()->toISOString()
            ]
        ]);
    }
    
    /**
     * Get application statistics
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            'total_users' => User::count(),
            'auth_type' => config('auth.type', 'none'),
            'environment' => app()->environment(),
            'app_name' => config('app.name'),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats,
            'meta' => [
                'timestamp' => now()->toISOString()
            ]
        ]);
    }
}
