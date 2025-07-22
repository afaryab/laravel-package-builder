@extends('layouts.dashboard')

@section('title', 'Settings')

@section('content')
<div class="p-6" x-data="settingsData()">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Settings</h1>
            <p class="text-gray-600 mt-1">Manage your account preferences and security</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Settings Navigation -->
        <div class="lg:col-span-1">
            <nav class="space-y-1 bg-white rounded-lg p-4 card-shadow">
                <a href="#profile" 
                   @click.prevent="activeTab = 'profile'"
                   :class="activeTab === 'profile' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-900 hover:bg-gray-50'"
                   class="border-l-4 pl-3 py-2 block text-sm font-medium cursor-pointer">
                    <i class="fas fa-user mr-2"></i>
                    Profile Settings
                </a>
                <a href="#security" 
                   @click.prevent="activeTab = 'security'"
                   :class="activeTab === 'security' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-900 hover:bg-gray-50'"
                   class="border-l-4 pl-3 py-2 block text-sm font-medium cursor-pointer">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Security
                </a>
                <a href="#notifications" 
                   @click.prevent="activeTab = 'notifications'"
                   :class="activeTab === 'notifications' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-900 hover:bg-gray-50'"
                   class="border-l-4 pl-3 py-2 block text-sm font-medium cursor-pointer">
                    <i class="fas fa-bell mr-2"></i>
                    Notifications
                </a>
                <a href="#sessions" 
                   @click.prevent="activeTab = 'sessions'"
                   :class="activeTab === 'sessions' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-900 hover:bg-gray-50'"
                   class="border-l-4 pl-3 py-2 block text-sm font-medium cursor-pointer">
                    <i class="fas fa-desktop mr-2"></i>
                    Sessions
                </a>
            </nav>
        </div>

        <!-- Settings Content -->
        <div class="lg:col-span-3">
            <!-- Profile Settings -->
            <div x-show="activeTab === 'profile'" class="bg-white rounded-lg card-shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Profile Settings</h3>
                    <p class="text-sm text-gray-500">Manage your personal information and preferences</p>
                </div>
                
                @if(config('auth.type') === 'none')
                    <div class="p-6">
                        <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                            <p class="text-sm text-gray-600">Profile editing is disabled when authentication is turned off.</p>
                        </div>
                    </div>
                @elseif((auth()->user()?->auth_type === 'internal' || empty(auth()->user()?->auth_type)) && auth()->check())
                <form class="p-6 space-y-6" method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')
                    
                    <!-- Success/Error Messages -->
                    @if(session('success'))
                        <div class="bg-green-50 border border-green-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-green-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name"
                                   value="{{ old('name', auth()->user()?->name ?? '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-300 @enderror"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   name="email"
                                   value="{{ old('email', auth()->user()?->email ?? '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-300 @enderror"
                                   required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 font-medium">
                            Update Profile
                        </button>
                    </div>
                </form>
                @else
                <div class="p-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">External Authentication</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Your profile is managed through {{ ucfirst(auth()->user()?->auth_type ?? 'unknown') }} authentication. To edit your profile information, please visit your identity provider.</p>
                                    <div class="mt-4">
                                        @if(auth()->user()?->auth_type === 'oauth')
                                            <button onclick="alert('Please visit your OAuth provider to edit your profile.')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                                <i class="fas fa-external-link-alt mr-2"></i>
                                                Edit via OAuth Provider
                                            </button>
                                        @elseif(auth()->user()?->auth_type === 'saml')
                                            <button onclick="alert('Please visit your SAML identity provider to edit your profile.')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                                <i class="fas fa-external-link-alt mr-2"></i>
                                                Edit via Identity Provider
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Security Settings -->
            <div x-show="activeTab === 'security'" class="bg-white rounded-lg card-shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Security Settings</h3>
                    <p class="text-sm text-gray-500">Manage your password and security preferences</p>
                </div>
                
                @if((auth()->user()?->auth_type === 'internal' || auth()->user()?->auth_type === null) && auth()->check())
                <form class="p-6 space-y-6" method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PATCH')
                    
                    <!-- Success/Error Messages -->
                    @if(session('success'))
                        <div class="bg-green-50 border border-green-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-green-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Current Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="current_password"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('current_password') border-red-300 @enderror"
                               required>
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            New Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="password"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-300 @enderror"
                               required>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm New Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="password_confirmation"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                               required>
                    </div>
                    
                    <!-- Warning Message -->
                    <div class="bg-amber-50 border border-amber-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-amber-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-amber-800">Important Security Notice</h3>
                                <div class="mt-2 text-sm text-amber-700">
                                    <p>Changing your password will:</p>
                                    <ul class="list-disc list-inside mt-1">
                                        <li>Log you out immediately</li>
                                        <li>Destroy all active sessions</li>
                                        <li>Require you to login again with your new password</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="button" 
                                onclick="confirmPasswordChange()" 
                                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 font-medium">
                            <i class="fas fa-shield-alt mr-2"></i>
                            Update Password
                        </button>
                    </div>
                    
                    <!-- Hidden submit button for actual form submission -->
                    <button type="submit" id="actualSubmitBtn" style="display: none;"></button>
                </form>
                @else
                <div class="p-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-yellow-800">Password changes are not available for {{ ucfirst(auth()->user()?->auth_type ?? 'unknown') }} authentication.</p>
                                <p class="text-sm text-yellow-700 mt-1">Your password is managed by your authentication provider.</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Notifications -->
            <div x-show="activeTab === 'notifications'" class="bg-white rounded-lg card-shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Notification Settings</h3>
                    <p class="text-sm text-gray-500">Configure how you receive notifications</p>
                </div>
                <div class="p-6">
                    <!-- Notification Channels -->
                    <div class="mb-8">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Notification Channels</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium">Email Notifications</div>
                                    <div class="text-sm text-gray-500">Receive notifications via email</div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           x-model="notifications.channels.email"
                                           class="sr-only peer"
                                           checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium">SMS Notifications</div>
                                    <div class="text-sm text-gray-500">Receive notifications via SMS</div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           x-model="notifications.channels.sms"
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium">Push Notifications</div>
                                    <div class="text-sm text-gray-500">Browser push notifications</div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           x-model="notifications.channels.push"
                                           class="sr-only peer"
                                           checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium">In-App Notifications</div>
                                    <div class="text-sm text-gray-500">Notifications within the application</div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           x-model="notifications.channels.in_app"
                                           class="sr-only peer"
                                           checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Types -->
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-4">Notification Types</h4>
                        <div class="space-y-4">
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <div class="font-medium">Transaction Alerts</div>
                                        <div class="text-sm text-gray-500">Notifications for new transactions</div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               x-model="notifications.types.transaction_alerts"
                                               class="sr-only peer"
                                               checked>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>
                                <div class="text-xs text-gray-400">
                                    Channels: email, push, in_app
                                </div>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <div class="font-medium">Security Alerts</div>
                                        <div class="text-sm text-gray-500">Important security-related notifications</div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               x-model="notifications.types.security_alerts"
                                               class="sr-only peer"
                                               checked
                                               disabled>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>
                                <div class="text-xs text-gray-400">
                                    Channels: email, sms, push
                                    <span class="text-red-500">(Required)</span>
                                </div>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <div class="font-medium">Weekly Summary</div>
                                        <div class="text-sm text-gray-500">Weekly financial summary reports</div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               x-model="notifications.types.weekly_summary"
                                               class="sr-only peer"
                                               checked>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>
                                <div class="text-xs text-gray-400">
                                    Channels: email
                                </div>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <div class="font-medium">Monthly Report</div>
                                        <div class="text-sm text-gray-500">Monthly financial reports</div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               x-model="notifications.types.monthly_report"
                                               class="sr-only peer"
                                               checked>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>
                                <div class="text-xs text-gray-400">
                                    Channels: email
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <button @click="updateNotifications()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 font-medium">
                            Save Preferences
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sessions -->
            <div x-show="activeTab === 'sessions'" class="bg-white rounded-lg card-shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Active Sessions</h3>
                    <p class="text-sm text-gray-500">Manage your active sessions and devices</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <template x-for="session in sessions" :key="session.id">
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-medium flex items-center">
                                            <i :class="session.device_icon" class="mr-2"></i>
                                            <span x-text="session.device"></span>
                                            <span x-show="session.current" class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Current</span>
                                        </div>
                                        <div class="text-sm text-gray-500 mt-1">
                                            <div x-text="session.location"></div>
                                            <div>Last active: <span x-text="session.last_active"></span></div>
                                        </div>
                                    </div>
                                    <button x-show="!session.current" 
                                            @click="revokeSession(session.id)"
                                            class="text-red-600 hover:text-red-700 text-sm font-medium">
                                        Revoke
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Success Message -->
    <div x-show="showSuccess" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span x-text="successMessage"></span>
        </div>
    </div>
