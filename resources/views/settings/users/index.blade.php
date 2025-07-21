@extends('layouts.dashboard')

@section('title', 'Users Management')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Users Management</h1>
            <p class="text-gray-600 mt-1">Manage system users and their permissions</p>
        </div>
        <a href="{{ route('users.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium inline-flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Add New User
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Stats -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-users text-indigo-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-500">Total Users</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $users->total() }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-lg bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-user text-gray-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-500">Internal Auth</div>
                    <div class="text-2xl font-bold text-gray-900">{{ \LaravelApp\Models\User::where('auth_type', 'internal')->orWhereNull('auth_type')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-globe text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-500">OAuth Auth</div>
                    <div class="text-2xl font-bold text-gray-900">{{ \LaravelApp\Models\User::where('auth_type', 'oauth')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-lg bg-green-100 flex items-center justify-center">
                        <i class="fas fa-certificate text-green-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-500">SAML Auth</div>
                    <div class="text-2xl font-bold text-gray-900">{{ \LaravelApp\Models\User::where('auth_type', 'saml')->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow mb-6">
        <form method="GET" action="{{ route('users.index') }}" class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Name, email, or auth type..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Auth Type Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Authentication Type</label>
                    <select name="auth_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Types</option>
                        <option value="internal" {{ request('auth_type') === 'internal' ? 'selected' : '' }}>Internal</option>
                        <option value="oauth" {{ request('auth_type') === 'oauth' ? 'selected' : '' }}>OAuth</option>
                        <option value="saml" {{ request('auth_type') === 'saml' ? 'selected' : '' }}>SAML</option>
                    </select>
                </div>

                <!-- Sort By -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    <select name="sort" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Created Date</option>
                        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name</option>
                        <option value="email" {{ request('sort') === 'email' ? 'selected' : '' }}>Email</option>
                        <option value="updated_at" {{ request('sort') === 'updated_at' ? 'selected' : '' }}>Last Updated</option>
                    </select>
                </div>

                <!-- Sort Direction -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Direction</label>
                    <select name="direction" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Descending</option>
                        <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Ascending</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex justify-between items-center">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium">
                    <i class="fas fa-search mr-2"></i>
                    Apply Filters
                </button>
                <a href="{{ route('users.index') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times mr-1"></i>
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Authentication</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=667eea&color=fff" alt="{{ $user->name }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($user->auth_type === 'oauth') bg-blue-100 text-blue-800
                                    @elseif($user->auth_type === 'saml') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    <i class="fas fa-{{ $user->auth_type === 'oauth' ? 'globe' : ($user->auth_type === 'saml' ? 'certificate' : 'user') }} mr-1"></i>
                                    {{ ucfirst($user->auth_type ?? 'internal') }}
                                </span>
                                @if($user->external_id)
                                    <div class="text-xs text-gray-500 mt-1">ID: {{ $user->external_id }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->created_at->format('M d, Y') }}
                                <div class="text-xs">{{ $user->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->updated_at->format('M d, Y') }}
                                <div class="text-xs">{{ $user->updated_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900" title="View User">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit User">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->auth_type === 'internal' || empty($user->auth_type))
                                        <form method="POST" action="{{ route('users.reset-password', $user) }}" class="inline" onsubmit="return confirm('Are you sure you want to reset this user\'s password? This will generate a temporary password.')">
                                            @csrf
                                            <button type="submit" class="text-orange-600 hover:text-orange-900" title="Reset Password">
                                                <i class="fas fa-key"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Delete User">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No users found</h3>
                                    <p class="text-gray-500 mb-4">Try adjusting your search criteria or add a new user.</p>
                                    <a href="{{ route('users.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium">
                                        Add First User
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
