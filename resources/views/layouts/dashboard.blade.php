<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - {{ config('application.app.name', config('app.name', 'Laravel')) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-shadow {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        .metric-card {
            transition: all 0.3s ease;
        }
        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body class="bg-gray-50" x-data="{ sidebarOpen: false }">
    @php
        $appConfig = app(\LaravelApp\Services\ApplicationConfigService::class);
        $backendMenus = $appConfig->getBackendMenus();
        $breadcrumb = $appConfig->getBreadcrumb();
    @endphp
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64">
                <div class="flex flex-col h-0 flex-1 gradient-bg">
                    <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                        <div class="flex items-center flex-shrink-0 px-4">
                            <div class="flex items-center">
                                <div class="bg-white bg-opacity-20 rounded-lg p-2">
                                    @php
                                        $logoIcon = config('application.app.logo_icon', 'fas fa-cube');
                                        // Check if it's a Font Awesome icon or a Blade icon
                                        if (str_starts_with($logoIcon, 'fa') || str_contains($logoIcon, 'fa-')) {
                                            echo '<i class="' . $logoIcon . ' text-white text-xl"></i>';
                                        } else {
                                            echo '<x-' . $logoIcon . ' class="w-6 h-6 text-white" />';
                                        }
                                    @endphp
                                </div>
                                <h1 class="ml-3 text-xl font-bold text-white">{{ config('application.app.logo_text', config('app.name', 'App')) }}</h1>
                            </div>
                        </div>
                        <nav class="mt-10 px-4">
                            @foreach($backendMenus as $key => $menu)
                                @if(isset($menu['submenu']) && count($menu['submenu']) > 0)
                                    <!-- Nested Menu with Submenu -->
                                    <div x-data="{ open: {{ $appConfig->isActiveRoute($menu) ? 'true' : 'false' }} }" class="{{ $loop->first ? '' : 'mt-1' }}">
                                        <button @click="open = !open" 
                                                class="group w-full flex items-center justify-between px-4 py-3 text-base font-medium rounded-md {{ $appConfig->isActiveRoute($menu) ? 'text-white bg-indigo-800' : 'text-indigo-100 hover:text-white hover:bg-indigo-800' }}"
                                                title="{{ $menu['description'] ?? $menu['label'] }}">
                                            <div class="flex items-center">
                                                <i class="{{ $menu['icon'] }} mr-4"></i>
                                                {{ $menu['label'] }}
                                            </div>
                                            <x-heroicon-o-chevron-down class="w-4 h-4 transition-transform duration-200" 
                                               x-bind:class="open ? 'transform rotate-180' : ''" />
                                        </button>
                                        
                                        <div x-show="open" 
                                             x-transition:enter="transition ease-out duration-200" 
                                             x-transition:enter-start="opacity-0 transform scale-95" 
                                             x-transition:enter-end="opacity-100 transform scale-100"
                                             x-transition:leave="transition ease-in duration-75" 
                                             x-transition:leave-start="opacity-100 transform scale-100" 
                                             x-transition:leave-end="opacity-0 transform scale-95"
                                             class="mt-1 ml-4 space-y-1">
                                            @foreach($menu['submenu'] as $subKey => $submenu)
                                                <a href="{{ route($submenu['route']) }}" 
                                                   class="group flex items-center px-4 py-2 text-sm font-medium rounded-md {{ $appConfig->isActiveRoute($submenu) ? 'text-white bg-indigo-700' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}"
                                                   title="{{ $submenu['description'] ?? $submenu['label'] }}">
                                                    <i class="{{ $submenu['icon'] }} mr-3 text-xs"></i>
                                                    {{ $submenu['label'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <!-- Simple Menu Item -->
                                    <a href="{{ route($menu['route']) }}" 
                                       class="group flex items-center px-4 py-3 {{ $loop->first ? '' : 'mt-1' }} text-base font-medium rounded-md {{ $appConfig->isActiveRoute($menu) ? 'text-white bg-indigo-800' : 'text-indigo-100 hover:text-white hover:bg-indigo-800' }}"
                                       title="{{ $menu['description'] ?? $menu['label'] }}">
                                        <i class="{{ $menu['icon'] }} mr-4"></i>
                                        {{ $menu['label'] }}
                                    </a>
                                @endif
                            @endforeach
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile sidebar -->
        <div x-show="sidebarOpen" class="fixed inset-0 flex z-40 md:hidden" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click="sidebarOpen = false" class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
            <div class="relative flex-1 flex flex-col max-w-xs w-full gradient-bg">
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button @click="sidebarOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <x-heroicon-o-x-mark class="w-6 h-6 text-white" />
                    </button>
                </div>
                <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                    <div class="flex items-center flex-shrink-0 px-4">
                        <div class="flex items-center">
                            <div class="bg-white bg-opacity-20 rounded-lg p-2">
                                @php
                                    $logoIcon = config('application.app.logo_icon', 'fas fa-cube');
                                    // Check if it's a Font Awesome icon or a Blade icon
                                    if (str_starts_with($logoIcon, 'fa') || str_contains($logoIcon, 'fa-')) {
                                        echo '<i class="' . $logoIcon . ' text-white text-xl"></i>';
                                    } else {
                                        echo '<x-' . $logoIcon . ' class="w-6 h-6 text-white" />';
                                    }
                                @endphp
                            </div>
                            <h1 class="ml-3 text-xl font-bold text-white">{{ config('application.app.logo_text', config('app.name', 'App')) }}</h1>
                        </div>
                    </div>
                    <nav class="mt-10 px-4">
                        @foreach($backendMenus as $key => $menu)
                            @if(isset($menu['submenu']) && count($menu['submenu']) > 0)
                                <!-- Nested Menu with Submenu (Mobile) -->
                                <div x-data="{ open: {{ $appConfig->isActiveRoute($menu) ? 'true' : 'false' }} }" class="{{ $loop->first ? '' : 'mt-1' }}">
                                    <button @click="open = !open" 
                                            class="group w-full flex items-center justify-between px-4 py-3 text-base font-medium rounded-md {{ $appConfig->isActiveRoute($menu) ? 'text-white bg-indigo-800' : 'text-indigo-100 hover:text-white hover:bg-indigo-800' }}"
                                            title="{{ $menu['description'] ?? $menu['label'] }}">
                                        <div class="flex items-center">
                                            <i class="{{ $menu['icon'] }} mr-4"></i>
                                            {{ $menu['label'] }}
                                        </div>
                                        <x-heroicon-o-chevron-down class="w-4 h-4 transition-transform duration-200" 
                                           x-bind:class="open ? 'transform rotate-180' : ''" />
                                    </button>
                                    
                                    <div x-show="open" 
                                         x-transition:enter="transition ease-out duration-200" 
                                         x-transition:enter-start="opacity-0 transform scale-95" 
                                         x-transition:enter-end="opacity-100 transform scale-100"
                                         x-transition:leave="transition ease-in duration-75" 
                                         x-transition:leave-start="opacity-100 transform scale-100" 
                                         x-transition:leave-end="opacity-0 transform scale-95"
                                         class="mt-1 ml-4 space-y-1">
                                        @foreach($menu['submenu'] as $subKey => $submenu)
                                            <a href="{{ route($submenu['route']) }}" 
                                               class="group flex items-center px-4 py-2 text-sm font-medium rounded-md {{ $appConfig->isActiveRoute($submenu) ? 'text-white bg-indigo-700' : 'text-indigo-200 hover:text-white hover:bg-indigo-700' }}"
                                               title="{{ $submenu['description'] ?? $submenu['label'] }}">
                                                <i class="{{ $submenu['icon'] }} mr-3 text-xs"></i>
                                                {{ $submenu['label'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <!-- Simple Menu Item (Mobile) -->
                                <a href="{{ route($menu['route']) }}" 
                                   class="group flex items-center px-4 py-3 {{ $loop->first ? '' : 'mt-1' }} text-base font-medium rounded-md {{ $appConfig->isActiveRoute($menu) ? 'text-white bg-indigo-800' : 'text-indigo-100 hover:text-white hover:bg-indigo-800' }}"
                                   title="{{ $menu['description'] ?? $menu['label'] }}">
                                    <i class="{{ $menu['icon'] }} mr-4"></i>
                                    {{ $menu['label'] }}
                                </a>
                            @endif
                        @endforeach
                    </nav>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <!-- Top navigation -->
            <div class="relative z-10 flex-shrink-0 flex h-16 bg-white shadow">
                <div class="flex-1 px-4 flex justify-between items-center">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = true" class="md:hidden text-gray-500 hover:text-gray-900">
                            <x-heroicon-o-bars-3 class="w-5 h-5" />
                        </button>
                        <div class="ml-4 flex items-center md:ml-6">
                            <div class="relative">
                                <input type="text" placeholder="Search" class="bg-gray-100 rounded-full py-2 px-4 pl-10 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white">
                                <x-heroicon-o-magnifying-glass class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" />
                            </div>
                        </div>
                    </div>
                    <div class="ml-4 flex items-center md:ml-6">
                        <!-- Notifications Dropdown -->
                        <div class="relative mr-3" x-data="{ open: false, unreadCount: {{ auth()->user()->unread_notifications_count }} }">
                            <button @click="open = !open; if (open) loadNotifications()" 
                                    class="relative bg-gray-100 p-2 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <x-heroicon-o-bell class="w-5 h-5" />
                                <span x-show="unreadCount > 0" 
                                      x-text="unreadCount" 
                                      class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                </span>
                            </button>
                            
                            <div x-show="open" 
                                 @click.away="open = false" 
                                 x-transition 
                                 class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <div class="px-4 py-3 border-b border-gray-200">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
                                            <button @click="markAllAsRead()" 
                                                    x-show="unreadCount > 0"
                                                    class="text-xs text-blue-600 hover:text-blue-800">
                                                Mark all read
                                            </button>
                                        </div>
                                    </div>
                                    <div id="notifications-dropdown-content">
                                        <div class="px-4 py-8 text-center">
                                            <x-heroicon-o-arrow-path class="w-5 h-5 animate-spin text-gray-400 mx-auto" />
                                            <p class="text-sm text-gray-500 mt-2">Loading...</p>
                                        </div>
                                    </div>
                                    <div class="border-t border-gray-200">
                                        <a href="{{ route('notifications.index') }}" 
                                           class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 text-center">
                                            <x-heroicon-o-bell class="w-4 h-4 inline mr-2" />
                                            View All Notifications
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ml-3 relative" x-data="{ open: false }">
                            <div>
                                <button @click="open = !open" class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=667eea&color=fff" alt="{{ auth()->user()->name ?? 'User' }}">
                                    <span class="ml-3 text-gray-700 text-sm font-medium">{{ auth()->user()->name ?? 'User' }}</span>
                                    <x-heroicon-o-chevron-down class="ml-2 w-3 h-3 text-gray-400" />
                                </button>
                            </div>
                            <div x-show="open" @click.away="open = false" x-transition class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                <div class="py-1">
                                    <!-- Profile Section with Submenu -->
                                    <div class="relative">
                                        <a href="{{ route('profile.index') }}" class="w-full flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <div class="flex items-center">
                                                <x-heroicon-o-user class="w-4 h-4 mr-2" />
                                                Profile
                                            </div>
                                            <x-heroicon-o-chevron-right class="w-3 h-3 text-gray-400" />
                                        </a>
                                    </div>
                                    
                                    <!-- Divider -->
                                    <div class="border-t border-gray-100 my-1"></div>
                                    
                                    <!-- Settings -->
                                    <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <x-heroicon-o-cog-6-tooth class="w-4 h-4 mr-2" />
                                        Settings
                                    </a>
                                    
                                    <!-- Divider -->
                                    <div class="border-t border-gray-100 my-1"></div>
                                    
                                    <!-- Logout -->
                                    <form method="POST" action="{{ route('logout') }}" class="block">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4 mr-2" />
                                            Sign out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page content -->
            <main class="flex-1 relative overflow-y-auto focus:outline-none">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
    // Notification functions
    function loadNotifications() {
        fetch('{{ route("notifications.recent") }}?limit=5')
            .then(response => response.json())
            .then(data => {
                document.getElementById('notifications-dropdown-content').innerHTML = data.html;
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                document.getElementById('notifications-dropdown-content').innerHTML = 
                    '<div class="px-4 py-8 text-center"><p class="text-sm text-red-500">Error loading notifications</p></div>';
            });
    }

    function markAllAsRead() {
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
                // Update unread count
                const dropdown = document.querySelector('[x-data*="unreadCount"]');
                if (dropdown && dropdown.__x) {
                    dropdown.__x.$data.unreadCount = 0;
                }
                // Reload notifications
                loadNotifications();
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Auto-refresh notifications every 30 seconds
    setInterval(() => {
        fetch('{{ route("notifications.unread-count") }}')
            .then(response => response.json())
            .then(data => {
                const dropdown = document.querySelector('[x-data*="unreadCount"]');
                if (dropdown && dropdown.__x) {
                    dropdown.__x.$data.unreadCount = data.count;
                }
            })
            .catch(error => console.error('Error refreshing unread count:', error));
    }, 30000);
    </script>
</body>
</html>
