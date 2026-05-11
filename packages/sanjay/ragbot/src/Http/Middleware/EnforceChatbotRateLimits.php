<?php

namespace Sanjay\Ragbot\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to enforce dynamic rate limits for Chatbots and Sessions.
 */
class EnforceChatbotRateLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $chatbot = app()->bound('ragbot.chatbot') ? app('ragbot.chatbot') : null;

        if (! $chatbot) {
            return $next($request);
        }

        $sessionId = $request->input('session_id') ?? $request->ip(); // Fallback to IP if no session ID provided (though ChatController handles it, middleware runs earlier)

        // 1. Enforce Global Chatbot Rate Limit
        $globalLimit = $chatbot->rate_limit_per_minute ?? 60;
        $globalKey = 'chatbot_global_'.$chatbot->id;

        if (RateLimiter::tooManyAttempts($globalKey, $globalLimit)) {
            return response()->json([
                'error' => 'Too many requests for this chatbot. Please try again later.',
                'type' => 'ChatbotRateLimitException',
            ], 429);
        }

        // 2. Enforce Per-Session Rate Limit
        $sessionLimit = $chatbot->session_rate_limit_per_minute ?? 10;
        $sessionKey = 'chatbot_session_'.$chatbot->id.'_'.$sessionId;

        if (RateLimiter::tooManyAttempts($sessionKey, $sessionLimit)) {
            return response()->json([
                'error' => 'Too many requests for this session. Please try again later.',
                'type' => 'SessionRateLimitException',
            ], 429);
        }

        // Increment attempts
        RateLimiter::hit($globalKey, 60); // 1 minute decay
        RateLimiter::hit($sessionKey, 60); // 1 minute decay

        return $next($request);
    }
}
