@extends('layouts.dashboard')

@section('title', 'Edit User')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit User</h1>
            <p class="text-gray-600 mt-1">Update user information and settings</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('users.show', $user) }}" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium inline-flex items-center">
                <i class="fas fa-eye mr-2"></i>
                View User
            </a>
            <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Users
            </a>
        </div>
    </div>

    <div class="max-w-2xl">
        <!-- Edit User Form -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <img class="h-12 w-12 rounded-full mr-4" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=667eea&color=fff" alt="{{ $user->name }}">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $user->email }} • User ID: #{{ $user->id }}</p>
                    </div>
                </div>
            </div>
            
            <form method="POST" action="{{ route('users.update', $user) }}" class="p-6 space-y-6">
                @csrf
                @method('PATCH')
                
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name"
                           value="{{ old('name', $user->name) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-300 @enderror"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email"
                           value="{{ old('email', $user->email) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-300 @enderror"
                           required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Authentication Type -->
                <div>
                    <label for="auth_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Authentication Type <span class="text-red-500">*</span>
                    </label>
                    <select name="auth_type" 
                            id="auth_type"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('auth_type') border-red-300 @enderror"
                            required>
                        <option value="">Select Authentication Type</option>
                        <option value="internal" {{ old('auth_type', $user->auth_type) === 'internal' ? 'selected' : '' }}>Internal</option>
                        <option value="oauth" {{ old('auth_type', $user->auth_type) === 'oauth' ? 'selected' : '' }}>OAuth</option>
                        <option value="saml" {{ old('auth_type', $user->auth_type) === 'saml' ? 'selected' : '' }}>SAML</option>
                    </select>
                    @error('auth_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- External ID -->
                <div>
                    <label for="external_id" class="block text-sm font-medium text-gray-700 mb-2">
                        External ID
                    </label>
                    <input type="text" 
                           name="external_id" 
                           id="external_id"
                           value="{{ old('external_id', $user->external_id) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('external_id') border-red-300 @enderror"
                           placeholder="Optional: ID from external authentication provider">
                    @error('external_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Only required for OAuth and SAML authentication types</p>
                </div>

                <!-- Account Information -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Account Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-500">Created:</span>
                            <span class="text-gray-900">{{ $user->created_at->format('M d, Y \a\t g:i A') }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-500">Last Updated:</span>
                            <span class="text-gray-900">{{ $user->updated_at->format('M d, Y \a\t g:i A') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('users.show', $user) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-medium">
                        Cancel
                    </a>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-save mr-2"></i>
                        Update User
                    </button>
                </div>
            </form>
        </div>

        <!-- Warning for authentication type changes -->
        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-yellow-800">Authentication Type Changes</h4>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Changing the authentication type may affect how this user logs in:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li><strong>Internal:</strong> User will need to use their email and password</li>
                            <li><strong>OAuth:</strong> User will authenticate via external OAuth provider</li>
                            <li><strong>SAML:</strong> User will authenticate via SAML identity provider</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
