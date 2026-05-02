<?php

namespace Sanjay\Ragbot\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Sanjay\Ragbot\Contracts\Repositories\ProjectRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to resolve a Project from the project_slug route parameter.
 */
class ResolveProjectFromSlug
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
        $slug = $request->route('project_slug');

        if (! $slug) {
            return $next($request);
        }

        $project = $this->projectRepository->all()->where('slug', $slug)->first();

        if (! $project) {
            abort(404, 'Project not found for slug: '.$slug);
        }

        // Bind the project to the container for downstream use.
        app()->instance('ragbot.project', $project);

        return $next($request);
    }
}
