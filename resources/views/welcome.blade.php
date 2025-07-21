<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ config('app.name', 'Laravel') }}</h1>
                <p class="text-gray-600 mb-6">Welcome to your Laravel application with Docker setup</p>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-blue-800">
                        <strong>Current Authentication:</strong> 
                        <span class="font-mono bg-blue-100 px-2 py-1 rounded">{{ $authType }}</span>
                    </p>
                </div>
                
                @if($authType === 'none')
                    <p class="text-green-600">✓ Public access - no authentication required</p>
                @elseif($authType === 'basic')
                    <p class="text-yellow-600">🔒 Basic HTTP authentication enabled</p>
                @elseif($authType === 'internal')
                    <div class="space-y-2">
                        <p class="text-orange-600">🔐 Laravel authentication enabled</p>
                        @auth
                            <p class="text-green-600">Welcome, {{ auth()->user()->name }}!</p>
                            <a href="/admin" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 inline-block">Admin Panel</a>
                        @else
                            @if(\LaravelApp\Models\User::count() === 0)
                                <p class="text-amber-600">⚠️ No admin users found. Set up your first admin account.</p>
                                <a href="/login" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 inline-block">Setup Admin Account</a>
                            @else
                                <a href="/login" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 inline-block">Login</a>
                            @endif
                        @endauth
                    </div>
                @elseif($authType === 'saml')
                    <div class="space-y-2">
                        <p class="text-purple-600">🔗 SAML authentication enabled</p>
                        @auth
                            <p class="text-green-600">Welcome, {{ auth()->user()->name }}!</p>
                            <a href="/admin" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 inline-block">Admin Panel</a>
                        @else
                            <a href="/saml/login" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600 inline-block">SAML Login</a>
                        @endauth
                    </div>
                @elseif($authType === 'oauth')
                    <div class="space-y-2">
                        <p class="text-indigo-600">🔗 OAuth authentication enabled</p>
                        @auth
                            <p class="text-green-600">Welcome, {{ auth()->user()->name }}!</p>
                            <a href="/admin" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 inline-block">Admin Panel</a>
                        @else
                            <a href="/oauth/redirect" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600 inline-block">OAuth Login</a>
                        @endauth
                    </div>
                @endif
                
                <div class="mt-8 pt-4 border-t border-gray-200">
                    <p class="text-xs text-gray-500">
                        Namespace: LaravelApp | Docker: Enabled | Environment: {{ app()->environment() }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
