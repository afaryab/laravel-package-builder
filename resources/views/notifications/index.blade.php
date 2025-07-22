@extends('layouts.dashboard')

@section('title', 'Notifications')

@section('content')
<div class="bg-white shadow-sm rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
        <div class="flex space-x-3">
            @if((auth()->user()?->unread_notifications_count ?? 0) > 0)
                <button type="button" 
                        onclick="markAllAsRead()"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-check-double mr-2"></i>
                    Mark All Read
                </button>
            @endif
            <button type="button" 
                    onclick="clearReadNotifications()"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-trash mr-2"></i>
                Clear Read
            </button>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8">
            <a href="{{ route('notifications.index', ['filter' => 'all']) }}" 
               class="filter-tab {{ request('filter', 'all') == 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                All Notifications
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" 
               class="filter-tab {{ request('filter') == 'unread' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Unread
                @if((auth()->user()?->unread_notifications_count ?? 0) > 0)
                    <span class="ml-2 bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                        {{ auth()->user()?->unread_notifications_count ?? 0 }}
                    </span>
                @endif
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'read']) }}" 
               class="filter-tab {{ request('filter') == 'read' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Read
            </a>
        </nav>
    </div>

    <!-- Notifications List -->
    <div id="notifications-container">
        @include('notifications.partials.list', ['notifications' => $notifications])
    </div>
</div>

<script>
function markAllAsRead() {
    if (confirm('Mark all notifications as read?')) {
        fetch('{{ route("notifications.mark-all-as-read") }}', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function clearReadNotifications() {
    if (confirm('Delete all read notifications? This cannot be undone.')) {
        fetch('{{ route("notifications.clear-read") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function markAsRead(notificationId) {
    fetch(`{{ route("notifications.index") }}/${notificationId}/mark-as-read`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI to show as read
            const notification = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notification) {
                notification.classList.remove('bg-blue-50', 'border-blue-200');
                notification.classList.add('bg-white');
                const unreadIndicator = notification.querySelector('.unread-indicator');
                if (unreadIndicator) {
                    unreadIndicator.remove();
                }
            }
            // Update unread count
            updateUnreadCount(data.unread_count);
        }
    })
    .catch(error => console.error('Error:', error));
}

function deleteNotification(notificationId) {
    if (confirm('Delete this notification?')) {
        fetch(`{{ route("notifications.index") }}/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove notification from UI
                const notification = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (notification) {
                    notification.remove();
                }
                // Update unread count
                updateUnreadCount(data.unread_count);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function updateUnreadCount(count) {
    // Update any unread count displays
    const countElements = document.querySelectorAll('.unread-count');
    countElements.forEach(element => {
        element.textContent = count;
        if (count === 0) {
            element.style.display = 'none';
        }
    });
}
</script>
@endsection
