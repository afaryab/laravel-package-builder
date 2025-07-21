<?php

namespace LaravelApp\Services;

use LaravelApp\Models\Notification;
use LaravelApp\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Create a new notification for a user.
     */
    public function create(
        User $user,
        string $type,
        string $title,
        string $message,
        array $options = []
    ): Notification {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $options['data'] ?? null,
            'action_url' => $options['action_url'] ?? null,
            'action_text' => $options['action_text'] ?? null,
            'priority' => $options['priority'] ?? 'normal',
            'expires_at' => $options['expires_at'] ?? null,
        ]);
    }

    /**
     * Create a success notification.
     */
    public function success(User $user, string $title, string $message, array $options = []): Notification
    {
        return $this->create($user, 'success', $title, $message, $options);
    }

    /**
     * Create an info notification.
     */
    public function info(User $user, string $title, string $message, array $options = []): Notification
    {
        return $this->create($user, 'info', $title, $message, $options);
    }

    /**
     * Create a warning notification.
     */
    public function warning(User $user, string $title, string $message, array $options = []): Notification
    {
        return $this->create($user, 'warning', $title, $message, $options);
    }

    /**
     * Create an error notification.
     */
    public function error(User $user, string $title, string $message, array $options = []): Notification
    {
        return $this->create($user, 'error', $title, $message, $options);
    }

    /**
     * Create a system notification.
     */
    public function system(User $user, string $title, string $message, array $options = []): Notification
    {
        return $this->create($user, 'system', $title, $message, $options);
    }

    /**
     * Get notifications for a user.
     */
    public function getForUser(User $user, int $limit = 10): Collection
    {
        return $user->notifications()
            ->active()
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread notifications for a user.
     */
    public function getUnreadForUser(User $user, int $limit = 10): Collection
    {
        return $user->notifications()
            ->unread()
            ->active()
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread count for a user.
     */
    public function getUnreadCount(User $user): int
    {
        return $user->notifications()
            ->unread()
            ->active()
            ->count();
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Notification $notification): bool
    {
        $notification->markAsRead();
        return true;
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(User $user): int
    {
        return $user->notifications()
            ->unread()
            ->update(['read_at' => now()]);
    }

    /**
     * Delete notification.
     */
    public function delete(Notification $notification): bool
    {
        return $notification->delete();
    }

    /**
     * Clean up expired notifications.
     */
    public function cleanupExpired(): int
    {
        return Notification::where('expires_at', '<', now())->delete();
    }

    /**
     * Send notification for password reset.
     */
    public function passwordReset(User $user, string $temporaryPassword): Notification
    {
        return $this->warning(
            $user,
            'Password Reset',
            "Your password has been reset. Temporary password: {$temporaryPassword}",
            [
                'priority' => 'high',
                'action_url' => route('settings'),
                'action_text' => 'Change Password',
                'data' => ['type' => 'password_reset']
            ]
        );
    }

    /**
     * Send notification for successful password change.
     */
    public function passwordChanged(User $user): Notification
    {
        return $this->success(
            $user,
            'Password Changed',
            'Your password has been successfully updated.',
            [
                'data' => ['type' => 'password_change']
            ]
        );
    }

    /**
     * Send notification for user creation.
     */
    public function userCreated(User $user): Notification
    {
        return $this->info(
            $user,
            'Welcome!',
            'Your account has been created successfully. Please complete your profile setup.',
            [
                'action_url' => route('settings'),
                'action_text' => 'Complete Profile',
                'data' => ['type' => 'user_creation']
            ]
        );
    }

    /**
     * Send notification for login from new device.
     */
    public function newDeviceLogin(User $user, string $device, string $location): Notification
    {
        return $this->warning(
            $user,
            'New Device Login',
            "A login was detected from a new device: {$device} in {$location}. If this wasn't you, please secure your account.",
            [
                'priority' => 'high',
                'action_url' => route('settings'),
                'action_text' => 'Secure Account',
                'data' => ['type' => 'security_alert', 'device' => $device, 'location' => $location]
            ]
        );
    }

    /**
     * Send system maintenance notification.
     */
    public function systemMaintenance(User $user, string $scheduledTime): Notification
    {
        return $this->system(
            $user,
            'Scheduled Maintenance',
            "System maintenance is scheduled for {$scheduledTime}. Some features may be temporarily unavailable.",
            [
                'expires_at' => now()->addDays(7),
                'data' => ['type' => 'maintenance', 'scheduled_time' => $scheduledTime]
            ]
        );
    }
}
