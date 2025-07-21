@extends('layouts.dashboard')

@section('title', 'Create Role')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Create New Role</h1>
                        <p class="mt-2 text-gray-600">Define a new role and assign permissions</p>
                    </div>
                    <a href="{{ route('access-control.roles.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Back to Roles
                    </a>
                </div>
            </div>

            @if($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('access-control.roles.store') }}" x-data="roleForm()">
                @csrf
                
                <div class="bg-white shadow rounded-lg">
                    <!-- Basic Information -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                    </div>
                    
                    <div class="px-6 py-4 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Role Name (System)</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="e.g., custom_admin">
                                <p class="mt-1 text-xs text-gray-500">Used internally by the system. Use lowercase with underscores.</p>
                            </div>
                            
                            <div>
                                <label for="display_name" class="block text-sm font-medium text-gray-700">Display Name</label>
                                <input type="text" name="display_name" id="display_name" value="{{ old('display_name') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="e.g., Custom Administrator">
                                <p class="mt-1 text-xs text-gray-500">User-friendly name displayed in the interface.</p>
                            </div>
                        </div>
                        
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Describe what this role is for...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Permissions -->
                <div class="bg-white shadow rounded-lg mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Permissions</h3>
                            <div class="flex space-x-2">
                                <button type="button" @click="selectAll()" class="text-sm bg-green-100 hover:bg-green-200 text-green-800 px-3 py-1 rounded">
                                    Select All
                                </button>
                                <button type="button" @click="deselectAll()" class="text-sm bg-red-100 hover:bg-red-200 text-red-800 px-3 py-1 rounded">
                                    Deselect All
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4">
                        @foreach($permissions as $group => $groupPermissions)
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-md font-medium text-gray-900 capitalize">{{ $group }} Permissions</h4>
                                    <div class="flex space-x-2">
                                        <button type="button" @click="selectGroup('{{ $group }}')" class="text-xs bg-blue-100 hover:bg-blue-200 text-blue-800 px-2 py-1 rounded">
                                            Select All {{ ucfirst($group) }}
                                        </button>
                                        <button type="button" @click="deselectGroup('{{ $group }}')" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-800 px-2 py-1 rounded">
                                            Deselect All {{ ucfirst($group) }}
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($groupPermissions as $permission)
                                        <label class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input type="checkbox" 
                                                   name="permissions[]" 
                                                   value="{{ $permission->id }}"
                                                   data-group="{{ $group }}"
                                                   {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                                                   class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-medium text-gray-900">{{ $permission->display_name }}</div>
                                                @if($permission->description)
                                                    <div class="text-xs text-gray-500">{{ $permission->description }}</div>
                                                @endif
                                                <div class="text-xs text-gray-400 mt-1">{{ $permission->name }}</div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end space-x-4 mt-6">
                    <a href="{{ route('access-control.roles.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md text-sm font-medium">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md text-sm font-medium">
                        Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function roleForm() {
    return {
        selectAll() {
            const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        },
        
        deselectAll() {
            const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        },
        
        selectGroup(group) {
            const checkboxes = document.querySelectorAll(`input[data-group="${group}"]`);
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        },
        
        deselectGroup(group) {
            const checkboxes = document.querySelectorAll(`input[data-group="${group}"]`);
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    }
}
</script>
@endsection
