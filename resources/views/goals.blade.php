@extends('layouts.dashboard')

@section('title', 'Goals - Sharia Finance')

@section('content')
<div class="p-6" x-data="goalsData()">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Financial Goals</h1>
            <p class="text-gray-600 mt-1">Track and manage your financial objectives</p>
        </div>
        <button @click="showAddGoal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Add Goal
        </button>
    </div>

    <!-- Goals Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
        <template x-for="goal in goals" :key="goal.id">
            <div class="bg-white rounded-2xl p-6 card-shadow metric-card">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center mr-3"
                             :class="getGoalIconColor(goal.category)">
                            <i :class="getGoalIcon(goal.category)" class="text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900" x-text="goal.title"></h3>
                            <p class="text-sm text-gray-500" x-text="goal.category"></p>
                        </div>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Progress</span>
                        <span class="text-sm font-medium text-gray-900" x-text="Math.round((goal.current / goal.target) * 100) + '%'"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" 
                             :style="`width: ${Math.min((goal.current / goal.target) * 100, 100)}%`"></div>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Current:</span>
                        <span class="text-sm font-medium" x-text="formatCurrency(goal.current)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Target:</span>
                        <span class="text-sm font-medium" x-text="formatCurrency(goal.target)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Deadline:</span>
                        <span class="text-sm font-medium" x-text="formatDate(goal.deadline)"></span>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <button @click="addToGoal(goal)" class="w-full bg-indigo-50 text-indigo-600 py-2 px-4 rounded-lg font-medium hover:bg-indigo-100 transition">
                        Add Funds
                    </button>
                </div>
            </div>
        </template>
    </div>

    <!-- Goals Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-6 card-shadow">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900" x-text="completedGoals">3</p>
                    <p class="text-sm text-gray-600">Completed Goals</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 card-shadow">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-target text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900" x-text="activeGoals">5</p>
                    <p class="text-sm text-gray-600">Active Goals</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 card-shadow">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(totalSaved)">$45,230</p>
                    <p class="text-sm text-gray-600">Total Saved</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Goal Modal -->
    <div x-show="showAddGoal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form @submit.prevent="addGoal()">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Add New Goal</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Goal Title</label>
                                <input type="text" x-model="newGoal.title" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <select x-model="newGoal.category" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="Emergency Fund">Emergency Fund</option>
                                    <option value="Home">Home</option>
                                    <option value="Car">Car</option>
                                    <option value="Education">Education</option>
                                    <option value="Vacation">Vacation</option>
                                    <option value="Investment">Investment</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Target Amount</label>
                                <input type="number" x-model="newGoal.target" required min="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Deadline</label>
                                <input type="date" x-model="newGoal.deadline" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Create Goal
                        </button>
                        <button @click="showAddGoal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function goalsData() {
    return {
        showAddGoal: false,
        completedGoals: 3,
        activeGoals: 5,
        totalSaved: 45230,
        
        goals: [
            {
                id: 1,
                title: 'Emergency Fund',
                category: 'Emergency Fund',
                current: 15000,
                target: 20000,
                deadline: '2024-12-31'
            },
            {
                id: 2,
                title: 'New Car',
                category: 'Car',
                current: 25000,
                target: 40000,
                deadline: '2025-06-30'
            },
            {
                id: 3,
                title: 'House Down Payment',
                category: 'Home',
                current: 80000,
                target: 100000,
                deadline: '2025-12-31'
            },
            {
                id: 4,
                title: 'Vacation Fund',
                category: 'Vacation',
                current: 3500,
                target: 8000,
                deadline: '2024-08-15'
            },
            {
                id: 5,
                title: 'Education Fund',
                category: 'Education',
                current: 12000,
                target: 50000,
                deadline: '2026-09-01'
            }
        ],
        
        newGoal: {
            title: '',
            category: 'Emergency Fund',
            target: '',
            deadline: ''
        },
        
        formatCurrency(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        },
        
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        },
        
        getGoalIcon(category) {
            const icons = {
                'Emergency Fund': 'fas fa-shield-alt',
                'Home': 'fas fa-home',
                'Car': 'fas fa-car',
                'Education': 'fas fa-graduation-cap',
                'Vacation': 'fas fa-plane',
                'Investment': 'fas fa-chart-line'
            };
            return icons[category] || 'fas fa-bullseye';
        },
        
        getGoalIconColor(category) {
            const colors = {
                'Emergency Fund': 'bg-red-500',
                'Home': 'bg-blue-500',
                'Car': 'bg-green-500',
                'Education': 'bg-purple-500',
                'Vacation': 'bg-yellow-500',
                'Investment': 'bg-indigo-500'
            };
            return colors[category] || 'bg-gray-500';
        },
        
        addGoal() {
            const goal = {
                id: this.goals.length + 1,
                title: this.newGoal.title,
                category: this.newGoal.category,
                current: 0,
                target: parseInt(this.newGoal.target),
                deadline: this.newGoal.deadline
            };
            
            this.goals.push(goal);
            this.activeGoals++;
            
            // Reset form
            this.newGoal = {
                title: '',
                category: 'Emergency Fund',
                target: '',
                deadline: ''
            };
            
            this.showAddGoal = false;
        },
        
        addToGoal(goal) {
            const amount = prompt(`How much would you like to add to "${goal.title}"?`);
            if (amount && !isNaN(amount) && amount > 0) {
                goal.current += parseInt(amount);
                this.totalSaved += parseInt(amount);
                
                if (goal.current >= goal.target) {
                    alert(`Congratulations! You've reached your goal for "${goal.title}"!`);
                    this.completedGoals++;
                    this.activeGoals--;
                }
            }
        }
    }
}
</script>
@endsection
