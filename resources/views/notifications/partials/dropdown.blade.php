@if($notifications->count() > 0)
    <div class="max-h-96 overflow-y-auto">
        @foreach($notifications as $notification)
            <div class="notification-dropdown-item relative px-4 py-3 hover:bg-gray-50 {{ !$notification->read_at ? 'bg-blue-50' : '' }}">
                @if(!$notification->read_at)
                    <div class="absolute top-4 left-2 w-2 h-2 bg-blue-500 rounded-full"></div>
                @endif
                
                <div class="flex items-start {{ !$notification->read_at ? 'ml-4' : '' }}">
                    <!-- Type Icon -->
                    <div class="flex-shrink-0 mr-3 mt-1">
                        @switch($notification->type)
                            @case('success')
                                <i class="fas fa-check-circle text-green-500 text-sm"></i>
                                @break
                            @case('info')
                                <i class="fas fa-info-circle text-blue-500 text-sm"></i>
                                @break
                            @case('warning')
                                <i class="fas fa-exclamation-triangle text-yellow-500 text-sm"></i>
                                @break
                            @case('error')
                                <i class="fas fa-times-circle text-red-500 text-sm"></i>
                                @break
                            @case('security')
                                <i class="fas fa-shield-alt text-purple-500 text-sm"></i>
                                @break
                            @case('password_reset')
                                <i class="fas fa-key text-orange-500 text-sm"></i>
                                @break
                            @case('system')
                                <i class="fas fa-cog text-gray-500 text-sm"></i>
                                @break
                            @default
                                <i class="fas fa-bell text-gray-500 text-sm"></i>
                        @endswitch
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-900 truncate">
                            {{ $notification->title }}
                        </div>
                        <div class="text-sm text-gray-500 mt-1">
                            {{ Str::limit($notification->message, 80) }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                            {{ $notification->created_at->diffForHumans() }}
                        </div>
                    </div>
                    
                    @if($notification->priority === 'high')
                        <div class="flex-shrink-0 ml-2">
                            <span class="inline-block w-2 h-2 bg-red-500 rounded-full"></span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    
    <div class="border-t border-gray-200 px-4 py-3">
        <a href="{{ route('notifications.index') }}" 
           class="text-sm text-blue-600 hover:text-blue-800 font-medium">
            View all notifications
        </a>
    </div>
@else
    <div class="px-4 py-8 text-center">
        <i class="fas fa-bell-slash text-gray-300 text-2xl mb-2"></i>
        <p class="text-sm text-gray-500">No notifications</p>
    </div>
@endif
