<?php

namespace LaravelApp\Http\Controllers;

use LaravelApp\Models\ApplicationToken;
use LaravelApp\Models\User;
use LaravelApp\Services\ExternalTokenValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{
    /**
     * Display a listing of the user's tokens.
     */
    public function index()
    {
        $user = Auth::user();
        $tokens = ApplicationToken::where('user_id', $user->id)->get();
        
        return response()->json([
            'tokens' => $tokens->map(function ($token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'scopes' => $token->scopes,
                    'last_used_at' => $token->last_used_at,
                    'expires_at' => $token->expires_at,
                    'created_at' => $token->created_at,
                ];
            })
        ]);
    }

    /**
     * Store a newly created token in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'scopes' => 'required|array',
            'scopes.*' => 'string|in:users:read,users:write,tokens:read,tokens:write,admin:read,admin:write',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $user = Auth::user();
        
        $tokenData = ApplicationToken::generateToken(
            $request->name,
            'integration',
            $user->id,
            $request->scopes,
            []
        );

        // Update expires_at if provided
        if ($request->expires_at) {
            $tokenData['model']->update(['expires_at' => $request->expires_at]);
        }

        return response()->json([
            'token' => [
                'id' => $tokenData['model']->id,
                'name' => $tokenData['model']->name,
                'scopes' => $tokenData['model']->scopes,
                'expires_at' => $tokenData['model']->expires_at,
                'created_at' => $tokenData['model']->created_at,
            ],
            'access_token' => $tokenData['token'], // Only shown once
        ], 201);
    }

    /**
     * Remove the specified token from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $token = ApplicationToken::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();
        
        $token->delete();
        
        return response()->json(['message' => 'Token deleted successfully']);
    }

    /**
     * Exchange external token for application token.
     */
    public function exchangeToken(Request $request)
    {
        $request->validate([
            'external_token' => 'required|string',
            'provider' => 'required|string|in:authentik,saml,oauth',
            'scopes' => 'array',
            'scopes.*' => 'string|in:users:read,users:write,tokens:read,tokens:write',
        ]);

        try {
            // Validate the external token and get user info
            $externalTokenService = new ExternalTokenValidationService();
            
            // Exchange for application token
            $appTokenData = $externalTokenService->exchangeTokenForAppToken(
                $request->external_token,
                $request->provider,
                $request->scopes ?? ['users:read']
            );

            if (!$appTokenData) {
                return response()->json([
                    'error' => 'invalid_token',
                    'error_description' => 'The provided external token is invalid or expired'
                ], 401);
            }

            return response()->json([
                'access_token' => $appTokenData['token'],
                'token_type' => 'Bearer',
                'expires_in' => 3600, // 1 hour
                'user' => $appTokenData['user'],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'invalid_token',
                'error_description' => 'The provided external token is invalid or expired'
            ], 401);
        }
    }
}
