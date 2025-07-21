@extends('layouts.app')

@section('title', 'Token Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Integration Token Management</h1>
            <p class="text-gray-600">Create and manage integration tokens for API access. These tokens can only be created through this admin interface for security.</p>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('new_token'))
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-yellow-800">New Token Created: {{ session('token_name') }}</h3>
                        <div class="mt-2">
                            <p class="text-sm text-yellow-700">⚠️ <strong>Store this token securely. It will not be shown again.</strong></p>
                            <div class="mt-2 bg-gray-900 text-green-400 p-3 rounded font-mono text-sm break-all">
                                {{ session('new_token') }}
                            </div>
                            <button onclick="copyToClipboard('{{ session('new_token') }}')" class="mt-2 inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-yellow-700 bg-yellow-100 hover:bg-yellow-200">
                                Copy Token
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Create Token Form --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Create Integration Token</h2>
                
                <form action="{{ route('admin.tokens.create') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Token Name</label>
                        <input type="text" id="name" name="name" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="e.g., Production API Integration">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Permissions (Scopes)</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="scopes[]" value="integration:read" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">integration:read - Read access for integrations</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="scopes[]" value="integration:write" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">integration:write - Write access for integrations</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="scopes[]" value="user:read" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">user:read - Read user information</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="scopes[]" value="users:read" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">users:read - Read all users (admin)</span>
                            </label>
                        </div>
                        @error('scopes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">Expiration Date (Optional)</label>
                        <input type="datetime-local" id="expires_at" name="expires_at" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Leave empty for no expiration</p>
                        @error('expires_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                        Create Integration Token
                    </button>
                </form>
            </div>

            {{-- Security Notice --}}
            <div class="bg-red-50 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-red-800 mb-4">🔒 Security Notice</h2>
                
                <div class="space-y-3 text-sm text-red-700">
                    <p><strong>Integration tokens can only be created here</strong> - not through the API - for security reasons.</p>
                    
                    <p><strong>Token Security:</strong></p>
                    <ul class="list-disc list-inside space-y-1 ml-2">
                        <li>Tokens are only shown once during creation</li>
                        <li>Store tokens securely (environment variables, vault, etc.)</li>
                        <li>Never commit tokens to version control</li>
                        <li>Use specific scopes - avoid overprivileged tokens</li>
                        <li>Set expiration dates when possible</li>
                        <li>Revoke unused or compromised tokens immediately</li>
                    </ul>

                    <p><strong>Usage:</strong></p>
                    <ul class="list-disc list-inside space-y-1 ml-2">
                        <li>Include in API requests: <code>Authorization: Bearer {token}</code></li>
                        <li>Use HTTPS in production environments</li>
                        <li>Monitor token usage in the list below</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Token List --}}
        <div class="mt-8 bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">All Tokens</h2>
                <p class="text-sm text-gray-600">All integration and user tokens in the system</p>
            </div>
            
            <div id="tokens-list" class="p-6">
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="ml-2 text-gray-600">Loading tokens...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // You could show a toast notification here
        alert('Token copied to clipboard!');
    });
}

// Load tokens via AJAX
document.addEventListener('DOMContentLoaded', function() {
    loadTokens();
});

function loadTokens() {
    fetch('{{ route('admin.tokens.list') }}', {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        const container = document.getElementById('tokens-list');
        
        if (data.success && data.data.data.length > 0) {
            container.innerHTML = `
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scopes</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Used</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expires</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${data.data.data.map(token => `
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${token.name}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${token.type === 'integration' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}">
                                            ${token.type}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${token.user ? token.user.name + ' (' + token.user.email + ')' : 'System'}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="flex flex-wrap gap-1">
                                            ${token.scopes.map(scope => `<span class="inline-flex px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">${scope}</span>`).join('')}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        ${token.last_used_at ? new Date(token.last_used_at).toLocaleDateString() : 'Never'}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        ${token.expires_at ? new Date(token.expires_at).toLocaleDateString() : 'Never'}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="revokeToken(${token.id})" class="text-red-600 hover:text-red-900">Revoke</button>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
                ${data.data.next_page_url ? `<div class="mt-4 text-center"><button onclick="loadTokens()" class="text-blue-600 hover:text-blue-800">Load More</button></div>` : ''}
            `;
        } else {
            container.innerHTML = `
                <div class="text-center py-8">
                    <p class="text-gray-500">No tokens found</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading tokens:', error);
        document.getElementById('tokens-list').innerHTML = `
            <div class="text-center py-8">
                <p class="text-red-500">Error loading tokens</p>
            </div>
        `;
    });
}

function revokeToken(tokenId) {
    if (!confirm('Are you sure you want to revoke this token? This action cannot be undone.')) {
        return;
    }

    fetch(`/admin/tokens/${tokenId}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadTokens(); // Reload the list
            alert('Token revoked successfully');
        } else {
            alert('Error revoking token');
        }
    })
    .catch(error => {
        console.error('Error revoking token:', error);
        alert('Error revoking token');
    });
}
</script>
@endsection
