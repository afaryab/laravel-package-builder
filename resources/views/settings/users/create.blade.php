@extends('layouts.dashboard')

@section('title', 'Create User')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Create New User</h1>
            <p class="text-gray-600 mt-1">Add a new user to the system</p>
        </div>
        <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Users
        </a>
    </div>

    <div class="max-w-2xl">
        <!-- Create User Form -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">User Information</h3>
                <p class="text-sm text-gray-500">Fill in the details for the new user account</p>
            </div>
            
            <form method="POST" action="{{ route('users.store') }}" class="p-6 space-y-6">
                @csrf
                
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name"
                           value="{{ old('name') }}"
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
                           value="{{ old('email') }}"
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
                        <option value="internal" {{ old('auth_type') === 'internal' ? 'selected' : '' }}>Internal</option>
                        <option value="oauth" {{ old('auth_type') === 'oauth' ? 'selected' : '' }}>OAuth</option>
                        <option value="saml" {{ old('auth_type') === 'saml' ? 'selected' : '' }}>SAML</option>
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
                           value="{{ old('external_id') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('external_id') border-red-300 @enderror"
                           placeholder="Optional: ID from external authentication provider">
                    @error('external_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Only required for OAuth and SAML authentication types</p>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
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

                <!-- Confirm Password -->
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

                <!-- Submit Button -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('users.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-medium">
                        Cancel
                    </a>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-plus mr-2"></i>
                        Create User
                    </button>
                </div>
            </form>
        </div>

        <!-- Information Panel -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-blue-800">Authentication Types</h4>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>Internal:</strong> Traditional username/password authentication managed locally</li>
                            <li><strong>OAuth:</strong> Authentication via external OAuth providers (Google, GitHub, etc.)</li>
                            <li><strong>SAML:</strong> Enterprise single sign-on authentication via SAML providers</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
