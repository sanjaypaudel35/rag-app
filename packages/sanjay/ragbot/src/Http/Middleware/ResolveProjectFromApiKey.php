<?php

namespace Sanjay\Ragbot\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Sanjay\Ragbot\Contracts\Repositories\ChatbotRepositoryInterface;
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
    public function __construct(
        protected ProjectRepositoryInterface $projectRepository,
        protected ChatbotRepositoryInterface $chatbotRepository
    ) {}

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

        // 1. Try resolving as a Chatbot Key first
        if (str_starts_with($apiKey, 'rb_c_')) {
            $chatbot = $this->chatbotRepository->findByApiKey($apiKey);

            if ($chatbot) {
                app()->instance('ragbot.chatbot', $chatbot);
                app()->instance('ragbot.project', $chatbot->project);

                return $next($request);
            }
        }

        // 2. Fallback to Project Master Key
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
