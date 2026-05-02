<?php

namespace Sanjay\Ragbot\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to redirect to the login page if the user is not authenticated for Ragbot.
 */
class RedirectIfNotRagbotAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('ragbot')->check()) {
            $projectSlug = $request->route('project_slug');

            return redirect()->route('ragbot.login', ['project_slug' => $projectSlug]);
        }

        return $next($request);
    }
}
