@extends('layouts.dashboard')

@section('title', 'Role Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $role->display_name }}</h1>
                        <p class="mt-2 text-gray-600">{{ $role->description ?: 'No description available' }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('access-control.roles.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Back to Roles
                        </a>
                        <a href="{{ route('access-control.roles.edit', $role) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Edit Role
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Role Information -->
                <div class="lg:col-span-1">
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Role Information</h3>
                        </div>
                        <div class="px-6 py-4 space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">System Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $role->name }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Display Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $role->display_name }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <div class="flex items-center space-x-2">
                                        @if($role->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Inactive
                                            </span>
                                        @endif
                                        
                                        @if($role->is_default)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Default
                                            </span>
                                        @endif
                                    </div>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total Permissions</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $role->permissions->count() }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Assigned Users</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $role->users->count() }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $role->created_at->format('M d, Y \a\t H:i') }}</dd>
                            </div>
                        </div>
                    </div>

                    <!-- Assigned Users -->
                    @if($role->users->count() > 0)
                        <div class="bg-white shadow rounded-lg mt-6">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Assigned Users</h3>
                            </div>
                            <div class="px-6 py-4">
                                <div class="space-y-3">
                                    @foreach($role->users->take(10) as $user)
                                        <div class="flex items-center space-x-3">
                                            <div class="h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">{{ substr($user->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @if($role->users->count() > 10)
                                        <div class="text-sm text-gray-500 pt-2 border-t">
                                            And {{ $role->users->count() - 10 }} more users...
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Permissions -->
                <div class="lg:col-span-2">
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Permissions ({{ $role->permissions->count() }})</h3>
                        </div>
                        <div class="px-6 py-4">
                            @if($role->permissions->count() > 0)
                                @php $groupedPermissions = $role->permissions->groupBy('group'); @endphp
                                
                                @foreach($groupedPermissions as $group => $permissions)
                                    <div class="mb-6">
                                        <h4 class="text-md font-medium text-gray-900 mb-3 capitalize">{{ $group }} Permissions</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @foreach($permissions as $permission)
                                                <div class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg">
                                                    <div class="flex-shrink-0 mt-1">
                                                        <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-sm font-medium text-gray-900">{{ $permission->display_name }}</div>
                                                        @if($permission->description)
                                                            <div class="text-xs text-gray-500">{{ $permission->description }}</div>
                                                        @endif
                                                        <div class="text-xs text-gray-400 mt-1 font-mono">{{ $permission->name }}</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No permissions assigned</h3>
                                    <p class="mt-1 text-sm text-gray-500">This role doesn't have any permissions yet.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('access-control.roles.edit', $role) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                            Assign Permissions
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
