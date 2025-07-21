<?php

namespace LaravelApp\Services;

use LaravelApp\Models\User;
use LaravelApp\Models\ApplicationToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalTokenValidationService
{
    /**
     * Validate token from Authentik
     */
    public function validateAuthentikToken(string $token): ?array
    {
        try {
            $response = Http::withToken($token)
                ->get(config('services.authentik.base_url') . '/api/v3/core/user_sessions/');

            if ($response->successful()) {
                $userData = $response->json();
                return [
                    'valid' => true,
                    'user_data' => $userData,
                    'provider' => 'authentik'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Authentik token validation failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Validate SAML token/assertion
     */
    public function validateSamlToken(string $token): ?array
    {
        // This would integrate with your SAML provider
        // Implementation depends on your SAML setup
        
        try {
            // Example: Validate SAML assertion
            // This is a placeholder - implement based on your SAML provider
            
            return [
                'valid' => true,
                'user_data' => [
                    'id' => 'saml_user_id',
                    'email' => 'user@example.com',
                    'name' => 'SAML User'
                ],
                'provider' => 'saml'
            ];
        } catch (\Exception $e) {
            Log::error('SAML token validation failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Validate OAuth token
     */
    public function validateOAuthToken(string $token): ?array
    {
        try {
            // Validate with OAuth provider (e.g., Google)
            $response = Http::withToken($token)
                ->get('https://www.googleapis.com/oauth2/v2/userinfo');

            if ($response->successful()) {
                $userData = $response->json();
                return [
                    'valid' => true,
                    'user_data' => $userData,
                    'provider' => 'oauth'
                ];
            }
        } catch (\Exception $e) {
            Log::error('OAuth token validation failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Exchange external token for application token
     */
    public function exchangeTokenForAppToken(string $externalToken, string $provider): ?array
    {
        $validationResult = match($provider) {
            'authentik' => $this->validateAuthentikToken($externalToken),
            'saml' => $this->validateSamlToken($externalToken),
            'oauth' => $this->validateOAuthToken($externalToken),
            default => null
        };

        if (!$validationResult || !$validationResult['valid']) {
            return null;
        }

        $userData = $validationResult['user_data'];
        $externalUserId = $userData['id'] ?? $userData['sub'] ?? $userData['email'];

        // Find or create user
        $user = $this->findOrCreateUser($userData, $provider, $externalUserId);

        // Check if user already has a valid token
        $existingToken = ApplicationToken::where('user_id', $user->id)
            ->where('type', 'user')
            ->where('external_provider', $provider)
            ->where(function($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if ($existingToken) {
            return [
                'token' => 'app_' . substr($existingToken->token, 0, 60), // This won't work for hashed tokens
                'user' => $user,
                'expires_at' => $existingToken->expires_at
            ];
        }

        // Create new application token
        $tokenData = ApplicationToken::generateToken(
            name: "User token for {$user->email}",
            type: 'user',
            userId: $user->id,
            scopes: ['user:read', 'user:profile'],
            metadata: [
                'external_provider' => $provider,
                'external_user_id' => $externalUserId,
                'created_via' => 'token_exchange'
            ]
        );

        // Update the token record with external info
        $tokenData['model']->update([
            'external_user_id' => $externalUserId,
            'external_provider' => $provider,
        ]);

        return [
            'token' => $tokenData['token'],
            'user' => $user,
            'expires_at' => null // No expiration by default
        ];
    }

    /**
     * Find or create user from external provider data
     */
    private function findOrCreateUser(array $userData, string $provider, string $externalUserId): User
    {
        // First try to find by external ID
        $user = User::where('external_id', $externalUserId)
            ->where('auth_type', $provider)
            ->first();

        if ($user) {
            return $user;
        }

        // Try to find by email
        $email = $userData['email'] ?? null;
        if ($email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                // Update with external info
                $user->update([
                    'external_id' => $externalUserId,
                    'auth_type' => $provider,
                ]);
                return $user;
            }
        }

        // Create new user
        return User::create([
            'name' => $userData['name'] ?? $userData['given_name'] ?? $email ?? 'External User',
            'email' => $email ?? $externalUserId . '@' . $provider . '.local',
            'external_id' => $externalUserId,
            'auth_type' => $provider,
            'password' => null, // External users don't have passwords
        ]);
    }
}
