@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')
<div class="p-6" x-data="dashboardData()">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Good Morning, {{ auth()->user()->name ?? 'User' }}!</h1>
        <p class="text-gray-600 mt-1">Welcome to your Admin Dashboard</p>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 gap-6">
        <!-- Full Width Content -->
        <div class="space-y-6">
            <!-- Payment Overview -->
            <div class="bg-white rounded-2xl p-6 card-shadow">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Overview</h3>
                    <p class="text-sm text-gray-500">Current users and tokens in the system</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-100 rounded-2xl p-6 hover:bg-blue-200 transition cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <x-heroicon-o-users class="w-8 h-8 text-blue-600" />
                            <span class="text-xs text-blue-600 bg-blue-200 px-2 py-1 rounded-full font-medium">Active</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-1">Total Users</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</p>
                        <div class="flex items-center mt-3">
                            <x-heroicon-s-arrow-up class="w-3 h-3 text-green-500 mr-1" />
                            <span class="text-xs text-green-600 font-medium">+{{ $growthPercentage }}% from last month</span>
                        </div>
                    </div>
                    <div class="bg-green-100 rounded-2xl p-6 hover:bg-green-200 transition cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <x-heroicon-o-user-group class="w-8 h-8 text-green-600" />
                            <span class="text-xs text-green-600 bg-green-200 px-2 py-1 rounded-full font-medium">Active</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-1">Active Tokens</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $totalTokens }}</p>
                        <div class="flex items-center mt-3">
                            <x-heroicon-s-arrow-up class="w-3 h-3 text-green-500 mr-1" />
                            <span class="text-xs text-green-600 font-medium">Users active in last 30 days</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-2xl p-6 card-shadow">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Recent Activity</h3>
                        <p class="text-sm text-gray-500">Latest system activities and user actions</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2 text-sm text-gray-500">
                            <x-heroicon-o-calendar class="w-4 h-4" />
                            <span>Last 30 days</span>
                        </div>
                        <button @click="refreshActivities()" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-full w-10 h-10 flex items-center justify-center transition">
                            <x-heroicon-o-arrow-path class="w-5 h-5" />
                        </button>
                    </div>
                </div>
                
                <div class="border-b border-gray-200 mb-4">
                    <nav class="-mb-px flex space-x-8">
                        <button @click="activeTab = 'recent'; currentPage = 1;" 
                                :class="activeTab === 'recent' ? 'border-b-2 border-indigo-500 text-indigo-600' : 'border-b-2 border-transparent text-gray-500 hover:text-gray-700'" 
                                class="py-2 px-1 text-sm font-medium">
                            Recent
                        </button>
                        <button @click="activeTab = 'users'; currentPage = 1;" 
                                :class="activeTab === 'users' ? 'border-b-2 border-indigo-500 text-indigo-600' : 'border-b-2 border-transparent text-gray-500 hover:text-gray-700'" 
                                class="py-2 px-1 text-sm font-medium">
                            User Activities
                        </button>
                        <button @click="activeTab = 'integrations'; currentPage = 1;" 
                                :class="activeTab === 'integrations' ? 'border-b-2 border-indigo-500 text-indigo-600' : 'border-b-2 border-transparent text-gray-500 hover:text-gray-700'" 
                                class="py-2 px-1 text-sm font-medium">
                            Integration Activities
                        </button>
                        <button @click="activeTab = 'system'; currentPage = 1;" 
                                :class="activeTab === 'system' ? 'border-b-2 border-indigo-500 text-indigo-600' : 'border-b-2 border-transparent text-gray-500 hover:text-gray-700'" 
                                class="py-2 px-1 text-sm font-medium">
                            System
                        </button>
                    </nav>
                </div>
                
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="activity in filteredActivities" :key="activity.id">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full flex items-center justify-center"
                                                     :class="{
                                                         'bg-blue-100 text-blue-600': activity.type === 'User',
                                                         'bg-green-100 text-green-600': activity.type === 'Integration', 
                                                         'bg-purple-100 text-purple-600': activity.type === 'System'
                                                     }">
                                                    <x-heroicon-o-user class="w-5 h-5" x-show="activity.type === 'User'" />
                                                    <x-heroicon-o-globe-alt class="w-5 h-5" x-show="activity.type === 'Integration'" />
                                                    <x-heroicon-o-cog-6-tooth class="w-5 h-5" x-show="activity.type === 'System'" />
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900" x-text="activity.description"></div>
                                                <div class="text-sm text-gray-500" x-text="activity.details || 'No additional details'"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900" x-text="activity.user"></div>
                                        <div class="text-sm text-gray-500" x-text="activity.ip_address || 'Unknown IP'"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900" x-text="activity.created_at"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                              :class="{
                                                  'bg-green-100 text-green-800': activity.status >= 200 && activity.status < 300,
                                                  'bg-yellow-100 text-yellow-800': activity.status >= 300 && activity.status < 400,
                                                  'bg-red-100 text-red-800': activity.status >= 400,
                                                  'bg-gray-100 text-gray-800': activity.status === 'N/A'
                                              }"
                                              x-text="activity.status">
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button @click="viewActivityDetails(activity)" class="text-indigo-600 hover:text-indigo-900 mr-3">View</button>
                                        <button @click="downloadLog(activity)" class="text-gray-600 hover:text-gray-900">
                                            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <!-- Empty state -->
                            <tr x-show="filteredActivities.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="text-gray-400">
                                        <x-heroicon-o-inbox class="mx-auto h-12 w-12 mb-4" />
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">No activities found</h3>
                                        <p class="text-sm text-gray-500">
                                            <span x-show="activeTab === 'recent'">No recent activities to display.</span>
                                            <span x-show="activeTab === 'users'">No user activities found.</span>
                                            <span x-show="activeTab === 'integrations'">No integration activities found.</span>
                                            <span x-show="activeTab === 'system'">No system activities found.</span>
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-6 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing <span class="font-medium" x-text="(currentPage - 1) * itemsPerPage + 1"></span> to 
                        <span class="font-medium" x-text="Math.min(currentPage * itemsPerPage, totalActivities)"></span> of 
                        <span class="font-medium" x-text="totalActivities"></span> results
                    </div>
                    <nav class="flex space-x-2">
                        <button @click="previousPage()" :disabled="currentPage === 1" 
                                :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                                class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md">
                            Previous
                        </button>
                        <button @click="nextPage()" :disabled="currentPage === totalPages"
                                :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                                class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md">
                            Next
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function dashboardData() {
    return {
        // Data properties
        activeTab: 'recent',
        currentPage: 1,
        itemsPerPage: 10,
        totalActivities: {{ count($activities) }},
        
        stats: {
            totalUsers: {{ $totalUsers }},
            activeTokens: {{ $totalTokens }}
        },
        
        activities: @json($activities),
        
        // Computed properties
        get filteredActivities() {
            let filtered = this.activities;
            
            if (this.activeTab === 'users') {
                filtered = filtered.filter(activity => activity.type === 'User');
            } else if (this.activeTab === 'integrations') {
                filtered = filtered.filter(activity => activity.type === 'Integration');
            } else if (this.activeTab === 'system') {
                filtered = filtered.filter(activity => activity.type === 'System');
            }
            
            // Update totalActivities based on filtered results
            this.totalActivities = filtered.length;
            
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return filtered.slice(start, end);
        },
        
        get totalPages() {
            // Get the current filtered count
            let filtered = this.activities;
            
            if (this.activeTab === 'users') {
                filtered = filtered.filter(activity => activity.type === 'User');
            } else if (this.activeTab === 'integrations') {
                filtered = filtered.filter(activity => activity.type === 'Integration');
            } else if (this.activeTab === 'system') {
                filtered = filtered.filter(activity => activity.type === 'System');
            }
            
            return Math.ceil(filtered.length / this.itemsPerPage);
        },
        
        // Methods
        async refreshActivities() {
            try {
                const response = await fetch('/dashboard/activities?type=' + this.activeTab);
                const data = await response.json();
                this.activities = data;
                this.totalActivities = data.length;
                this.currentPage = 1; // Reset to first page
                console.log('Activities refreshed successfully');
            } catch (error) {
                console.error('Error refreshing activities:', error);
            }
        },
        
        viewActivityDetails(activity) {
            alert(`Activity Details:\n\nType: ${activity.type}\nDescription: ${activity.description}\nUser: ${activity.user}\nIP Address: ${activity.ip_address}\nStatus: ${activity.status}\nTime: ${activity.created_at}\nDetails: ${activity.details}`);
        },
        
        downloadLog(activity) {
            console.log('Downloading log for activity:', activity.id);
            // In a real app, this would trigger a download
            alert('Log download would start for activity: ' + activity.description);
        },
        
        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard loaded successfully with real data');
    console.log('Activities data:', @json($activities));
    console.log('Total activities:', {{ count($activities) }});
});
</script>
@endsection
