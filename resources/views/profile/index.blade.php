@extends('layouts.dashboard')

@section('title', 'Profile')

@section('content')
<div class="bg-white overflow-hidden">
    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">Profile</h2>
                    <p class="mt-1 text-sm text-gray-500">View your personal information and account details</p>
                </div>
                <div class="mt-4 md:mt-0">
                    @if(auth()->user()->auth_type === 'internal' || empty(auth()->user()->auth_type))
                        <a href="{{ route('profile.edit') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Edit Profile
                        </a>
                    @else
                        <div class="text-center">
                            <p class="text-sm text-gray-600 mb-2">Profile managed by {{ ucfirst(auth()->user()->auth_type) }}</p>
                            @if(auth()->user()->auth_type === 'oauth')
                                <a href="#" onclick="alert('Please visit your OAuth provider to edit your profile.')" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    Edit via OAuth Provider
                                </a>
                            @elseif(auth()->user()->auth_type === 'saml')
                                <a href="#" onclick="alert('Please visit your SAML identity provider to edit your profile.')" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    Edit via Identity Provider
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Profile Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4">
                            <div class="flex flex-col items-center">
                                <div class="flex-shrink-0">
                                    <img class="h-24 w-24 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=667eea&color=fff&size=200" alt="{{ auth()->user()->name }}">
                                </div>
                                <div class="mt-4 text-center">
                                    <h3 class="text-xl font-medium text-gray-900">{{ auth()->user()->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ ucfirst(auth()->user()->auth_type ?? 'internal') }} User
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-200">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Member since</dt>
                                    <dd class="text-sm text-gray-900">{{ auth()->user()->created_at->format('M d, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Last updated</dt>
                                    <dd class="text-sm text-gray-900">{{ auth()->user()->updated_at->format('M d, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">User ID</dt>
                                    <dd class="text-sm text-gray-900">#{{ auth()->user()->id }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Profile Information -->
                <div class="lg:col-span-2">
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Profile Information</h3>
                            <p class="mt-1 text-sm text-gray-500">Your account details and preferences.</p>
                        </div>
                        <div class="px-6 py-4">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ auth()->user()->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ auth()->user()->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Authentication Type</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(auth()->user()->auth_type ?? 'internal') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Account Status</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Active
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="mt-6 bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
                            <p class="mt-1 text-sm text-gray-500">Your recent account activity.</p>
                        </div>
                        <div class="px-6 py-4">
                            <div class="flow-root">
                                <ul class="-my-5 divide-y divide-gray-200">
                                    <li class="py-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                                    <i class="fas fa-sign-in-alt text-green-600 text-sm"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900">Signed in</p>
                                                <p class="text-sm text-gray-500">Last login session</p>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Today
                                            </div>
                                        </div>
                                    </li>
                                    <li class="py-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900">Profile created</p>
                                                <p class="text-sm text-gray-500">Account was created</p>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ auth()->user()->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
