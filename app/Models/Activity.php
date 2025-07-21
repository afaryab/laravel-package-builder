<?php

namespace LaravelApp\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'description',
        'details',
        'ip_address',
        'user_agent',
        'request_method',
        'request_url',
        'request_data',
        'response_status',
        'integration_token_id',
        'session_id',
        'metadata'
    ];

    protected $casts = [
        'request_data' => 'json',
        'metadata' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Activity types
    const TYPE_USER = 'user';
    const TYPE_INTEGRATION = 'integration';
    const TYPE_SYSTEM = 'system';

    /**
     * Get the user that performed this activity
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for user activities
     */
    public function scopeUserActivities($query)
    {
        return $query->where('type', self::TYPE_USER);
    }

    /**
     * Scope for integration activities
     */
    public function scopeIntegrationActivities($query)
    {
        return $query->where('type', self::TYPE_INTEGRATION);
    }

    /**
     * Scope for system activities
     */
    public function scopeSystemActivities($query)
    {
        return $query->where('type', self::TYPE_SYSTEM);
    }

    /**
     * Get formatted activity data for display
     */
    public function getFormattedDataAttribute()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'description' => $this->description,
            'details' => $this->details,
            'user' => $this->user ? [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($this->user->name) . '&background=667eea&color=fff'
            ] : [
                'name' => 'System',
                'email' => 'system@' . config('app.name', 'app') . '.com',
                'avatar' => 'https://ui-avatars.com/api/?name=System&background=6c757d&color=fff'
            ],
            'time' => $this->created_at->diffForHumans(),
            'date' => $this->created_at->format('d M Y'),
            'status' => $this->response_status >= 200 && $this->response_status < 300 ? 'Success' : 
                       ($this->response_status >= 400 ? 'Failed' : 'Info'),
            'request_method' => $this->request_method,
            'request_url' => $this->request_url,
            'ip_address' => $this->ip_address
        ];
    }
}
