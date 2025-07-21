@extends('layouts.dashboard')

@section('title', 'Roles Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Roles Management</h1>
                        <p class="mt-2 text-gray-600">Create and manage user roles</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('access-control.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Back to Access Control
                        </a>
                        <a href="{{ route('access-control.roles.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Create New Role
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Roles Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($roles as $role)
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-medium text-gray-900">{{ $role->display_name }}</h3>
                                <div class="flex items-center space-x-2">
                                    @if($role->is_default)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Default
                                        </span>
                                    @endif
                                    @if($role->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Inactive
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <p class="text-gray-600 text-sm mb-4">{{ $role->description ?: 'No description available' }}</p>
                            
                            <div class="text-sm text-gray-500 mb-4">
                                <div class="flex items-center justify-between">
                                    <span>Permissions:</span>
                                    <span class="font-medium">{{ $role->permissions->count() }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Users:</span>
                                    <span class="font-medium">{{ $role->users->count() ?? 0 }}</span>
                                </div>
                            </div>
                            
                            <!-- Permission Preview -->
                            @if($role->permissions->count() > 0)
                                <div class="mb-4">
                                    <div class="text-xs text-gray-500 mb-2">Permissions:</div>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($role->permissions->take(5) as $permission)
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                                {{ $permission->display_name }}
                                            </span>
                                        @endforeach
                                        @if($role->permissions->count() > 5)
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-200 text-gray-600">
                                                +{{ $role->permissions->count() - 5 }} more
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="bg-gray-50 px-6 py-3">
                            <div class="flex items-center justify-between">
                                <div class="flex space-x-2">
                                    <a href="{{ route('access-control.roles.show', $role) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        View
                                    </a>
                                    <a href="{{ route('access-control.roles.edit', $role) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                        Edit
                                    </a>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    @if(!$role->is_default)
                                        <form method="POST" action="{{ route('access-control.roles.toggle', $role) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-sm {{ $role->is_active ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800' }}">
                                                {{ $role->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                        
                                        @if($role->users->count() == 0)
                                            <form method="POST" action="{{ route('access-control.roles.destroy', $role) }}" 
                                                  class="inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete this role?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3">
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No roles found</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new role.</p>
                            <div class="mt-6">
                                <a href="{{ route('access-control.roles.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Create New Role
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
