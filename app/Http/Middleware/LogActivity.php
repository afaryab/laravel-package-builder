<?php

namespace LaravelApp\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use LaravelApp\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip logging for certain routes/patterns
        if ($this->shouldSkipLogging($request)) {
            return $response;
        }

        try {
            $this->logActivity($request, $response);
        } catch (\Exception $e) {
            // Don't let logging errors break the application
            Log::error('Failed to log activity: ' . $e->getMessage());
        }

        return $response;
    }

    /**
     * Log the activity
     */
    protected function logActivity(Request $request, Response $response): void
    {
        $user = Auth::user();
        $isIntegrationRequest = $this->isIntegrationRequest($request);
        $integrationTokenId = $isIntegrationRequest ? $this->getIntegrationTokenId($request) : null;
        
        // Determine activity type
        $activityType = $isIntegrationRequest ? Activity::TYPE_INTEGRATION : 
                       ($user ? Activity::TYPE_USER : Activity::TYPE_SYSTEM);

        // Get description based on request
        $description = $this->getActivityDescription($request, $activityType, $response);
        
        // Get details
        $details = $this->getActivityDetails($request, $response, $activityType);

        // Create activity record
        try {
            Activity::create([
                'user_id' => $user ? $user->id : null,
                'type' => $activityType,
                'description' => $description,
                'details' => $details,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'request_method' => $request->method(),
                'request_url' => $request->fullUrl(),
                'request_data' => $this->sanitizeRequestData($request->all()),
                'response_status' => $response->getStatusCode(),
                'integration_token_id' => $integrationTokenId,
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
                'metadata' => [
                    'route_name' => $request->route() ? $request->route()->getName() : null,
                    'controller_action' => $this->getControllerAction($request),
                    'is_ajax' => $request->ajax(),
                    'accepts_json' => $request->acceptsJson(),
                    'content_type' => $request->header('Content-Type'),
                    'referrer' => $request->header('Referer'),
                ],
            ]);
        } catch (\Exception $e) {
            // Log the error but don't throw it to avoid breaking the application
            Log::error('Failed to log activity: ' . $e->getMessage());
        }
    }

    /**
     * Check if this is an integration/API request
     */
    protected function isIntegrationRequest(Request $request): bool
    {
        // Check for API routes
        if (str_starts_with($request->getPathInfo(), '/api/')) {
            return true;
        }

        // Check for Bearer token
        if ($request->bearerToken()) {
            return true;
        }

        // Check for API key in headers
        if ($request->header('X-API-Key') || $request->header('Authorization')) {
            return true;
        }

        // Check for JSON content type requests
        if ($request->expectsJson() && !$request->ajax()) {
            return true;
        }

        return false;
    }

    /**
     * Get integration token ID if available
     */
    protected function getIntegrationTokenId(Request $request): ?string
    {
        // Try to extract token from Bearer header
        $bearerToken = $request->bearerToken();
        if ($bearerToken) {
            return substr($bearerToken, 0, 10) . '***'; // Partial token for security
        }

        // Try to extract API key
        $apiKey = $request->header('X-API-Key');
        if ($apiKey) {
            return substr($apiKey, 0, 10) . '***';
        }

        return null;
    }

    /**
     * Get activity description based on request
     */
    protected function getActivityDescription(Request $request, string $type, Response $response): string
    {
        $method = $request->method();
        $path = $request->getPathInfo();
        $statusCode = $response->getStatusCode();

        // Handle authentication related requests
        if (str_contains($path, 'login')) {
            return $statusCode < 300 ? 'User Login' : 'Failed Login Attempt';
        }

        if (str_contains($path, 'logout')) {
            return 'User Logout';
        }

        if (str_contains($path, 'register')) {
            return $statusCode < 300 ? 'User Registration' : 'Failed Registration';
        }

        // Handle dashboard requests
        if (str_contains($path, 'dashboard')) {
            return $type === Activity::TYPE_INTEGRATION ? 'API Dashboard Access' : 'Dashboard Access';
        }

        // Handle API requests
        if ($type === Activity::TYPE_INTEGRATION) {
            return match($method) {
                'GET' => 'API Data Retrieval',
                'POST' => 'API Data Creation',
                'PUT', 'PATCH' => 'API Data Update',
                'DELETE' => 'API Data Deletion',
                default => 'API Request'
            };
        }

        // Handle regular user requests
        return match($method) {
            'GET' => 'Page View',
            'POST' => 'Data Submission',
            'PUT', 'PATCH' => 'Data Update',
            'DELETE' => 'Data Deletion',
            default => 'User Request'
        };
    }

    /**
     * Get detailed activity information
     */
    protected function getActivityDetails(Request $request, Response $response, string $type): string
    {
        $details = [];
        
        $details[] = ucfirst($request->method()) . ' request to ' . $request->getPathInfo();
        
        if ($type === Activity::TYPE_INTEGRATION) {
            $details[] = 'Integration access via API';
        }

        if ($response->getStatusCode() >= 400) {
            $details[] = 'Request failed with status ' . $response->getStatusCode();
        } else {
            $details[] = 'Request completed successfully';
        }

        if ($request->route()?->getName()) {
            $details[] = 'Route: ' . $request->route()->getName();
        }

        return implode('. ', $details);
    }

    /**
     * Sanitize request data for logging (remove sensitive information)
     */
    protected function sanitizeRequestData(array $data): array
    {
        $sensitiveFields = [
            'password',
            'password_confirmation',
            'token',
            'api_key',
            'secret',
            'private_key',
            'credit_card',
            'ssn',
            'social_security'
        ];

        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[REDACTED]';
            }
        }

        // Limit size of logged data
        $jsonString = json_encode($data);
        if (strlen($jsonString) > 5000) {
            return ['message' => 'Request data too large to log', 'size' => strlen($jsonString)];
        }

        return $data;
    }

    /**
     * Check if logging should be skipped for this request
     */
    protected function shouldSkipLogging(Request $request): bool
    {
        $skipPatterns = [
            '/_debugbar',
            '/telescope',
            '/favicon.ico',
            '/robots.txt',
            '/.well-known',
            '/health',
            '/ping'
        ];

        $path = $request->getPathInfo();

        foreach ($skipPatterns as $pattern) {
            if (str_contains($path, $pattern)) {
                return true;
            }
        }

        // Skip assets and static files
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf)$/i', $path)) {
            return true;
        }

        return false;
    }

    /**
     * Get controller action from request
     */
    protected function getControllerAction(Request $request): ?string
    {
        $route = $request->route();
        if (!$route) {
            return null;
        }

        $action = $route->getAction();
        
        // Check for controller action
        if (isset($action['controller'])) {
            return $action['controller'];
        }

        // Check for uses (callable)
        if (isset($action['uses'])) {
            $uses = $action['uses'];
            
            // If it's a string, return it
            if (is_string($uses)) {
                return $uses;
            }
            
            // If it's a closure or other callable, return a string representation
            if (is_callable($uses)) {
                return 'Closure';
            }
        }

        return 'Closure';
    }
}
