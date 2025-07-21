<?php

namespace LaravelApp\Http\Controllers\Api;

use LaravelApp\Http\Controllers\Controller;
use LaravelApp\Models\ApplicationToken;
use LaravelApp\Models\User;
use LaravelApp\Services\ExternalTokenValidationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TokenController extends Controller
{
    private ExternalTokenValidationService $tokenValidationService;

    public function __construct(ExternalTokenValidationService $tokenValidationService)
    {
        $this->tokenValidationService = $tokenValidationService;
    }

    /**
     * Create integration token (admin only)
     */
    public function createIntegrationToken(Request $request)
    {
        // Check if user has permission to create tokens
        /** @var User|null $user */
        $user = Auth::user();
        if (!$user || !$user->hasPermission('tokens.create')) {
            abort(403, 'Unauthorized. You do not have permission to create tokens.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'scopes' => 'array',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $scopes = $request->input('scopes', ['integration:read', 'integration:write']);
        
        $tokenData = ApplicationToken::generateToken(
            name: $request->name,
            type: 'integration',
            scopes: $scopes,
            metadata: [
                'created_by' => Auth::id(),
                'created_via' => 'admin_panel'
            ]
        );

        if ($request->expires_at) {
            $tokenData['model']->update(['expires_at' => $request->expires_at]);
        }

        $responseData = [
            'success' => true,
            'data' => [
                'token' => $tokenData['token'],
                'name' => $tokenData['model']->name,
                'scopes' => $tokenData['model']->scopes,
                'expires_at' => $tokenData['model']->expires_at,
                'id' => $tokenData['model']->id,
            ],
            'message' => 'Integration token created successfully',
            'warning' => 'Store this token securely. It will not be shown again.'
        ];

        if ($request->expectsJson()) {
            return response()->json($responseData, 201);
        }

        // For web requests, redirect back with success message and token data in session
        return back()->with('success', 'Integration token created successfully')
                    ->with('new_token', $tokenData['token'])
                    ->with('token_name', $tokenData['model']->name);
    }

    /**
     * Exchange external token for application token
     */
    public function exchangeToken(Request $request): JsonResponse
    {
        $request->validate([
            'external_token' => 'required|string',
            'provider' => 'required|in:authentik,saml,oauth',
        ]);

        $result = $this->tokenValidationService->exchangeTokenForAppToken(
            $request->external_token,
            $request->provider
        );

        if (!$result) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid external token or provider validation failed'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $result['token'],
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $result['user']->id,
                    'name' => $result['user']->name,
                    'email' => $result['user']->email,
                    'auth_type' => $result['user']->auth_type,
                ],
                'expires_at' => $result['expires_at'],
            ],
            'message' => 'Token exchange successful'
        ]);
    }

    /**
     * Get current token info
     */
    public function getTokenInfo(Request $request): JsonResponse
    {
        $token = $request->attributes->get('application_token');
        
        if (!$token) {
            return response()->json(['error' => 'No token found'], 400);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $token->id,
                'name' => $token->name,
                'type' => $token->type,
                'scopes' => $token->scopes,
                'expires_at' => $token->expires_at,
                'last_used_at' => $token->last_used_at,
                'created_at' => $token->created_at,
                'user' => $token->user ? [
                    'id' => $token->user->id,
                    'name' => $token->user->name,
                    'email' => $token->user->email,
                ] : null,
            ]
        ]);
    }

    /**
     * List user's tokens (for authenticated users)
     */
    public function listTokens(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $tokens = ApplicationToken::where('user_id', $user->id)
            ->select('id', 'name', 'type', 'scopes', 'last_used_at', 'expires_at', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tokens
        ]);
    }

    /**
     * Revoke token
     */
    public function revokeToken(Request $request, int $tokenId): JsonResponse
    {
        $currentToken = $request->attributes->get('application_token');
        
        // Users can only revoke their own tokens, admins can revoke any token
        $query = ApplicationToken::where('id', $tokenId);
        
        if (!Gate::allows('viewApiDocs')) {
            // Non-admin users can only revoke their own tokens
            if ($currentToken->type === 'user' && $currentToken->user_id) {
                $query->where('user_id', $currentToken->user_id);
            } else {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $token = $query->first();
        
        if (!$token) {
            return response()->json(['error' => 'Token not found'], 404);
        }

        $token->revoke();

        return response()->json([
            'success' => true,
            'message' => 'Token revoked successfully'
        ]);
    }

    /**
     * List all tokens (admin only)
     */
    public function listAllTokens(Request $request): JsonResponse
    {
        Gate::authorize('viewApiDocs');

        $tokens = ApplicationToken::with('user:id,name,email')
            ->select('id', 'name', 'type', 'user_id', 'scopes', 'last_used_at', 'expires_at', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $tokens
        ]);
    }

    /**
     * Revoke token by admin (web interface)
     */
    public function revokeTokenByAdmin(Request $request, ApplicationToken $token)
    {
        Gate::authorize('viewApiDocs'); // Admin permission required

        $token->revoke();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Token revoked successfully'
            ]);
        }

        return back()->with('success', 'Token revoked successfully');
    }
}
