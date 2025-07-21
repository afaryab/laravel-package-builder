<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sharia Finance Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-shadow { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        .metric-card { transition: all 0.3s ease; }
        .metric-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px -8px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64">
                <div class="flex flex-col h-0 flex-1 gradient-bg">
                    <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                        <div class="flex items-center flex-shrink-0 px-4">
                            <div class="flex items-center">
                                <div class="bg-white bg-opacity-20 rounded-lg p-2">
                                    <i class="fas fa-mosque text-white text-xl"></i>
                                </div>
                                <h1 class="ml-3 text-xl font-bold text-white">Sharia</h1>
                            </div>
                        </div>
                                                <nav class="mt-10">
                            <a href="{{ route('dashboard') }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('dashboard') ? 'text-white bg-indigo-800' : 'text-indigo-100 hover:text-white hover:bg-indigo-800' }}">
                                <i class="fas fa-chart-line mr-4"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('goals') }}" class="group flex items-center px-2 py-2 mt-1 text-base font-medium rounded-md {{ request()->routeIs('goals') ? 'text-white bg-indigo-800' : 'text-indigo-100 hover:text-white hover:bg-indigo-800' }}">
                                <i class="fas fa-bullseye mr-4"></i>
                                Goals
                            </a>
                            <a href="#" class="group flex items-center px-2 py-2 mt-1 text-base font-medium rounded-md text-indigo-100 hover:text-white hover:bg-indigo-800">
                                <i class="fas fa-exchange-alt mr-4"></i>
                                Transactions
                            </a>
                            <a href="#" class="group flex items-center px-2 py-2 mt-1 text-base font-medium rounded-md text-indigo-100 hover:text-white hover:bg-indigo-800">
                                <i class="fas fa-chart-pie mr-4"></i>
                                Analytics
                            </a>
                            <a href="{{ route('settings') }}" class="group flex items-center px-2 py-2 mt-1 text-base font-medium rounded-md {{ request()->routeIs('settings') ? 'text-white bg-indigo-800' : 'text-indigo-100 hover:text-white hover:bg-indigo-800' }}">
                                <i class="fas fa-cog mr-4"></i>
                                Settings
                            </a>
                        </nav>
                        <div class="flex-shrink-0 px-4 py-4">
                            <div class="bg-white bg-opacity-10 rounded-lg p-4">
                                <h3 class="text-white font-medium text-sm mb-2">Invite your friend by referral code</h3>
                                <p class="text-white text-opacity-80 text-xs mb-3">Maximize rewards - Share your unique referral code for exclusive benefits</p>
                                <button class="w-full bg-white text-indigo-600 text-sm font-medium py-2 px-4 rounded-lg hover:bg-gray-100 transition">
                                    Get Code
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <!-- Top navigation -->
            <div class="relative z-10 flex-shrink-0 flex h-16 bg-white shadow">
                <div class="flex-1 px-4 flex justify-between items-center">
                    <div class="flex items-center">
                        <button class="md:hidden text-gray-500 hover:text-gray-900">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <div class="ml-4 flex items-center md:ml-6">
                            <div class="relative">
                                <input type="text" placeholder="Search" class="bg-gray-100 rounded-full py-2 px-4 pl-10 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white">
                                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                            </div>
                        </div>
                    </div>
                    <div class="ml-4 flex items-center md:ml-6">
                        <button class="bg-gray-100 p-2 rounded-full text-gray-400 hover:text-gray-500 mr-3">
                            <i class="fas fa-bell"></i>
                        </button>
                        <div class="ml-3 relative" x-data="{ open: false }">
                            <div>
                                <button @click="open = !open" class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name=Matt+Shadows&background=667eea&color=fff" alt="Matt Shadows">
                                    <span class="ml-3 text-gray-700 text-sm font-medium">Matt Shadows</span>
                                    <i class="fas fa-chevron-down ml-2 text-gray-400 text-xs"></i>
                                </button>
                            </div>
                            <div x-show="open" @click.away="open = false" x-transition class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                <div class="py-1">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Your Profile</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                                    <a href="/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign out</a>
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

    <!-- Mobile sidebar overlay -->
    <div x-show="sidebarOpen" class="fixed inset-0 flex z-40 md:hidden" x-data="{ sidebarOpen: false }">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
        <!-- Mobile sidebar content would go here -->
    </div>
</body>
</html>
