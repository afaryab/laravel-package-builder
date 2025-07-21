<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use LaravelApp\Http\Controllers\Api\ApiController;
use LaravelApp\Http\Controllers\Api\TokenController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public API endpoints (no authentication required)
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
});

Route::get('/info', function () {
    return response()->json([
        'app_name' => config('app.name'),
        'environment' => app()->environment(),
        'auth_type' => env('AUTH', 'none'),
        'version' => '1.0.0'
    ]);
});

Route::get('/stats', [ApiController::class, 'getStats']);

// Token management endpoints (only available when AUTH is not 'none')
if (env('AUTH', 'none') !== 'none') {
    Route::post('/auth/exchange-token', [TokenController::class, 'exchangeToken']);
}

// Session-based authentication endpoints (for web users)
// Only available when AUTH is not 'none'
if (env('AUTH', 'none') !== 'none') {
    Route::middleware(['web', 'auth.type'])->group(function () {
        
        // These will use your existing web authentication
        Route::get('/user', function (Request $request) {
            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            return response()->json(['user' => Auth::user()]);
        });
        
        // Admin endpoints for token management - REMOVED FOR SECURITY
        // Integration tokens should only be created through admin web interface
        Route::get('/tokens/all', [TokenController::class, 'listAllTokens']);
    
        Route::get('/docs', function () {
            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            
            Gate::authorize('viewApiDocs');
            
            $authType = env('AUTH', 'none');
            $authMethods = [];
            $webEndpoints = [];
            
            // Configure authentication methods based on AUTH type
            switch ($authType) {
                case 'internal':
                    $authMethods = [
                        'session_based' => [
                            'type' => 'Session-based (Internal Laravel Auth)',
                            'login' => 'POST /login',
                            'register' => 'POST /register (when user provisioning enabled)',
                            'logout' => 'POST /logout'
                        ],
                        'token_based' => [
                            'type' => 'Bearer Token',
                            'header' => 'Authorization: Bearer {token}',
                            'note' => 'Integration tokens can only be created via admin web interface',
                            'exchange_external_token' => 'POST /api/auth/exchange-token'
                        ]
                    ];
                    $webEndpoints = [
                        'GET /' => 'Home page with login form',
                        'POST /login' => 'Internal authentication',
                        'POST /register' => 'User registration (if enabled)',
                        'POST /logout' => 'Logout'
                    ];
                    break;
                    
                case 'oauth':
                    $authMethods = [
                        'oauth_based' => [
                            'type' => 'OAuth 2.0 (via Authentik)',
                            'login' => 'GET /oauth/redirect',
                            'callback' => 'GET /oauth/callback',
                            'provider' => 'Authentik OpenID Connect'
                        ],
                        'token_based' => [
                            'type' => 'Bearer Token',
                            'header' => 'Authorization: Bearer {token}',
                            'note' => 'Integration tokens can only be created via admin web interface',
                            'exchange_external_token' => 'POST /api/auth/exchange-token'
                        ]
                    ];
                    $webEndpoints = [
                        'GET /' => 'Home page with OAuth login',
                        'GET /oauth/redirect' => 'Redirect to OAuth provider',
                        'GET /oauth/callback' => 'OAuth callback handler',
                        'POST /logout' => 'Logout'
                    ];
                    break;
                    
                case 'saml':
                    $authMethods = [
                        'saml_based' => [
                            'type' => 'SAML 2.0 SSO',
                            'login' => 'GET /saml/login',
                            'acs' => 'POST /saml/acs (Assertion Consumer Service)',
                            'metadata' => 'Available via SAML metadata endpoint'
                        ],
                        'token_based' => [
                            'type' => 'Bearer Token',
                            'header' => 'Authorization: Bearer {token}',
                            'note' => 'Integration tokens can only be created via admin web interface',
                            'exchange_external_token' => 'POST /api/auth/exchange-token'
                        ]
                    ];
                    $webEndpoints = [
                        'GET /' => 'Home page with SAML SSO',
                        'GET /saml/login' => 'Initiate SAML SSO',
                        'POST /saml/acs' => 'SAML assertion consumer',
                        'POST /logout' => 'Logout'
                    ];
                    break;
                    
                default:
                    $authMethods = [
                        'session_based' => [
                            'type' => 'Session-based (uses web login)',
                            'note' => 'Must be logged in through web interface'
                        ],
                        'token_based' => [
                            'type' => 'Bearer Token',
                            'header' => 'Authorization: Bearer {token}',
                            'note' => 'Integration tokens can only be created via admin web interface',
                            'exchange_external_token' => 'POST /api/auth/exchange-token'
                        ]
                    ];
                    $webEndpoints = [
                        'GET /' => 'Home page',
                        'POST /logout' => 'Logout'
                    ];
            }
            
            return response()->json([
                'documentation' => 'Laravel Package Builder API',
                'version' => '2.0.0',
                'base_url' => url('/api'),
                'auth_type' => $authType,
                'authentication' => $authMethods,
                'endpoints' => [
                    'public' => [
                        'GET /api/health' => 'Health check endpoint',
                        'GET /api/info' => 'Application information',
                        'GET /api/stats' => 'Application statistics',
                    ],
                    'web' => $webEndpoints,
                    'authentication' => [
                        'POST /api/auth/exchange-token' => 'Exchange external token for app token',
                    ],
                    'session_authenticated' => [
                        'GET /api/user' => 'Get authenticated user information',
                        'GET /api/tokens/all' => 'List all tokens (admin only)',
                        'GET /api/docs' => 'This documentation endpoint',
                    ],
                    'token_authenticated' => [
                        'GET /api/v1/user' => 'Get current user info',
                        'GET /api/v1/users' => 'Get all users (requires users:read scope)',
                        'GET /api/v1/tokens' => 'List user tokens',
                        'GET /api/v1/tokens/info' => 'Get current token info',
                        'DELETE /api/v1/tokens/{id}' => 'Revoke token',
                    ]
                ],
                'token_scopes' => [
                    'integration:read' => 'Read access for integrations',
                    'integration:write' => 'Write access for integrations',
                    'user:read' => 'Read user information',
                    'user:profile' => 'Access user profile',
                    'users:read' => 'Read all users (admin)',
                    '*' => 'Full access'
                ]
            ]);
        });
    });
}

// Token-based authentication endpoints (v1 API)
// Only available when AUTH is not 'none'
if (env('AUTH', 'none') !== 'none') {
    Route::prefix('v1')->middleware(['token.auth'])->group(function () {
        
        // User endpoints
        Route::get('/user', [ApiController::class, 'getAuthenticatedUser']);
        Route::get('/tokens', [TokenController::class, 'listTokens']);
        Route::get('/tokens/info', [TokenController::class, 'getTokenInfo']);
        Route::delete('/tokens/{tokenId}', [TokenController::class, 'revokeToken']);
        
        // Protected endpoints with scope requirements
        Route::middleware(['token.auth:users:read'])->group(function () {
            Route::get('/users', [ApiController::class, 'getUsers']);
        });
        
    });
}
