@extends('layouts.dashboard')

@section('title', 'Permission Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Permission Management</h1>
                        <p class="mt-2 text-gray-600">Configure role permissions with checkboxes</p>
                    </div>
                    <a href="{{ route('access-control.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Back to Access Control
                    </a>
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

            <!-- Permission Matrix -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <form method="POST" action="{{ route('access-control.permissions.bulk-update') }}" x-data="permissionMatrix()">
                    @csrf
                    @method('PATCH')
                    
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Role Permission Matrix</h3>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                Save All Changes
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-4 font-medium text-gray-900 bg-gray-50 sticky left-0 z-10 min-w-[200px]">
                                            Permission
                                        </th>
                                        @foreach($roles as $role)
                                            <th class="text-center py-3 px-4 font-medium text-gray-900 bg-gray-50 min-w-[120px]">
                                                <div class="flex flex-col items-center">
                                                    <span>{{ $role->display_name }}</span>
                                                    @if($role->is_default)
                                                        <span class="text-xs text-green-600">(Default)</span>
                                                    @endif
                                                </div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permissions as $group => $groupPermissions)
                                        <!-- Group Header -->
                                        <tr class="bg-gray-100">
                                            <td colspan="{{ count($roles) + 1 }}" class="px-4 py-2 font-semibold text-gray-700 uppercase text-sm tracking-wide">
                                                {{ ucfirst($group) }} Permissions
                                            </td>
                                        </tr>
                                        
                                        @foreach($groupPermissions as $permission)
                                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                                <td class="py-3 px-4 bg-white sticky left-0 z-10 border-r border-gray-200">
                                                    <div>
                                                        <div class="font-medium text-gray-900">{{ $permission->display_name }}</div>
                                                        @if($permission->description)
                                                            <div class="text-sm text-gray-500">{{ $permission->description }}</div>
                                                        @endif
                                                        <div class="text-xs text-gray-400 mt-1">{{ $permission->name }}</div>
                                                    </div>
                                                </td>
                                                
                                                @foreach($roles as $role)
                                                    <td class="py-3 px-4 text-center">
                                                        <div class="flex items-center justify-center">
                                                            <input 
                                                                type="checkbox" 
                                                                name="role_permissions[{{ $role->id }}][]" 
                                                                value="{{ $permission->id }}"
                                                                {{ $role->permissions->contains('id', $permission->id) ? 'checked' : '' }}
                                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                                @change="togglePermission({{ $role->id }}, {{ $permission->id }}, $event.target.checked)"
                                                            >
                                                        </div>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Bulk Actions -->
                        <div class="mt-6 border-t border-gray-200 pt-6">
                            <div class="flex flex-wrap gap-4">
                                @foreach($roles as $role)
                                    <div class="flex items-center space-x-2">
                                        <button 
                                            type="button" 
                                            @click="selectAllForRole({{ $role->id }})"
                                            class="text-sm bg-green-100 hover:bg-green-200 text-green-800 px-3 py-1 rounded-md"
                                        >
                                            Select All for {{ $role->display_name }}
                                        </button>
                                        <button 
                                            type="button" 
                                            @click="deselectAllForRole({{ $role->id }})"
                                            class="text-sm bg-red-100 hover:bg-red-200 text-red-800 px-3 py-1 rounded-md"
                                        >
                                            Deselect All for {{ $role->display_name }}
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Individual Role Update Forms -->
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <h4 class="text-md font-medium text-gray-900 mb-4">Quick Individual Updates</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($roles as $role)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <h5 class="font-medium text-gray-900">{{ $role->display_name }}</h5>
                                            @if($role->is_default)
                                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Default</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 mb-3">{{ $role->description }}</p>
                                        <div class="text-sm text-gray-500 mb-3">
                                            {{ $role->permissions->count() }} permissions assigned
                                        </div>
                                        <form method="POST" action="{{ route('access-control.roles.permissions', $role) }}" class="space-y-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="redirect_back" value="1">
                                            
                                            @php $rolePermissionIds = $role->permissions->pluck('id')->toArray(); @endphp
                                            @foreach($permissions->flatten() as $permission)
                                                <label class="flex items-center">
                                                    <input 
                                                        type="checkbox" 
                                                        name="permissions[]" 
                                                        value="{{ $permission->id }}"
                                                        {{ in_array($permission->id, $rolePermissionIds) ? 'checked' : '' }}
                                                        class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mr-2"
                                                    >
                                                    <span class="text-xs text-gray-700">{{ $permission->display_name }}</span>
                                                </label>
                                            @endforeach
                                            
                                            <button type="submit" class="w-full mt-3 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                                Update {{ $role->display_name }}
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function permissionMatrix() {
    return {
        togglePermission(roleId, permissionId, checked) {
            console.log(`Role ${roleId}, Permission ${permissionId}, Checked: ${checked}`);
        },
        
        selectAllForRole(roleId) {
            const checkboxes = document.querySelectorAll(`input[name="role_permissions[${roleId}][]"]`);
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        },
        
        deselectAllForRole(roleId) {
            const checkboxes = document.querySelectorAll(`input[name="role_permissions[${roleId}][]"]`);
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    }
}
</script>
@endsection
