<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Menu Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the menu structure and permissions for the application.
    | Menus are organized into backend and settings categories with their
    | respective permissions and routing information.
    |
    */

    'menus' => [
                'backend' => [
            'dashboard' => [
                'label' => 'Dashboard',
                'route' => 'dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'description' => 'Overview and analytics',
                'permission' => 'dashboard.view',
                'order' => 1,
                'active' => true,
            ],
            'settings' => [
                'label' => 'Settings',
                'route' => 'settings.index',
                'icon' => 'fas fa-cog',
                'description' => 'System settings and configuration',
                'permission' => 'settings.view',
                'order' => 2,
                'active' => true,
                'submenu' => [
                    'users' => [
                        'label' => 'Users',
                        'route' => 'users.index',
                        'icon' => 'fas fa-users',
                        'description' => 'Manage system users',
                        'permission' => 'users.view',
                        'order' => 1,
                        'active' => true,
                    ],
                    'access_control' => [
                        'label' => 'Access Control',
                        'route' => 'access-control.index',
                        'icon' => 'fas fa-shield-alt',
                        'description' => 'Manage roles and permissions',
                        'permission' => 'roles.view',
                        'order' => 2,
                        'active' => true,
                    ],
                    'setup' => [
                        'label' => 'Setup',
                        'route' => 'setup',
                        'icon' => 'fas fa-tools',
                        'description' => 'System configuration and setup',
                        'permission' => 'settings.access',
                        'order' => 3,
                        'active' => true,
                        'tabs' => [
                            'authentication' => [
                                'label' => 'Authentication',
                                'icon' => 'fas fa-key',
                                'order' => 1,
                                'description' => 'Authentication providers and configuration',
                                'route' => 'authentication'
                            ],
                            'integrations' => [
                                'label' => 'Integration Tokens',
                                'icon' => 'fas fa-plug',
                                'order' => 2,
                                'description' => 'API tokens and integration settings',
                                'route' => 'tokens'
                            ]
                        ]
                    ]
                ]
            ],
        ],

        'profile' => [
            // Profile menu accessed from top bar user dropdown
            'tabs' => [
                'profile' => [
                    'label' => 'Profile',
                    'icon' => 'fas fa-user',
                    'route' => 'profile',
                    'order' => 1,
                    'description' => 'View profile information'
                ],
                'settings' => [
                    'label' => 'Settings',
                    'icon' => 'fas fa-user-edit',
                    'route' => 'settings.profile',
                    'order' => 2,
                    'description' => 'Edit profile information',
                    'fields' => [
                        'name' => [
                            'type' => 'text',
                            'label' => 'Full Name',
                            'required' => true,
                            'validation' => 'required|string|max:255'
                        ],
                        'email' => [
                            'type' => 'email',
                            'label' => 'Email Address',
                            'required' => true,
                            'validation' => 'required|email|unique:users,email'
                        ],
                        'phone' => [
                            'type' => 'tel',
                            'label' => 'Phone Number',
                            'required' => false,
                            'validation' => 'nullable|string|max:20'
                        ],
                        'timezone' => [
                            'type' => 'select',
                            'label' => 'Timezone',
                            'required' => false,
                            'options' => 'timezones',
                            'validation' => 'nullable|string'
                        ],
                        'language' => [
                            'type' => 'select',
                            'label' => 'Preferred Language',
                            'required' => false,
                            'options' => 'languages',
                            'validation' => 'nullable|string'
                        ]
                    ]
                ],
                'security' => [
                    'label' => 'Security',
                    'icon' => 'fas fa-shield-alt',
                    'route' => 'settings.security',
                    'order' => 3,
                    'description' => 'Password and security settings',
                    'fields' => [
                        'current_password' => [
                            'type' => 'password',
                            'label' => 'Current Password',
                            'required' => true,
                            'validation' => 'required|string'
                        ],
                        'password' => [
                            'type' => 'password',
                            'label' => 'New Password',
                            'required' => true,
                            'validation' => 'required|string|min:8|confirmed'
                        ],
                        'password_confirmation' => [
                            'type' => 'password',
                            'label' => 'Confirm New Password',
                            'required' => true,
                            'validation' => 'required|string|min:8'
                        ],
                        'two_factor_enabled' => [
                            'type' => 'checkbox',
                            'label' => 'Enable Two-Factor Authentication',
                            'required' => false,
                            'validation' => 'nullable|boolean'
                        ]
                    ]
                ],
                'notifications' => [
                    'label' => 'Notifications',
                    'icon' => 'fas fa-bell',
                    'route' => 'settings.notifications',
                    'order' => 4,
                    'description' => 'Notification preferences and settings'
                ],
                'sessions' => [
                    'label' => 'Sessions',
                    'icon' => 'fas fa-desktop',
                    'route' => 'settings.sessions',
                    'order' => 5,
                    'description' => 'Active sessions and device management'
                ]
            ]
        ],

        'settings' => [
            // Legacy settings routes for backward compatibility
            'profile' => [
                'label' => 'Profile Settings',
                'icon' => 'fas fa-user',
                'route' => 'settings.profile',
                'permission' => 'settings.profile',
                'order' => 1,
                'active' => false, // Hidden, redirects to main profile
                'description' => 'Manage personal profile information'
            ],
            'security' => [
                'label' => 'Security',
                'icon' => 'fas fa-shield-alt',
                'route' => 'settings.security',
                'permission' => 'settings.security',
                'order' => 2,
                'active' => false, // Hidden, redirects to main profile
                'description' => 'Password and security settings'
            ],
            'notifications' => [
                'label' => 'Notifications',
                'icon' => 'fas fa-bell',
                'route' => 'settings.notifications',
                'permission' => 'settings.notifications',
                'order' => 3,
                'active' => false, // Hidden, redirects to main profile
                'description' => 'Notification preferences and settings'
            ],
            'sessions' => [
                'label' => 'Sessions',
                'icon' => 'fas fa-desktop',
                'route' => 'settings.sessions',
                'permission' => 'settings.sessions',
                'order' => 4,
                'active' => false, // Hidden, redirects to main profile
                'description' => 'Active sessions and device management'
            ],
            'tokens' => [
                'label' => 'Integration Tokens',
                'icon' => 'fas fa-key',
                'route' => 'settings.tokens',
                'permission' => 'settings.tokens',
                'order' => 5,
                'active' => false, // Hidden, redirects to setup
                'description' => 'API tokens for integrations'
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Configuration
    |--------------------------------------------------------------------------
    |
    | Configure notification types and their default settings
    |
    */

    'notifications' => [
        'channels' => [
            'email' => [
                'label' => 'Email Notifications',
                'description' => 'Receive notifications via email',
                'enabled' => true
            ],
            'sms' => [
                'label' => 'SMS Notifications',
                'description' => 'Receive notifications via SMS',
                'enabled' => false
            ],
            'push' => [
                'label' => 'Push Notifications',
                'description' => 'Browser push notifications',
                'enabled' => true
            ],
            'in_app' => [
                'label' => 'In-App Notifications',
                'description' => 'Notifications within the application',
                'enabled' => true
            ]
        ],

        'types' => [
            'transaction_alerts' => [
                'label' => 'Transaction Alerts',
                'description' => 'Notifications for new transactions',
                'channels' => ['email', 'push', 'in_app'],
                'default_enabled' => true
            ],
            'goal_updates' => [
                'label' => 'Goal Updates',
                'description' => 'Notifications when goals are achieved or updated',
                'channels' => ['email', 'push', 'in_app'],
                'default_enabled' => true
            ],
            'security_alerts' => [
                'label' => 'Security Alerts',
                'description' => 'Important security-related notifications',
                'channels' => ['email', 'sms', 'push'],
                'default_enabled' => true,
                'force_enabled' => true
            ],
            'system_updates' => [
                'label' => 'System Updates',
                'description' => 'System maintenance and update notifications',
                'channels' => ['email', 'in_app'],
                'default_enabled' => false
            ],
            'weekly_summary' => [
                'label' => 'Weekly Summary',
                'description' => 'Weekly financial summary reports',
                'channels' => ['email'],
                'default_enabled' => true
            ],
            'monthly_report' => [
                'label' => 'Monthly Report',
                'description' => 'Monthly financial reports',
                'channels' => ['email'],
                'default_enabled' => true
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission Configuration
    |--------------------------------------------------------------------------
    |
    | Define all permissions used in the application
    |
    */

    'permissions' => [
        // Dashboard permissions
        'dashboard.view' => 'View dashboard',
        'dashboard.export' => 'Export dashboard data',

        // Profile permissions
        'profile.view' => 'View profile',
        'profile.edit' => 'Edit profile',

        // Settings permissions
        'settings.view' => 'View settings',
        'settings.profile' => 'Manage profile settings',
        'settings.security' => 'Manage security settings',
        'settings.notifications' => 'Manage notification preferences',
        'settings.sessions' => 'View and manage sessions',
        'settings.tokens' => 'Manage integration tokens',

        // Users management permissions
        'users.view' => 'View users list',
        'users.create' => 'Create new users',
        'users.edit' => 'Edit user information',
        'users.delete' => 'Delete users',
        'users.manage_passwords' => 'Manage user passwords',

        // Setup permissions
        'setup.view' => 'View setup configuration',
        'setup.authentication' => 'Configure authentication settings',
        'setup.integrations' => 'Manage integration tokens',

        // Admin permissions
        'admin.users' => 'Manage users',
        'admin.system' => 'System administration',
        'admin.logs' => 'View system logs'
    ],

    /*
    |--------------------------------------------------------------------------
    | Role Configuration
    |--------------------------------------------------------------------------
    |
    | Define default roles and their permissions
    |
    */

    'roles' => [
        'user' => [
            'label' => 'User',
            'description' => 'Standard user with basic permissions',
            'permissions' => [
                'dashboard.view',
                'profile.view',
                'profile.edit',
                'settings.view',
                'settings.profile',
                'settings.security',
                'settings.notifications',
                'settings.sessions'
            ]
        ],
        'admin' => [
            'label' => 'Administrator',
            'description' => 'Full system access',
            'permissions' => '*' // All permissions
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Settings
    |--------------------------------------------------------------------------
    |
    | General application configuration
    |
    */

    'app' => [
        'name' => env('APP_NAME', 'Sharia Finance'),
        'logo_icon' => env('APP_LOGO_ICON', 'fas fa-mosque'),
        'logo_text' => env('APP_LOGO_TEXT', 'Sharia'),
        'version' => '1.0.0',
        'timezone' => env('APP_TIMEZONE', 'UTC'),
        'locale' => env('APP_LOCALE', 'en'),
        'currency' => env('APP_CURRENCY', 'USD'),
        'date_format' => env('APP_DATE_FORMAT', 'Y-m-d'),
        'time_format' => env('APP_TIME_FORMAT', 'H:i:s')
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific features
    |
    */

    'features' => [
        'two_factor_auth' => env('FEATURE_2FA', true),
        'api_tokens' => env('FEATURE_API_TOKENS', true),
        'export_data' => env('FEATURE_EXPORT', true),
        'sms_notifications' => env('FEATURE_SMS', false)
    ]
];
