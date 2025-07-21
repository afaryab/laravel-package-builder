<?php

namespace LaravelApp\Http\Controllers;

use LaravelApp\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     */
    public function index()
    {
        // Mock data for the dashboard
        $data = [
            'user' => [
                'name' => 'Matt Shadows',
                'avatar' => 'https://ui-avatars.com/api/?name=Matt+Shadows&background=667eea&color=fff'
            ],
            'totalIncome' => 256000,
            'incomeGrowth' => 25000,
            'payments' => [
                'murabahah' => 1.00,
                'ijarah' => 20198,
                'musharakah' => 43092,
                'istisna' => 12662
            ],
            'recentTransactions' => [
                [
                    'name' => 'Amazon Support',
                    'date' => '12 Jan 2024',
                    'time' => '08:00 AM',
                    'invoice' => 'WWXS302',
                    'amount' => 0.00,
                    'fee' => 56,
                    'status' => 'Success'
                ],
                [
                    'name' => 'Upwork',
                    'date' => '12 Jan 2024',
                    'time' => '08:00 AM',
                    'invoice' => 'WWXS302',
                    'amount' => 0.00,
                    'fee' => 56,
                    'status' => 'Pending'
                ],
                [
                    'name' => 'EA Games',
                    'date' => '12 Jan 2024',
                    'time' => '08:00 AM',
                    'invoice' => 'WWXS302',
                    'amount' => 0.00,
                    'fee' => 56,
                    'status' => 'Pending'
                ],
                [
                    'name' => 'Apple Inc',
                    'date' => '12 Jan 2024',
                    'time' => '08:00 AM',
                    'invoice' => 'WWXS302',
                    'amount' => 0.00,
                    'fee' => 56,
                    'status' => 'Failed'
                ]
            ],
            'quickSendContacts' => [
                ['name' => 'Matt', 'avatar' => 'https://ui-avatars.com/api/?name=Matt&background=667eea&color=fff'],
                ['name' => 'Sarah', 'avatar' => 'https://ui-avatars.com/api/?name=Sarah&background=f093fb&color=fff'],
                ['name' => 'John', 'avatar' => 'https://ui-avatars.com/api/?name=John&background=4facfe&color=fff'],
                ['name' => 'Emma', 'avatar' => 'https://ui-avatars.com/api/?name=Emma&background=43e97b&color=fff'],
                ['name' => 'Alex', 'avatar' => 'https://ui-avatars.com/api/?name=Alex&background=feca57&color=fff'],
                ['name' => 'Lisa', 'avatar' => 'https://ui-avatars.com/api/?name=Lisa&background=ff6b6b&color=fff']
            ],
            'balance' => 300359
        ];

        return view('dashboard', compact('data'));
    }

    /**
     * Get chart data for income analytics
     */
    public function getIncomeChartData()
    {
        // Mock chart data - in real app, this would come from database
        $chartData = [
            'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7'],
            'data' => [40000, 60000, 45000, 80000, 65000, 90000, 75000],
            'growth' => [
                'current' => 256000,
                'previous' => 231000,
                'percentage' => 10.8
            ]
        ];

        return response()->json($chartData);
    }

    /**
     * Get recent transactions
     */
    public function getRecentTransactions(Request $request)
    {
        $type = $request->get('type', 'history'); // history or upcoming
        
        // Mock data - in real app, query database based on type
        $transactions = [
            [
                'id' => 1,
                'company' => 'Amazon Support',
                'date' => '2024-01-12',
                'time' => '08:00 AM',
                'invoice' => 'WWXS302',
                'amount' => 0.00,
                'fee' => 56,
                'status' => 'Success',
                'type' => 'credit'
            ],
            [
                'id' => 2,
                'company' => 'Upwork',
                'date' => '2024-01-12',
                'time' => '08:00 AM',
                'invoice' => 'WWXS302',
                'amount' => 0.00,
                'fee' => 56,
                'status' => 'Pending',
                'type' => 'credit'
            ],
            [
                'id' => 3,
                'company' => 'EA Games',
                'date' => '2024-01-12',
                'time' => '08:00 AM',
                'invoice' => 'WWXS302',
                'amount' => 0.00,
                'fee' => 56,
                'status' => 'Pending',
                'type' => 'debit'
            ],
            [
                'id' => 4,
                'company' => 'Apple Inc',
                'date' => '2024-01-12',
                'time' => '08:00 AM',
                'invoice' => 'WWXS302',
                'amount' => 0.00,
                'fee' => 56,
                'status' => 'Failed',
                'type' => 'debit'
            ]
        ];

        return response()->json($transactions);
    }

    /**
     * Send money to contact
     */
    public function sendMoney(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'recipient' => 'required|string',
            'note' => 'nullable|string|max:255'
        ]);

        // Mock sending money - in real app, process the transaction
        $transaction = [
            'id' => rand(1000, 9999),
            'amount' => $request->amount,
            'recipient' => $request->recipient,
            'status' => 'pending',
            'created_at' => now(),
            'fee' => $request->amount * 0.01 // 1% fee
        ];

        return response()->json([
            'success' => true,
            'message' => 'Money sent successfully!',
            'transaction' => $transaction
        ]);
    }
}
