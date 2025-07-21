@extends('layouts.dashboard')

@section('title', 'User Details')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">User Details</h1>
            <p class="text-gray-600 mt-1">View and manage user information</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('users.edit', $user) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg font-medium inline-flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Edit User
            </a>
            <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Users
            </a>
        </div>
    </div>

    <!-- Success Messages -->
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4">
                    <div class="flex flex-col items-center">
                        <div class="flex-shrink-0">
                            <img class="h-24 w-24 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=667eea&color=fff&size=200" alt="{{ $user->name }}">
                        </div>
                        <div class="mt-4 text-center">
                            <h3 class="text-xl font-medium text-gray-900">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($user->auth_type === 'oauth') bg-blue-100 text-blue-800
                                    @elseif($user->auth_type === 'saml') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    <i class="fas fa-{{ $user->auth_type === 'oauth' ? 'globe' : ($user->auth_type === 'saml' ? 'certificate' : 'user') }} mr-1"></i>
                                    {{ ucfirst($user->auth_type ?? 'internal') }} User
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">User ID</dt>
                            <dd class="text-sm text-gray-900 font-mono">#{{ $user->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Member since</dt>
                            <dd class="text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last updated</dt>
                            <dd class="text-sm text-gray-900">{{ $user->updated_at->format('M d, Y') }}</dd>
                        </div>
                        @if($user->external_id)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">External ID</dt>
                            <dd class="text-sm text-gray-900 font-mono">{{ $user->external_id }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <!-- User Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">User Information</h3>
                    <p class="mt-1 text-sm text-gray-500">Detailed account information and settings.</p>
                </div>
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Authentication Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($user->auth_type ?? 'internal') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Account Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Active
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('F j, Y \a\t g:i A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('F j, Y \a\t g:i A') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Password Management (for internal auth only) -->
            @if($user->auth_type === 'internal' || $user->auth_type === null)
            <div class="mt-6 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Password Management</h3>
                    <p class="mt-1 text-sm text-gray-500">Update the user's password.</p>
                </div>
                <form id="password-form" method="POST" action="{{ route('users.password', $user) }}" class="px-6 py-4">
                    @csrf
                    @method('PATCH')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                New Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" 
                                   name="password" 
                                   id="password"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-300 @enderror"
                                   required>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   required>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <form method="POST" action="{{ route('users.reset-password', $user) }}" class="inline">
                            @csrf
                            <button type="button" 
                                    onclick="confirmPasswordReset(this.form)" 
                                    class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium">
                                <i class="fas fa-refresh mr-2"></i>
                                Reset Password
                            </button>
                        </form>
                        <button type="submit" form="password-form" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">
                            <i class="fas fa-key mr-2"></i>
                            Update Password
                        </button>
                    </div>
                </form>
                
                <!-- JavaScript for password reset confirmation -->
                <script>
                function confirmPasswordReset(form) {
                    if (confirm('Are you sure you want to reset this user\'s password? This will generate a temporary password and cannot be undone.')) {
                        form.submit();
                    }
                }
                </script>
            </div>
            @else
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-yellow-800">Password Management Unavailable</h4>
                        <p class="text-sm text-yellow-700 mt-1">
                            Password changes are not available for {{ ucfirst($user->auth_type) }} authentication. 
                            The user's password is managed by their external authentication provider.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Danger Zone -->
            @if($user->id !== auth()->id())
            <div class="mt-6 bg-white rounded-lg shadow border border-red-200">
                <div class="px-6 py-4 border-b border-red-200">
                    <h3 class="text-lg font-medium text-red-900">Danger Zone</h3>
                    <p class="mt-1 text-sm text-red-600">Irreversible and destructive actions.</p>
                </div>
                <div class="px-6 py-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Delete User Account</h4>
                            <p class="text-sm text-gray-500 mt-1">
                                Permanently delete this user account and all associated data. This action cannot be undone.
                            </p>
                        </div>
                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="ml-4" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium text-sm">
                                <i class="fas fa-trash mr-2"></i>
                                Delete User
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
