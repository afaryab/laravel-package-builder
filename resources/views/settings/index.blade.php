@extends('layouts.dashboard')

@section('title', 'Settings')

@section('content')
<div class="bg-white shadow-sm rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
        <p class="mt-1 text-sm text-gray-600">Manage system configuration and user settings</p>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Users Management Card -->
            @permission('users.view')
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-blue-500 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Users Management</h3>
                            <p class="text-sm text-gray-600">Manage system users and permissions</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Total Users</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ \LaravelApp\Models\User::count() }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Active Users</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ \LaravelApp\Models\User::whereNotNull('email_verified_at')->count() }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Recent Registrations</span>
                            <span class="text-sm text-gray-600">
                                {{ \LaravelApp\Models\User::where('created_at', '>=', now()->subDays(7))->count() }} this week
                            </span>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('users.index') }}" 
                           class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Manage Users
                        </a>
                    </div>
                </div>
            @endpermission

            <!-- Access Control Card -->
            @permission('roles.view')
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <i class="fas fa-shield-alt text-purple-500 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Access Control</h3>
                            <p class="text-sm text-gray-600">Manage roles and permissions</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Active Roles</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ \LaravelApp\Models\Role::where('is_active', true)->count() }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Total Permissions</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ \LaravelApp\Models\Permission::where('is_active', true)->count() }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Default Role</span>
                            <span class="text-sm text-gray-600">
                                {{ \LaravelApp\Models\Role::where('is_default', true)->first()?->display_name ?? 'None' }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('access-control.index') }}" 
                           class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            Manage Access Control
                        </a>
                    </div>
                </div>
            @endpermission

            <!-- System Setup Card -->
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <i class="fas fa-tools text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">System Setup</h3>
                        <p class="text-sm text-gray-600">Configure authentication and integrations</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Authentication Type</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ ucfirst(config('auth.type', 'internal')) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Active Tokens</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ auth()->user()?->tokens()?->count() ?? 0 }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">OAuth Enabled</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ config('services.google.client_id') ? 'green' : 'gray' }}-100 text-{{ config('services.google.client_id') ? 'green' : 'gray' }}-800">
                            {{ config('services.google.client_id') ? 'Yes' : 'No' }}
                        </span>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="{{ route('setup') }}" 
                       class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Configure Setup
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @permission('users.create')
                    <a href="{{ route('users.create') }}" class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div>
                            <span class="rounded-lg inline-flex p-3 bg-blue-50 text-blue-700 ring-4 ring-white">
                                <i class="fas fa-user-plus"></i>
                            </span>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-sm font-medium text-gray-900">
                                Add New User
                            </h3>
                            <p class="mt-2 text-sm text-gray-500">
                                Create a new user account with system access
                            </p>
                        </div>
                    </a>
                @endpermission

                @permission('settings.access')
                    <a href="{{ route('authentication') }}" class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div>
                            <span class="rounded-lg inline-flex p-3 bg-green-50 text-green-700 ring-4 ring-white">
                                <i class="fas fa-key"></i>
                            </span>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-sm font-medium text-gray-900">
                                Configure Auth
                            </h3>
                            <p class="mt-2 text-sm text-gray-500">
                                Set up authentication providers and security
                            </p>
                        </div>
                    </a>
                @endpermission

                @permission('tokens.view')
                    <a href="{{ route('tokens') }}" class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div>
                            <span class="rounded-lg inline-flex p-3 bg-purple-50 text-purple-700 ring-4 ring-white">
                                <i class="fas fa-plug"></i>
                            </span>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-sm font-medium text-gray-900">
                                API Tokens
                            </h3>
                            <p class="mt-2 text-sm text-gray-500">
                                Manage integration tokens and API access
                            </p>
                        </div>
                    </a>
                @endpermission

                @permission('roles.create')
                    <a href="{{ route('access-control.roles.create') }}" class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div>
                            <span class="rounded-lg inline-flex p-3 bg-indigo-50 text-indigo-700 ring-4 ring-white">
                                <i class="fas fa-shield-alt"></i>
                            </span>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-sm font-medium text-gray-900">
                                Create Role
                            </h3>
                            <p class="mt-2 text-sm text-gray-500">
                                Define new user roles and permissions
                            </p>
                        </div>
                    </a>
                @endpermission
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="mt-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    @php
                        $recentUsers = \LaravelApp\Models\User::latest()->limit(5)->get();
                    @endphp
                    @forelse($recentUsers as $user)
                        <li>
                            <div class="px-4 py-4 flex items-center justify-between">
                                <div class="flex items-center">
                                    <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=667eea&color=fff" alt="{{ $user->name }}">
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $user->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </li>
                    @empty
                        <li>
                            <div class="px-4 py-4 text-center text-gray-500">
                                No recent activity
                            </div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