</div>

<script>
function settingsData() {
    return {
        activeTab: 'profile',
        showSuccess: false,
        successMessage: '',
        showCreateToken: false,
        
        profile: {
            name: '@if(auth()->check()){{ auth()->user()?->name ?? "" }}@endif',
            email: '@if(auth()->check()){{ auth()->user()?->email ?? "" }}@endif',
            phone: '',
            timezone: '',
            language: 'en'
        },
        
        security: {
            current_password: '',
            password: '',
            password_confirmation: '',
            two_factor_enabled: false
        },
        
        notifications: {
            channels: {
                email: true,
                sms: false,
                push: true,
                in_app: true
            },
            types: {
                transaction_alerts: true,
                goal_updates: true,
                security_alerts: true,
                system_updates: false,
                weekly_summary: true,
                monthly_report: true
            }
        },
        
        sessions: [
            {
                id: 1,
                device: 'MacBook Pro',
                device_icon: 'fas fa-laptop',
                location: 'San Francisco, CA',
                last_active: '2 minutes ago',
                current: true
            },
            {
                id: 2,
                device: 'iPhone 14',
                device_icon: 'fas fa-mobile-alt',
                location: 'San Francisco, CA',
                last_active: '1 hour ago',
                current: false
            }
        ],
        
        tokens: [
            {
                id: 1,
                name: 'API Integration',
                created_at: '2025-07-15',
                last_used: '2025-07-20'
            }
        ],
        
        updateProfile() {
            // Simulate API call
            this.showSuccessMessage('Profile updated successfully!');
        },
        
        updatePassword() {
            // Simulate API call
            this.showSuccessMessage('Password updated successfully!');
            this.security = {
                current_password: '',
                password: '',
                password_confirmation: '',
                two_factor_enabled: this.security.two_factor_enabled
            };
        },
        
        updateNotifications() {
            // Simulate API call
            this.showSuccessMessage('Notification preferences saved!');
        },
        
        revokeSession(sessionId) {
            this.sessions = this.sessions.filter(s => s.id !== sessionId);
            this.showSuccessMessage('Session revoked successfully!');
        },
        
        revokeToken(tokenId) {
            this.tokens = this.tokens.filter(t => t.id !== tokenId);
            this.showSuccessMessage('Token revoked successfully!');
        },
        
        showSuccessMessage(message) {
            this.successMessage = message;
            this.showSuccess = true;
            setTimeout(() => {
                this.showSuccess = false;
            }, 3000);
        }
    }
}

function confirmPasswordChange() {
    if (confirm('⚠️ SECURITY WARNING ⚠️\n\nChanging your password will:\n• Log you out immediately\n• Destroy all active sessions\n• Require you to login again with your new password\n\nDo you want to continue?')) {
        document.getElementById('actualSubmitBtn').click();
    }
}
</script>
@endsection
