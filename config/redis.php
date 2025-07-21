<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Redis Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the Redis connections below you wish
    | to use as your default connection for all Redis work. Of course
    | you may use many connections at once using the Redis library.
    |
    */

    'default' => env('REDIS_CLIENT', 'phpredis'),

    /*
    |--------------------------------------------------------------------------
    | Redis Clusters
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the Redis clusters used by your application.
    | Clusters are useful when managing a sharded cache across multiple Redis
    | nodes. Here you may define which nodes exist in your clusters.
    |
    */

    'clusters' => [

        'default' => [
            [
                'host' => env('REDIS_HOST', '127.0.0.1'),
                'password' => env('REDIS_PASSWORD'),
                'port' => env('REDIS_PORT', 6379),
                'database' => env('REDIS_DB', 0),
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the Redis connections used by your application.
    | Configuration for each connection is available and may be overridden
    | based on this application's needs. This is a great default setup.
    |
    */

    'connections' => [

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DB', 0),
        ],

        'cache' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_CACHE_DB', 1),
        ],

        'session' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_SESSION_DB', 2),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Sentinel
    |--------------------------------------------------------------------------
    |
    | Here you may configure Redis Sentinel which provides high availability
    | for Redis. Sentinel can automatically failover when a primary Redis
    | server is not working as expected. See the Laravel documentation.
    |
    */

    'sentinel' => [
        'masters' => [
            'default' => [
                'host' => env('REDIS_SENTINEL_HOST', 'localhost'),
                'port' => env('REDIS_SENTINEL_PORT', 26379),
            ],
        ],
    ],

];
