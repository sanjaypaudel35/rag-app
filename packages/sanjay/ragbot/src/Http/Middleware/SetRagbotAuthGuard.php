<?php

namespace Sanjay\Ragbot\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to set the Fortify guard to 'ragbot'.
 */
class SetRagbotAuthGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        config(['fortify.guard' => 'ragbot']);
        config(['auth.defaults.guard' => 'ragbot']);

        if (app()->bound('ragbot.project')) {
            config(['fortify.home' => route('ragbot.dashboard', ['project_slug' => app('ragbot.project')->slug, 'absolute' => false])]);
        }

        return $next($request);
    }
}
