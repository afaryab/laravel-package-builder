@if($notifications->count() > 0)
    <div class="space-y-4">
        @foreach($notifications as $notification)
            <div data-notification-id="{{ $notification->id }}" 
                 class="notification-item relative border rounded-lg p-4 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50 border-blue-200' }}">
                
                @if(!$notification->read_at)
                    <div class="unread-indicator absolute top-3 left-3 w-2 h-2 bg-blue-500 rounded-full"></div>
                @endif
                
                <div class="flex justify-between items-start {{ !$notification->read_at ? 'ml-5' : '' }}">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            <!-- Type Icon -->
                            <div class="notification-icon">
                                @switch($notification->type)
                                    @case('success')
                                        <i class="fas fa-check-circle text-green-500"></i>
                                        @break
                                    @case('info')
                                        <i class="fas fa-info-circle text-blue-500"></i>
                                        @break
                                    @case('warning')
                                        <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                                        @break
                                    @case('error')
                                        <i class="fas fa-times-circle text-red-500"></i>
                                        @break
                                    @case('security')
                                        <i class="fas fa-shield-alt text-purple-500"></i>
                                        @break
                                    @case('password_reset')
                                        <i class="fas fa-key text-orange-500"></i>
                                        @break
                                    @case('system')
                                        <i class="fas fa-cog text-gray-500"></i>
                                        @break
                                    @default
                                        <i class="fas fa-bell text-gray-500"></i>
                                @endswitch
                            </div>
                            
                            <!-- Priority Badge -->
                            @if($notification->priority !== 'normal')
                                <span class="priority-badge px-2 py-1 text-xs font-medium rounded-full
                                    {{ $notification->priority === 'high' ? 'bg-red-100 text-red-800' : 
                                       ($notification->priority === 'low' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($notification->priority) }}
                                </span>
                            @endif
                            
                            <!-- Type Badge -->
                            <span class="type-badge px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                {{ str_replace('_', ' ', ucfirst($notification->type)) }}
                            </span>
                        </div>
                        
                        <!-- Title -->
                        <h3 class="font-semibold text-gray-900 mb-1">{{ $notification->title }}</h3>
                        
                        <!-- Message -->
                        <p class="text-gray-700 mb-2">{{ $notification->message }}</p>
                        
                        <!-- Additional Data -->
                        @if($notification->data && is_array($notification->data) && count($notification->data) > 0)
                            <div class="additional-data mt-2 p-3 bg-gray-50 rounded-md">
                                @foreach($notification->data as $key => $value)
                                    @if($key !== 'action_url' && $key !== 'action_text')
                                        <div class="text-sm text-gray-600">
                                            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                            @if(is_array($value))
                                                {{ json_encode($value) }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                        
                        <!-- Action Button -->
                        @if($notification->data && isset($notification->data['action_url']) && isset($notification->data['action_text']))
                            <div class="mt-3">
                                <a href="{{ $notification->data['action_url'] }}" 
                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    {{ $notification->data['action_text'] }}
                                </a>
                            </div>
                        @endif
                        
                        <!-- Timestamp -->
                        <div class="mt-3 text-xs text-gray-500">
                            <i class="fas fa-clock mr-1"></i>
                            {{ $notification->created_at->diffForHumans() }}
                            @if($notification->read_at)
                                <span class="ml-2">
                                    <i class="fas fa-eye mr-1"></i>
                                    Read {{ $notification->read_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex items-center space-x-2 ml-4">
                        @if(!$notification->read_at)
                            <button type="button" 
                                    onclick="markAsRead({{ $notification->id }})"
                                    class="p-2 text-gray-400 hover:text-blue-600 focus:outline-none"
                                    title="Mark as read">
                                <i class="fas fa-check"></i>
                            </button>
                        @endif
                        
                        <button type="button" 
                                onclick="deleteNotification({{ $notification->id }})"
                                class="p-2 text-gray-400 hover:text-red-600 focus:outline-none"
                                title="Delete notification">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->appends(request()->query())->links() }}
        </div>
    @endif
@else
    <div class="text-center py-12">
        <i class="fas fa-bell-slash text-gray-300 text-6xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications</h3>
        <p class="text-gray-500">
            @if(request('filter') == 'unread')
                You don't have any unread notifications.
            @elseif(request('filter') == 'read')
                You don't have any read notifications.
            @else
                You don't have any notifications yet.
            @endif
        </p>
    </div>
@endif
