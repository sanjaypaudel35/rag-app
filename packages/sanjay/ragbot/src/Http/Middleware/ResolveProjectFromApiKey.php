<?php

namespace Sanjay\Ragbot\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Sanjay\Ragbot\Contracts\Repositories\ProjectRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to resolve a Project from the X-Api-Key header.
 */
class ResolveProjectFromApiKey
{
    /**
     * Create a new middleware instance.
     */
    public function __construct(protected ProjectRepositoryInterface $projectRepository) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-Api-Key');

        if (! $apiKey) {
            return response()->json([
                'error' => 'Invalid or missing API key',
            ], 401);
        }

        $project = $this->projectRepository->findByApiKey($apiKey);

        if (! $project) {
            return response()->json([
                'error' => 'Invalid or missing API key',
            ], 401);
        }

        // Bind the project to the container for downstream use.
        app()->instance('ragbot.project', $project);

        return $next($request);
    }
}
