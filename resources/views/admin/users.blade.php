<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Laravel Package Builder</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-semibold">User Management</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/admin" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                    @auth
                        <span class="text-gray-700">{{ auth()->user()->name }}</span>
                        <form method="POST" action="/logout" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800">Logout</button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold">System Users</h2>
                </div>
                <div class="p-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <p class="text-yellow-800">
                            <strong>Note:</strong> User management features depend on your authentication type ({{ env('AUTH', 'none') }}).
                        </p>
                    </div>
                    
                    @if(env('AUTH') === 'none')
                        <p class="text-gray-600">No authentication is enabled. User management is not available.</p>
                    @elseif(env('AUTH') === 'basic')
                        <p class="text-gray-600">Basic HTTP authentication is enabled. Users are managed via .htpasswd file.</p>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-600 mb-4">User management interface would be implemented here based on your authentication type.</p>
                            <p class="text-sm text-gray-500">This is a placeholder for the user management functionality.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
