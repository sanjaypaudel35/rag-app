<?php

use Illuminate\Support\Facades\Route;
use Sanjay\Ragbot\Http\Controllers\Api\ChatController;

/**
 * API routes for sanjay/ragbot.
 * All routes are prefixed by config("ragbot.prefix") . "/api" in the service provider.
 */
Route::prefix('v1')->middleware(['ragbot.auth', 'ragbot.cors', 'ragbot.ratelimit'])->group(function () {
    /**
     * Test route to verify API key resolution and container binding.
     */
    Route::get('/ping', function () {
        $project = app('ragbot.project');

        return response()->json([
            'status' => 'ok',
            'project' => $project->name,
        ]);
    });

    /**
     * Chat API endpoint.
     */
    Route::post('/chat', ChatController::class)->name('chat');
});
