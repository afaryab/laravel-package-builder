<?php

namespace LaravelApp\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BasicAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = env('AUTH_USER', 'admin');
        $password = env('AUTH_PASSWORD', 'password');

        if ($request->getUser() !== $user || $request->getPassword() !== $password) {
            $headers = ['WWW-Authenticate' => 'Basic realm="Restricted Area"'];
            
            return new Response('Unauthorized', 401, $headers);
        }

        return $next($request);
    }
}
