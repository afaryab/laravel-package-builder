<?php

namespace LaravelApp\Http\Controllers\Auth;

use LaravelApp\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use LaravelApp\Models\User;
use LaravelApp\Services\ExternalTokenValidationService;

class OAuthController extends Controller
{
    protected $tokenService;
    
    public function __construct(ExternalTokenValidationService $tokenService)
    {
        $this->tokenService = $tokenService;
    }
    
    public function redirectToProvider()
    {
        if (env('AUTH') !== 'oauth') {
            abort(404);
        }
        
        $params = [
            'client_id' => env('AUTHENTIK_CLIENT_ID'),
            'redirect_uri' => env('OAUTH_REDIRECT_URI'),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => csrf_token()
        ];
        
        $authorizeUrl = env('AUTHENTIK_BASE_URL') . env('AUTHENTIK_AUTHORIZE_ENDPOINT', '/application/o/authorize/');
        
        return redirect($authorizeUrl . '?' . http_build_query($params));
    }

    public function handleProviderCallback(Request $request)
    {
        if (env('AUTH') !== 'oauth') {
            abort(404);
        }
        
        $code = $request->get('code');
        $state = $request->get('state');
        
        // Verify CSRF state
        if ($state !== csrf_token()) {
            return redirect('/')->withErrors(['error' => 'Invalid state parameter']);
        }
        
        if (!$code) {
            return redirect('/')->withErrors(['error' => 'No authorization code received']);
        }
        
        try {
            // Exchange code for access token
            $tokenResponse = Http::asForm()->post(env('AUTHENTIK_INTERNAL_URL', env('AUTHENTIK_BASE_URL')) . env('AUTHENTIK_TOKEN_ENDPOINT'), [
                'grant_type' => 'authorization_code',
                'client_id' => env('AUTHENTIK_CLIENT_ID'),
                'client_secret' => env('AUTHENTIK_CLIENT_SECRET'),
                'redirect_uri' => env('OAUTH_REDIRECT_URI'),
                'code' => $code
            ]);
            
            if (!$tokenResponse->successful()) {
                return redirect('/')->withErrors(['error' => 'Failed to obtain access token']);
            }
            
            $tokenData = $tokenResponse->json();
            $accessToken = $tokenData['access_token'];
            
            // Get user info from Authentik
            $userResponse = Http::withToken($accessToken)
                ->get(env('AUTHENTIK_INTERNAL_URL', env('AUTHENTIK_BASE_URL')) . env('AUTHENTIK_USERINFO_ENDPOINT'));
            
            if (!$userResponse->successful()) {
                return redirect('/')->withErrors(['error' => 'Failed to get user information']);
            }
            
            $authentikUser = $userResponse->json();
            
            // Find or create user
            $user = User::firstOrCreate(
                ['email' => $authentikUser['email']],
                [
                    'name' => $authentikUser['name'] ?? $authentikUser['preferred_username'],
                    'external_id' => $authentikUser['sub'],
                    'email_verified_at' => now(),
                ]
            );
            
            // Update external_id if not set
            if (!$user->external_id) {
                $user->update(['external_id' => $authentikUser['sub']]);
            }
            
            // Log the user in
            Auth::login($user);
            
            return redirect('/')->with('success', 'Successfully logged in via OAuth');
            
        } catch (\Exception $e) {
            Log::error('OAuth callback error: ' . $e->getMessage());
            return redirect('/')->withErrors(['error' => 'Authentication failed: ' . $e->getMessage()]);
        }
    }
}
