<?php

use Illuminate\Support\Facades\Route;
use LaravelApp\Http\Controllers\HomeController;
use LaravelApp\Http\Controllers\Auth\AuthController;
use LaravelApp\Http\Controllers\Auth\OAuthController;
use LaravelApp\Http\Controllers\Auth\SamlController;
use LaravelApp\Http\Controllers\DashboardController;
use LaravelApp\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Apply auth.type middleware to all routes
Route::group(['middleware' => 'auth.type'], function () {
    
    Route::get('/', [HomeController::class, 'index']);

    // Dashboard route (accessible when authenticated)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth.dynamic');
    
    // Dashboard API routes
    Route::get('/api/dashboard/income-chart', [DashboardController::class, 'getIncomeChartData'])->middleware('auth.dynamic');
    Route::get('/api/dashboard/transactions', [DashboardController::class, 'getRecentTransactions'])->middleware('auth.dynamic');
    Route::post('/api/dashboard/send-money', [DashboardController::class, 'sendMoney'])->middleware('auth.dynamic');

    $authType = env('AUTH', 'none');
    
    // Internal Laravel Auth routes (only for internal auth)
    if ($authType === 'internal') {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register'])->name('register');
    }
    
    // OAuth routes (only for OAuth auth)
    if ($authType === 'oauth') {
        Route::get('/oauth/redirect', [OAuthController::class, 'redirectToProvider'])->name('oauth.redirect');
        Route::get('/oauth/callback', [OAuthController::class, 'handleProviderCallback'])->name('oauth.callback');
        
        // Create a login route that redirects to OAuth for compatibility
        Route::get('/login', function () {
            return redirect()->route('oauth.redirect');
        })->name('login');
    }
    
    // SAML routes (only for SAML auth)
    if ($authType === 'saml') {
        Route::get('/saml/login', [SamlController::class, 'login'])->name('saml.login');
        Route::post('/saml/acs', [SamlController::class, 'acs'])->name('saml.acs');
        Route::get('/saml/metadata', [SamlController::class, 'metadata'])->name('saml.metadata');
        
        // Create a login route that redirects to SAML for compatibility  
        Route::get('/login', function () {
            return redirect()->route('saml.login');
        })->name('login');
    }
    
    // Common logout route (available for all auth types except 'none')
    if ($authType !== 'none') {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    }

    // Password reset routes (only for internal auth)
    if ($authType === 'internal') {
        Route::get('/forgot-password', [\LaravelApp\Http\Controllers\Auth\PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('/forgot-password', [\LaravelApp\Http\Controllers\Auth\PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('/reset-password/{token}', [\LaravelApp\Http\Controllers\Auth\PasswordResetController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password', [\LaravelApp\Http\Controllers\Auth\PasswordResetController::class, 'reset'])->name('password.update');
    }

    // Protected admin routes (available when authenticated)
    Route::group(['prefix' => 'admin', 'middleware' => 'auth.dynamic'], function () {
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
        
        Route::get('/users', function () {
            return view('admin.users');
        })->name('admin.users');
        
        Route::get('/tokens', function () {
            return view('admin.tokens');
        })->name('admin.tokens');
        
        // Token management routes (admin-only)
        Route::post('/tokens/integration', [\LaravelApp\Http\Controllers\Api\TokenController::class, 'createIntegrationToken'])->name('admin.tokens.create');
        Route::delete('/tokens/{token}', [\LaravelApp\Http\Controllers\Api\TokenController::class, 'revokeTokenByAdmin'])->name('admin.tokens.revoke');
        Route::get('/tokens/list', [\LaravelApp\Http\Controllers\Api\TokenController::class, 'listAllTokens'])->name('admin.tokens.list');
    });

    // Dashboard routes (available when authenticated)
    Route::middleware(['auth.dynamic'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        
        Route::prefix('profile')->name('profile.')->group(function () {
            // Profile routes
            Route::get('/', [\LaravelApp\Http\Controllers\ProfileController::class, 'show'])->name('index');
            Route::patch('/', [\LaravelApp\Http\Controllers\ProfileController::class, 'update'])->name('update');
            Route::patch('/password', [\LaravelApp\Http\Controllers\ProfileController::class, 'updatePassword'])->name('password');

            Route::get('/edit', function () {
                return view('profile.edit');
            })->name('edit');

            Route::get('/security', function () {
                return view('profile.edit');
            })->name('security');
            
            Route::get('/notifications', function () {
                return view('profile.edit');
            })->name('notifications');
            
            Route::get('/sessions', function () {
                return view('profile.edit');
            })->name('sessions');
            
        });



        Route::prefix('settings')->group(function () {
            
            Route::get('/', function () {
                return view('settings.index');
            })->name('settings.index');
            // Users management routes
            Route::resource('/users', \LaravelApp\Http\Controllers\UsersController::class);
            Route::patch('/users/{user}/password', [\LaravelApp\Http\Controllers\UsersController::class, 'updatePassword'])->name('users.password');
            Route::post('/users/{user}/reset-password', [\LaravelApp\Http\Controllers\UsersController::class, 'resetPassword'])->name('users.reset-password');

            Route::prefix('setup')->group(function () {
                // New setup routes
                Route::get('/', function () {
                    return view('settings.setup.index');
                })->name('setup');
                
                Route::get('/authentication', function () {
                    return view('settings.setup.authentication');
                })->name('authentication');
                Route::get('/tokens', function () {
                    return view('settings.setup.tokens');
                })->name('tokens');
            });
        });


        

        // Settings routes (legacy - redirect to new structure)
        
        
        // Dashboard API routes
        Route::get('/api/dashboard/income-chart', [DashboardController::class, 'getIncomeChartData']);
        Route::get('/api/dashboard/transactions', [DashboardController::class, 'getRecentTransactions']);
        Route::post('/api/dashboard/send-money', [DashboardController::class, 'sendMoney']);
        
        // Notification routes
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
            Route::get('/recent', [NotificationController::class, 'getRecent'])->name('recent');
            Route::patch('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
            Route::patch('/mark-multiple-as-read', [NotificationController::class, 'markMultipleAsRead'])->name('mark-multiple-as-read');
            Route::patch('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
            Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
            Route::delete('/bulk-delete', [NotificationController::class, 'destroyMultiple'])->name('destroy-multiple');
            Route::delete('/clear-read', [NotificationController::class, 'clearRead'])->name('clear-read');
            Route::delete('/clear-all', [NotificationController::class, 'clearAll'])->name('clear-all');
            
            // Test route for development
            if (app()->environment('local')) {
                Route::post('/test', [NotificationController::class, 'test'])->name('test');
            }
        });
    });

});
