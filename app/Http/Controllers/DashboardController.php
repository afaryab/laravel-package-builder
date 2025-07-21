<?php

namespace LaravelApp\Http\Controllers;

use LaravelApp\Http\Controllers\Controller;
use LaravelApp\Models\Activity;
use LaravelApp\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     */
    public function index()
    {
        try {
            $users = User::all();
            $totalUsers = $users->count();
            
            // Calculate growth percentage (last 30 days)
            $previousCount = User::where('created_at', '<', now()->subDays(30))->count();
            $growthPercentage = $previousCount > 0 
                ? round((($totalUsers - $previousCount) / $previousCount) * 100, 1) 
                : 0;
            
            // Get total tokens using a safer method
            $totalTokens = 0;
            try {
                $totalTokens = DB::table('personal_access_tokens')->count();
            } catch (\Exception $e) {
                // Fallback if personal_access_tokens table doesn't exist
                $totalTokens = 0;
            }
            
            // Get activities instead of users for recent activity
            $activities = Activity::with('user')
                ->latest()
                ->take(50)  // Increased to show more activities and ensure all types are represented
                ->get()
                ->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'type' => ucfirst($activity->type),
                        'description' => $activity->description,
                        'user' => $activity->user ? $activity->user->name : 'System',
                        'created_at' => $activity->created_at->diffForHumans(),
                        'details' => $activity->details,
                        'ip_address' => $activity->ip_address,
                        'status' => $activity->response_status ?? 'N/A'
                    ];
                });
            
            return view('dashboard', [
                'totalUsers' => $totalUsers,
                'growthPercentage' => $growthPercentage,
                'totalTokens' => $totalTokens,
                'activities' => $activities
            ]);
        } catch (\Exception $e) {
            // Fallback for development/testing
            return view('dashboard', [
                'totalUsers' => 0,
                'growthPercentage' => 0,
                'totalTokens' => 0,
                'activities' => collect([])
            ]);
        }
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities(Request $request)
    {
        try {
            $type = $request->get('type', 'recent'); // recent, users, integrations, system
            
            $query = Activity::with('user')->latest();
            
            // Filter based on type
            if ($type === 'users') {
                $query->where('type', Activity::TYPE_USER);
            } elseif ($type === 'integrations') {
                $query->where('type', Activity::TYPE_INTEGRATION);
            } elseif ($type === 'system') {
                $query->where('type', Activity::TYPE_SYSTEM);
            }
            
            $activities = $query->take(50)
                ->get()
                ->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'type' => ucfirst($activity->type),
                        'description' => $activity->description,
                        'user' => $activity->user ? $activity->user->name : 'System',
                        'created_at' => $activity->created_at->diffForHumans(),
                        'details' => $activity->details,
                        'ip_address' => $activity->ip_address,
                        'status' => $activity->response_status ?? 'N/A'
                    ];
                });
            
            return response()->json($activities);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }
}
