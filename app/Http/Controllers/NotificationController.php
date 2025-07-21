<?php

namespace LaravelApp\Http\Controllers;

use Illuminate\Http\Request;
use LaravelApp\Models\Notification;
use LaravelApp\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of the user's notifications.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get query parameters
        $filter = $request->input('filter', 'all'); // all, unread, read
        $type = $request->input('type');
        $priority = $request->input('priority');
        
        // Build query
        $query = $user->notifications()->latest();
        
        // Apply filters
        if ($filter === 'unread') {
            $query->unread();
        } elseif ($filter === 'read') {
            $query->read();
        }
        
        if ($type) {
            $query->where('type', $type);
        }
        
        if ($priority) {
            $query->where('priority', $priority);
        }
        
        $notifications = $query->paginate(20);
        
        if ($request->ajax()) {
            return response()->json([
                'html' => view('notifications.partials.list', compact('notifications'))->render(),
                'unread_count' => $user->unread_notifications_count
            ]);
        }
        
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Get unread notifications count for AJAX requests.
     */
    public function getUnreadCount(): JsonResponse
    {
        $count = auth()->user()->unread_notifications_count;
        
        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications for dropdown.
     */
    public function getRecent(Request $request): JsonResponse
    {
        $user = auth()->user();
        $limit = $request->input('limit', 5);
        
        $notifications = $user->notifications()
            ->latest()
            ->limit($limit)
            ->get();
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->unread_notifications_count,
            'html' => view('notifications.partials.dropdown', compact('notifications'))->render()
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification): JsonResponse
    {
        // Check if the notification belongs to the authenticated user
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $notification->markAsRead();
        
        return response()->json([
            'success' => true,
            'unread_count' => auth()->user()->unread_notifications_count
        ]);
    }

    /**
     * Mark multiple notifications as read.
     */
    public function markMultipleAsRead(Request $request): JsonResponse
    {
        $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'integer|exists:notifications,id'
        ]);
        
        $user = auth()->user();
        
        $user->notifications()
            ->whereIn('id', $request->notification_ids)
            ->update(['read_at' => now()]);
        
        return response()->json([
            'success' => true,
            'unread_count' => $user->unread_notifications_count
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        auth()->user()->notifications()->unread()->update(['read_at' => now()]);
        
        return response()->json([
            'success' => true,
            'unread_count' => 0
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(Notification $notification): JsonResponse
    {
        // Check if the notification belongs to the authenticated user
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $notification->delete();
        
        return response()->json([
            'success' => true,
            'unread_count' => auth()->user()->unread_notifications_count
        ]);
    }

    /**
     * Delete multiple notifications.
     */
    public function destroyMultiple(Request $request): JsonResponse
    {
        $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'integer|exists:notifications,id'
        ]);
        
        $user = auth()->user();
        
        $user->notifications()
            ->whereIn('id', $request->notification_ids)
            ->delete();
        
        return response()->json([
            'success' => true,
            'unread_count' => $user->unread_notifications_count
        ]);
    }

    /**
     * Clear all read notifications.
     */
    public function clearRead(): JsonResponse
    {
        auth()->user()->notifications()->read()->delete();
        
        return response()->json([
            'success' => true,
            'unread_count' => auth()->user()->unread_notifications_count
        ]);
    }

    /**
     * Clear all notifications.
     */
    public function clearAll(): JsonResponse
    {
        auth()->user()->notifications()->delete();
        
        return response()->json([
            'success' => true,
            'unread_count' => 0
        ]);
    }

    /**
     * Test notification creation (for development/testing).
     */
    public function test(Request $request): JsonResponse
    {
        if (app()->environment() !== 'local') {
            return response()->json(['error' => 'Only available in local environment'], 403);
        }
        
        $type = $request->input('type', 'info');
        $message = $request->input('message', 'Test notification message');
        $title = $request->input('title', 'Test Notification');
        
        $notification = $this->notificationService->create(
            auth()->user(),
            $type,
            $title,
            $message,
            ['test' => true]
        );
        
        return response()->json([
            'success' => true,
            'notification' => $notification,
            'unread_count' => auth()->user()->unread_notifications_count
        ]);
    }
}
