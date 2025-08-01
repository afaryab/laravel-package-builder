@extends('layouts.dashboard')

@section('title', 'Authentication Setup')

@section('content')
<div class="bg-white shadow-sm rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center">
            <a href="{{ route('setup') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <x-heroicon-o-arrow-left class="w-5 h-5" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Authentication Setup</h1>
                <p class="mt-1 text-sm text-gray-600">Configure authentication providers and security settings</p>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8 px-6">
            <a href="{{ route('setup') }}" 
               class="py-4 px-1 border-b-2 {{ request()->routeIs('setup') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium text-sm whitespace-nowrap">
                <x-heroicon-o-home class="w-4 h-4 mr-2 inline" />
            </a>
            <a href="{{ route('authentication') }}" 
               class="py-4 px-1 border-b-2 {{ request()->routeIs('authentication') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium text-sm whitespace-nowrap">
                <x-heroicon-o-key class="w-4 h-4 mr-2 inline" />
                Authentication
            </a>
            <a href="{{ route('tokens') }}" 
               class="py-4 px-1 border-b-2 {{ request()->routeIs('tokens') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium text-sm whitespace-nowrap">
                <x-heroicon-o-cpu-chip class="w-4 h-4 mr-2 inline" />
                Integration Tokens
            </a>
        </nav>
    </div>

    <div class="max-w-5xl mx-auto p-6">
        <!-- Authentication Providers -->
        <div class="space-y-8">
            <!-- Internal Authentication -->
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <x-heroicon-o-shield-check class="w-6 h-6 text-blue-500 mr-3" />
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Internal Authentication</h3>
                            <p class="text-sm text-gray-600">Standard username/password authentication</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <x-heroicon-s-check class="w-4 h-4 mr-1" />
                        Enabled
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password Requirements</label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <x-heroicon-s-check class="w-4 h-4 text-green-500 mr-2" />
                                    <span class="text-sm text-gray-600">Minimum 8 characters</span>
                                </div>
                                <div class="flex items-center">
                                    <x-heroicon-s-check class="w-4 h-4 text-green-500 mr-2" />
                                    <span class="text-sm text-gray-600">Password confirmation required</span>
                                </div>
                                <div class="flex items-center">
                                    <x-heroicon-s-x-mark class="w-4 h-4 text-red-500 mr-2" />
                                    <span class="text-sm text-gray-600">Special characters required</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Security Features</label>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Session Timeout</span>
                                    <span class="text-sm font-medium text-gray-900">2 hours</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Failed Login Attempts</span>
                                    <span class="text-sm font-medium text-gray-900">5 attempts</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Account Lockout</span>
                                    <span class="text-sm font-medium text-gray-900">15 minutes</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- OAuth Authentication -->
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <x-heroicon-o-globe-alt class="w-6 h-6 text-red-500 mr-3" />
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">OAuth Authentication</h3>
                            <p class="text-sm text-gray-600">Google OAuth integration for single sign-on</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ config('auth.providers.oauth.enabled', false) ? 'green' : 'yellow' }}-100 text-{{ config('auth.providers.oauth.enabled', false) ? 'green' : 'yellow' }}-800">
                        @if(config('auth.providers.oauth.enabled', false))
                            <x-heroicon-s-check class="w-4 h-4 mr-1" />
                            Enabled
                        @else
                            <x-heroicon-s-exclamation-triangle class="w-4 h-4 mr-1" />
                            Configuration Required
                        @endif
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">OAuth Configuration</label>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Client ID</label>
                                    <input type="text" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                           value="{{ config('services.google.client_id') ? '••••••••••••' . substr(config('services.google.client_id'), -4) : 'Not configured' }}"
                                           readonly>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Client Secret</label>
                                    <input type="password" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                           value="{{ config('services.google.client_secret') ? '••••••••••••••••' : 'Not configured' }}"
                                           readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">OAuth Status</label>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Provider</span>
                                    <span class="text-sm font-medium text-gray-900">Google</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Redirect URI</span>
                                    <span class="text-sm font-medium text-gray-900 truncate ml-2">{{ url('/auth/oauth/callback') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Last Test</span>
                                    <span class="text-sm font-medium text-gray-900">Never</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if(!config('auth.providers.oauth.enabled', false))
                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                    <div class="flex">
                        <x-heroicon-s-exclamation-triangle class="w-5 h-5 text-yellow-400 mr-2" />
                        <div class="text-sm text-yellow-700">
                            <p class="font-medium">OAuth not configured</p>
                            <p class="mt-1">Configure Google OAuth credentials in your environment file to enable OAuth authentication.</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- SAML Authentication -->
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <x-heroicon-o-document-text class="w-6 h-6 text-purple-500 mr-3" />
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">SAML Authentication</h3>
                            <p class="text-sm text-gray-600">Enterprise SAML 2.0 single sign-on</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ config('auth.providers.saml.enabled', false) ? 'green' : 'gray' }}-100 text-{{ config('auth.providers.saml.enabled', false) ? 'green' : 'gray' }}-800">
                        @if(config('auth.providers.saml.enabled', false))
                            <x-heroicon-s-check class="w-4 h-4 mr-1" />
                            Enabled
                        @else
                            <x-heroicon-s-x-mark class="w-4 h-4 mr-1" />
                            Disabled
                        @endif
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">SAML Configuration</label>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Entity ID</span>
                                    <span class="text-sm font-medium text-gray-900">{{ config('saml2.sp.entityId', 'Not configured') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">ACS URL</span>
                                    <span class="text-sm font-medium text-gray-900 truncate ml-2">{{ url('/saml/acs') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">SLS URL</span>
                                    <span class="text-sm font-medium text-gray-900 truncate ml-2">{{ url('/saml/sls') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Identity Provider</label>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">IdP Entity ID</span>
                                    <span class="text-sm font-medium text-gray-900">{{ config('saml2.idp.entityId', 'Not configured') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">SSO URL</span>
                                    <span class="text-sm font-medium text-gray-900">{{ config('saml2.idp.sso', 'Not configured') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Certificate</span>
                                    <span class="text-sm font-medium text-gray-900">{{ config('saml2.idp.x509cert') ? 'Configured' : 'Not configured' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if(!config('auth.providers.saml.enabled', false))
                <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-md">
                    <div class="flex">
                        <x-heroicon-s-information-circle class="w-5 h-5 text-gray-400 mr-2" />
                        <div class="text-sm text-gray-600">
                            <p class="font-medium">SAML not enabled</p>
                            <p class="mt-1">Contact your system administrator to configure SAML authentication for enterprise single sign-on.</p>
                        </div>
                    </div>
                </div>
                @endif
                
                @if(config('auth.providers.saml.enabled', false))
                <div class="mt-4 flex space-x-3">
                    <a href="{{ route('saml.metadata') }}" 
                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-2" />
                        Download Metadata
                    </a>
                    <button type="button" 
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <x-heroicon-o-beaker class="w-4 h-4 mr-2" />
                        Test SAML
                    </button>
                </div>
                @endif
            </div>

            <!-- Current Auth Type Display -->
            <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
                <div class="flex items-center">
                    <x-heroicon-s-information-circle class="w-6 h-6 text-blue-500 mr-3" />
                    <div>
                        <h3 class="text-lg font-medium text-blue-900">Current Authentication Type</h3>
                        <p class="text-sm text-blue-700 mt-1">
                            The system is currently configured to use: 
                            <span class="font-medium">{{ ucfirst(config('auth.type', 'internal')) }} Authentication</span>
                        </p>
                        <p class="text-xs text-blue-600 mt-2">
                            To change the authentication type, update the AUTH_TYPE environment variable and restart the application.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
