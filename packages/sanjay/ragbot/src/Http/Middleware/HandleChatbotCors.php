<?php

namespace Sanjay\Ragbot\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to handle CORS origin validation for Chatbots.
 */
class HandleChatbotCors
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Resolve Identity
        $chatbot = app()->bound('ragbot.chatbot') ? app('ragbot.chatbot') : null;
        $project = app()->bound('ragbot.project') ? app('ragbot.project') : null;

        // 2. Extract Origin/Referer
        $origin = $request->header('Origin');
        $referer = $request->header('Referer');
        
        // If no Origin (e.g. non-browser), use Referer domain as fallback for validation
        $effectiveOrigin = $origin;
        if (! $effectiveOrigin && $referer) {
            $urlParts = parse_url($referer);
            $effectiveOrigin = ($urlParts['scheme'] ?? 'https') . '://' . ($urlParts['host'] ?? '');
            if (isset($urlParts['port'])) {
                $effectiveOrigin .= ':' . $urlParts['port'];
            }
        }

        // 3. Strict Origin Validation (Software Industry Standard: Domain Locking)
        $allowedOrigins = [];
        if ($chatbot && ! empty($chatbot->allowed_origins)) {
            $allowedOrigins = $chatbot->allowed_origins;
        }

        if (! empty($allowedOrigins)) {
            $allowed = false;
            foreach ($allowedOrigins as $pattern) {
                if ($pattern === '*') {
                    $allowed = true;
                    break;
                }
                
                // Exact match or wildcard subdomain match (standard security practice)
                if ($effectiveOrigin === $pattern || (str_starts_with($pattern, '*.') && str_ends_with($effectiveOrigin, substr($pattern, 1)))) {
                    $allowed = true;
                    break;
                }
            }

            if (! $allowed) {
                return response()->json([
                    'error' => 'Unauthorized Origin: The API key used is restricted to specific domains.',
                    'type' => 'UnauthorizedOriginException',
                    'hint' => 'Add ' . ($effectiveOrigin ?: 'your domain') . ' to the allowed_origins in your chatbot settings.',
                ], 403);
            }
        }

        // 4. Handle Preflight (OPTIONS)
        if ($request->isMethod('OPTIONS')) {
            $response = response('', 204);
        } else {
            $response = $next($request);
        }

        // 5. Add Security Headers
        if ($origin) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Headers', 'X-Api-Key, Content-Type, Accept, Authorization');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        } elseif (! empty($allowedOrigins) && ! in_array('*', $allowedOrigins)) {
            // For non-browser clients, still indicate the restriction
            $response->headers->set('X-Allowed-Origins', implode(', ', $allowedOrigins));
        }

        return $response;
    }
}
