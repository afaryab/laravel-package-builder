<?php

namespace LaravelApp\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ApplicationToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'token',
        'type',
        'user_id',
        'external_user_id',
        'external_provider',
        'scopes',
        'metadata',
        'last_used_at',
        'expires_at',
    ];

    protected $hidden = [
        'token',
    ];

    protected $casts = [
        'scopes' => 'array',
        'metadata' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Generate a new application token
     */
    public static function generateToken(string $name, string $type = 'integration', ?int $userId = null, array $scopes = [], array $metadata = []): array
    {
        $plainTextToken = 'app_' . Str::random(60);
        $hashedToken = Hash::make($plainTextToken);

        $token = static::create([
            'name' => $name,
            'token' => $hashedToken,
            'type' => $type,
            'user_id' => $userId,
            'scopes' => $scopes,
            'metadata' => $metadata,
        ]);

        return [
            'token' => $plainTextToken,
            'model' => $token,
        ];
    }

    /**
     * Validate and find token
     */
    public static function findToken(string $token): ?ApplicationToken
    {
        $tokens = static::where('type', 'integration')
            ->orWhere('type', 'user')
            ->get();

        foreach ($tokens as $tokenModel) {
            if (Hash::check($token, $tokenModel->token)) {
                // Check if token is expired
                if ($tokenModel->expires_at && $tokenModel->expires_at->isPast()) {
                    return null;
                }

                // Update last used
                $tokenModel->update(['last_used_at' => now()]);
                
                return $tokenModel;
            }
        }

        return null;
    }

    /**
     * Check if token has scope
     */
    public function hasScope(string $scope): bool
    {
        if (empty($this->scopes)) {
            return false;
        }

        return in_array($scope, $this->scopes) || in_array('*', $this->scopes);
    }

    /**
     * Check if token is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get the user that owns the token
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Revoke token
     */
    public function revoke(): bool
    {
        return $this->delete();
    }
}
