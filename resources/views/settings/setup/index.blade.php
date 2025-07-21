@extends('layouts.dashboard')

@section('title', 'Setup')

@section('content')
<div class="bg-white shadow-sm rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900">System Setup</h1>
        <p class="mt-1 text-sm text-gray-600">Configure system-wide settings and integrations</p>
    </div>

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8 px-6">
            <a href="{{ route('setup') }}" 
               class="py-4 px-1 border-b-2 {{ request()->routeIs('setup') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium text-sm whitespace-nowrap">
                <i class="fas fa-home mr-2"></i>
            </a>
            <a href="{{ route('authentication') }}" 
               class="py-4 px-1 border-b-2 {{ request()->routeIs('authentication') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium text-sm whitespace-nowrap">
                <i class="fas fa-key mr-2"></i>
                Authentication
            </a>
            <a href="{{ route('tokens') }}" 
               class="py-4 px-1 border-b-2 {{ request()->routeIs('tokens') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium text-sm whitespace-nowrap">
                <i class="fas fa-plug mr-2"></i>
                Integration Tokens
            </a>
        </nav>
    </div>

    <div class="max-w-5xl mx-auto p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Authentication Setup Card -->
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <i class="fas fa-key text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Authentication Setup</h3>
                        <p class="text-sm text-gray-600">Configure authentication providers and methods</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Internal Authentication</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Enabled
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">OAuth Integration</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ config('auth.providers.oauth.enabled', false) ? 'green' : 'gray' }}-100 text-{{ config('auth.providers.oauth.enabled', false) ? 'green' : 'gray' }}-800">
                            {{ config('auth.providers.oauth.enabled', false) ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">SAML Authentication</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ config('auth.providers.saml.enabled', false) ? 'green' : 'gray' }}-100 text-{{ config('auth.providers.saml.enabled', false) ? 'green' : 'gray' }}-800">
                            {{ config('auth.providers.saml.enabled', false) ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="{{ route('authentication') }}" 
                       class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Configure Authentication
                    </a>
                </div>
            </div>

            <!-- Integration Tokens Card -->
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <i class="fas fa-plug text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Integration Tokens</h3>
                        <p class="text-sm text-gray-600">Manage API tokens and third-party integrations</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Active API Tokens</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ auth()->user()->tokens()->count() ?? 0 }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Last Token Created</span>
                        <span class="text-sm text-gray-600">
                            {{ auth()->user()->tokens()->latest()->first()?->created_at?->diffForHumans() ?? 'Never' }}
                        </span>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="{{ route('tokens') }}" 
                       class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Manage Tokens
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <button type="button" class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div>
                        <span class="rounded-lg inline-flex p-3 bg-blue-50 text-blue-700 ring-4 ring-white">
                            <i class="fas fa-sync-alt"></i>
                        </span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-sm font-medium text-gray-900">
                            Sync Configuration
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Update system configuration from external sources
                        </p>
                    </div>
                </button>

                <button type="button" class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div>
                        <span class="rounded-lg inline-flex p-3 bg-green-50 text-green-700 ring-4 ring-white">
                            <i class="fas fa-shield-alt"></i>
                        </span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-sm font-medium text-gray-900">
                            Security Audit
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Review security settings and permissions
                        </p>
                    </div>
                </button>

                <button type="button" class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div>
                        <span class="rounded-lg inline-flex p-3 bg-yellow-50 text-yellow-700 ring-4 ring-white">
                            <i class="fas fa-download"></i>
                        </span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-sm font-medium text-gray-900">
                            Export Settings
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Download configuration as backup
                        </p>
                    </div>
                </button>

                <button type="button" class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div>
                        <span class="rounded-lg inline-flex p-3 bg-purple-50 text-purple-700 ring-4 ring-white">
                            <i class="fas fa-cogs"></i>
                        </span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-sm font-medium text-gray-900">
                            Advanced Settings
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Configure advanced system options
                        </p>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
