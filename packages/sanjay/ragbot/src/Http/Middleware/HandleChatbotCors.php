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
        $origin = $request->header('Origin');
        $chatbot = app()->bound('ragbot.chatbot') ? app('ragbot.chatbot') : null;

        // 1. Origin Check (Security)
        if ($chatbot && $origin && ! empty($chatbot->allowed_origins)) {
            $allowed = false;
            foreach ($chatbot->allowed_origins as $pattern) {
                if ($pattern === '*' || $pattern === $origin) {
                    $allowed = true;
                    break;
                }
            }

            if (! $allowed) {
                return response()->json([
                    'error' => 'Origin not allowed: '.$origin,
                    'type' => 'UnauthorizedOriginException',
                ], 403);
            }
        }

        $response = $next($request);

        // 2. Add CORS Header to Response
        if ($origin) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        } else {
            $response->headers->set('Access-Control-Allow-Origin', '*');
        }

        return $response;
    }
}
