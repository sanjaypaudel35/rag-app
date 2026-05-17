<?php

namespace Sanjay\Ragbot\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Sanjay\Ragbot\Contracts\Repositories\ChatbotRepositoryInterface;
use Sanjay\Ragbot\Contracts\Repositories\ProjectRepositoryInterface;
use Sanjay\Ragbot\Services\Tenant\WidgetService;

/**
 * Controller to serve the embeddable chat widget script.
 */
class WidgetController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ProjectRepositoryInterface $projectRepository,
        protected ChatbotRepositoryInterface $chatbotRepository,
        protected WidgetService $widgetService
    ) {}

    /**
     * Serve the widget JavaScript script.
     */
    public function serve(Request $request): Response
    {
        $apiKey = $request->query('api_key');

        if (! $apiKey) {
            return response('/* Missing API key. */', 404)
                ->header('Content-Type', 'application/javascript');
        }

        $project = $this->projectRepository->findByApiKey($apiKey);

        if (! $project) {
            $chatbot = $this->chatbotRepository->findByApiKey($apiKey);
            if ($chatbot) {
                $project = $chatbot->project;
            }
        }

        if (! $project) {
            return response('/* Invalid API key. */', 404)
                ->header('Content-Type', 'application/javascript');
        }

        $script = $this->widgetService->getScript($project, url('/'));

        return response($script)
            ->header('Content-Type', 'application/javascript');
    }
}
