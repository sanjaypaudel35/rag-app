<?php

namespace Sanjay\Ragbot\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sanjay\Ragbot\Contracts\Repositories\ProjectRepositoryInterface;
use Sanjay\Ragbot\Models\RagbotUser;
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

        // Fallback for Livewire requests where the slug might not be in the route.
        if (! $slug) {
            // Try to get slug from Referer if this is a Livewire update
            if ($request->hasHeader('X-Livewire')) {
                $referer = $request->header('Referer');
                if ($referer && preg_match("/tenant\/([^\/]+)/", $referer, $matches)) {
                    $slug = $matches[1];
                }
            }
        }

        if (! $slug) {
            if (Auth::guard('ragbot')->check()) {
                /** @var RagbotUser $user */
                $user = Auth::guard('ragbot')->user();
                app()->instance('ragbot.project', $user->project);
            }

            return $next($request);
        }

        $project = $this->projectRepository->findBySlug($slug);

        if (! $project) {
            abort(404, 'Project not found for slug: '.$slug);
        }

        // Bind the project to the container for downstream use.
        app()->instance('ragbot.project', $project);

        return $next($request);
    }
}
