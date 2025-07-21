<?php

namespace LaravelApp\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'group',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the roles that have this permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    /**
     * Get permissions grouped by category
     */
    public static function getGrouped(): array
    {
        return self::where('is_active', true)
            ->orderBy('group')
            ->orderBy('display_name')
            ->get()
            ->groupBy('group')
            ->toArray();
    }

    /**
     * Create permission if it doesn't exist
     */
    public static function findOrCreate(string $name, string $displayName, string $description = '', string $group = 'general'): self
    {
        return self::firstOrCreate(
            ['name' => $name],
            [
                'display_name' => $displayName,
                'description' => $description,
                'group' => $group,
                'is_active' => true,
            ]
        );
    }
}
