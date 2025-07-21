@extends('layouts.dashboard')

@section('title', 'Finance Management Dashboard')

@section('content')
<div class="p-6" x-data="dashboardData()">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Good Morning, Matt!</h1>
        <p class="text-gray-600 mt-1">Welcome to Finance Management Dashboard</p>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Total Income Card -->
            <div class="bg-white rounded-2xl p-6 card-shadow metric-card">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Total Income</h3>
                        <p class="text-sm text-gray-500">View your income in a certain period of time</p>
                    </div>
                    <button class="text-indigo-600 text-sm font-medium hover:text-indigo-700 flex items-center">
                        SEE MORE <i class="fas fa-arrow-right ml-1"></i>
                    </button>
                </div>
                
                <div class="flex items-end justify-between">
                    <div>
                        <div class="text-4xl font-bold text-gray-900 mb-2" x-text="formatCurrency(totalIncome)">$ 256K</div>
                        <div class="flex items-center text-sm">
                            <span class="text-green-600 bg-green-50 px-2 py-1 rounded-full text-xs font-medium mr-2 flex items-center">
                                <i class="fas fa-arrow-up mr-1"></i>
                                <span x-text="incomeGrowth + '%'">12%</span>
                            </span>
                            <span class="text-gray-600">BETWEEN AUG 3-31</span>
                        </div>
                        <p class="text-gray-500 text-sm mt-1" x-text="formatCurrency(monthlyAverage)">25K</p>
                    </div>
                    
                    <!-- Chart Container -->
                    <div class="flex-1 ml-8">
                        <canvas id="incomeChart" width="300" height="120"></canvas>
                    </div>
                </div>
            </div>

            <!-- Payment Overview -->
            <div class="bg-white rounded-2xl p-6 card-shadow">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Payment Overview</h3>
                    <p class="text-sm text-gray-500">View your income in a certain period of time</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-orange-100 rounded-2xl p-4 hover:bg-orange-200 transition cursor-pointer" @click="showPaymentDetails('murabahah')">
                        <p class="text-sm text-gray-600 mb-1">Murabahah</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(payments.murabahah)">$1.00</p>
                        <div class="flex items-center mt-2">
                            <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded-full">+2.3%</span>
                        </div>
                    </div>
                    <div class="bg-cyan-100 rounded-2xl p-4 hover:bg-cyan-200 transition cursor-pointer" @click="showPaymentDetails('ijarah')">
                        <p class="text-sm text-gray-600 mb-1">Ijarah</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(payments.ijarah)">$20,198</p>
                        <div class="flex items-center mt-2">
                            <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded-full">+5.7%</span>
                        </div>
                    </div>
                    <div class="bg-green-100 rounded-2xl p-4 hover:bg-green-200 transition cursor-pointer" @click="showPaymentDetails('musharakah')">
                        <p class="text-sm text-gray-600 mb-1">Musharakah</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(payments.musharakah)">$43,092</p>
                        <div class="flex items-center mt-2">
                            <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded-full">+1.2%</span>
                        </div>
                    </div>
                    <div class="bg-purple-100 rounded-2xl p-4 hover:bg-purple-200 transition cursor-pointer" @click="showPaymentDetails('istisna')">
                        <p class="text-sm text-gray-600 mb-1">Istisna</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(payments.istisna)">$12,662</p>
                        <div class="flex items-center mt-2">
                            <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded-full">-0.8%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-2xl p-6 card-shadow">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Recent activity</h3>
                        <p class="text-sm text-gray-500">View your recent transaction certain period of time</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2 text-sm text-gray-500">
                            <i class="fas fa-calendar-alt"></i>
                            <span>12 January 2024 - 12 February 2024</span>
                        </div>
                        <button @click="showAddTransaction = true" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-full w-10 h-10 flex items-center justify-center transition">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                
                <div class="border-b border-gray-200 mb-4">
                    <nav class="-mb-px flex space-x-8">
                        <button @click="activeTab = 'history'" 
                                :class="activeTab === 'history' ? 'border-b-2 border-indigo-500 text-indigo-600' : 'border-b-2 border-transparent text-gray-500 hover:text-gray-700'" 
                                class="py-2 px-1 text-sm font-medium">
                            History
                        </button>
                        <button @click="activeTab = 'upcoming'" 
                                :class="activeTab === 'upcoming' ? 'border-b-2 border-indigo-500 text-indigo-600' : 'border-b-2 border-transparent text-gray-500 hover:text-gray-700'" 
                                class="py-2 px-1 text-sm font-medium">
                            Upcoming
                        </button>
                    </nav>
                </div>
                
                <div class="space-y-4">
                    <template x-for="transaction in transactions" :key="transaction.id">
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 hover:bg-gray-50 rounded-lg px-2 transition cursor-pointer"
                             @click="showTransactionDetails(transaction)">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-building text-white text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900" x-text="transaction.name"></p>
                                    <p class="text-sm text-gray-500" x-text="transaction.category"></p>
                                </div>
                            </div>
                            <div class="text-sm text-gray-500">
                                <p x-text="transaction.date"></p>
                                <p x-text="transaction.time"></p>
                            </div>
                            <div class="text-sm text-gray-500" x-text="transaction.invoice"></div>
                            <div class="text-right">
                                <p class="font-medium text-gray-900" x-text="formatCurrency(transaction.amount)"></p>
                                <p class="text-sm text-green-600" x-text="'+' + formatCurrency(transaction.fee)"></p>
                            </div>
                            <div>
                                <span class="px-3 py-1 rounded-full text-xs font-medium"
                                      :class="{
                                          'bg-green-100 text-green-800': transaction.status === 'Success',
                                          'bg-yellow-100 text-yellow-800': transaction.status === 'Pending',
                                          'bg-red-100 text-red-800': transaction.status === 'Failed'
                                      }"
                                      x-text="transaction.status">
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Quick Send -->
            <div class="bg-white rounded-2xl p-6 card-shadow">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Quick send</h3>
                    <p class="text-sm text-gray-500">Send money to your friends quickly</p>
                </div>
                
                <div class="flex -space-x-2 mb-6">
                    <template x-for="(contact, index) in quickContacts" :key="index">
                        <img class="w-10 h-10 rounded-full border-2 border-white cursor-pointer hover:scale-110 transition-transform" 
                             :src="contact.avatar" 
                             :alt="contact.name"
                             @click="selectedContact = contact">
                    </template>
                    <button class="w-10 h-10 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition">
                        <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm text-gray-500 mb-2">SEND AMOUNT</p>
                    <div class="flex items-center justify-between bg-gray-50 rounded-xl p-4">
                        <input type="number" 
                               x-model="sendAmount" 
                               class="text-2xl font-bold text-gray-900 bg-transparent border-none outline-none w-full"
                               placeholder="100,000">
                        <div class="flex items-center">
                            <span class="text-sm text-gray-500 mr-2" x-text="'Balance: ' + formatCurrency(balance)">Balance: $300,359</span>
                            <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa" class="h-6">
                        </div>
                    </div>
                </div>
                
                <button @click="sendMoney()" 
                        :disabled="!sendAmount || !selectedContact"
                        :class="sendAmount && selectedContact ? 'bg-gray-900 hover:bg-gray-800' : 'bg-gray-400 cursor-not-allowed'"
                        class="w-full text-white font-medium py-3 px-4 rounded-xl transition">
                    <span x-show="!isSending">Send</span>
                    <span x-show="isSending" class="flex items-center justify-center">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Sending...
                    </span>
                </button>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-2xl p-6 card-shadow">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Stats</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">This Month</span>
                        <span class="font-semibold text-green-600" x-text="formatCurrency(monthlyTotal)">+$45,230</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Last Month</span>
                        <span class="font-semibold text-gray-900" x-text="formatCurrency(lastMonthTotal)">$38,940</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Growth</span>
                        <span class="font-semibold text-green-600">+16.2%</span>
                    </div>
                </div>
                <div class="mt-6 h-24 bg-gradient-to-r from-blue-400 to-purple-500 rounded-xl flex items-center justify-center">
                    <canvas id="miniChart" width="200" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Send Money Success Modal -->
    <div x-show="showSuccessModal" 
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
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Transfer Successful!</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Successfully sent <span class="font-medium" x-text="formatCurrency(lastSentAmount)"></span> 
                                    to <span class="font-medium" x-text="lastSentTo"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="showSuccessModal = false" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Done
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function dashboardData() {
    return {
        // Data properties
        totalIncome: 256000,
        incomeGrowth: 12,
        monthlyAverage: 25000,
        balance: 300359,
        sendAmount: '',
        selectedContact: null,
        isSending: false,
        showSuccessModal: false,
        lastSentAmount: 0,
        lastSentTo: '',
        activeTab: 'history',
        monthlyTotal: 45230,
        lastMonthTotal: 38940,
        
        payments: {
            murabahah: 1.00,
            ijarah: 20198,
            musharakah: 43092,
            istisna: 12662
        },
        
        transactions: [
            {
                id: 1,
                name: 'Amazon Support',
                category: 'E-commerce',
                date: '12 Jan 2024',
                time: '08:00 AM',
                invoice: 'WWXS302',
                amount: 0.00,
                fee: 56,
                status: 'Success'
            },
            {
                id: 2,
                name: 'Upwork',
                category: 'Freelance',
                date: '12 Jan 2024',
                time: '08:00 AM',
                invoice: 'WWXS302',
                amount: 0.00,
                fee: 56,
                status: 'Pending'
            },
            {
                id: 3,
                name: 'EA Games',
                category: 'Entertainment',
                date: '12 Jan 2024',
                time: '08:00 AM',
                invoice: 'WWXS302',
                amount: 0.00,
                fee: 56,
                status: 'Pending'
            },
            {
                id: 4,
                name: 'Apple Inc',
                category: 'Technology',
                date: '12 Jan 2024',
                time: '08:00 AM',
                invoice: 'WWXS302',
                amount: 0.00,
                fee: 56,
                status: 'Failed'
            }
        ],
        
        quickContacts: [
            { name: 'Matt', avatar: 'https://ui-avatars.com/api/?name=Matt&background=667eea&color=fff' },
            { name: 'Sarah', avatar: 'https://ui-avatars.com/api/?name=Sarah&background=f093fb&color=fff' },
            { name: 'John', avatar: 'https://ui-avatars.com/api/?name=John&background=4facfe&color=fff' },
            { name: 'Emma', avatar: 'https://ui-avatars.com/api/?name=Emma&background=43e97b&color=fff' },
            { name: 'Alex', avatar: 'https://ui-avatars.com/api/?name=Alex&background=feca57&color=fff' },
            { name: 'Lisa', avatar: 'https://ui-avatars.com/api/?name=Lisa&background=ff6b6b&color=fff' }
        ],
        
        // Methods
        formatCurrency(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        },
        
        async sendMoney() {
            if (!this.sendAmount || !this.selectedContact) return;
            
            this.isSending = true;
            
            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 1500));
            
            this.lastSentAmount = this.sendAmount;
            this.lastSentTo = this.selectedContact.name;
            this.balance -= parseFloat(this.sendAmount);
            
            this.isSending = false;
            this.showSuccessModal = true;
            this.sendAmount = '';
            this.selectedContact = null;
        },
        
        showPaymentDetails(type) {
            alert(`Payment details for ${type}: ${this.formatCurrency(this.payments[type])}`);
        },
        
        showTransactionDetails(transaction) {
            alert(`Transaction: ${transaction.name}\nAmount: ${this.formatCurrency(transaction.amount)}\nStatus: ${transaction.status}`);
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Income Chart
    const ctx = document.getElementById('incomeChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                data: [40, 60, 45, 80, 65, 90, 75],
                backgroundColor: '#3B82F6',
                borderRadius: 6,
                borderSkipped: false,
                barThickness: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { display: false },
                y: { display: false }
            },
            elements: {
                bar: { borderRadius: 6 }
            }
        }
    });
    
    // Mini Chart
    const miniCtx = document.getElementById('miniChart').getContext('2d');
    new Chart(miniCtx, {
        type: 'line',
        data: {
            labels: ['', '', '', '', '', '', ''],
            datasets: [{
                data: [30, 45, 35, 60, 50, 75, 65],
                borderColor: '#FFFFFF',
                backgroundColor: 'rgba(255, 255, 255, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { display: false },
                y: { display: false }
            }
        }
    });
});
</script>
@endsection
