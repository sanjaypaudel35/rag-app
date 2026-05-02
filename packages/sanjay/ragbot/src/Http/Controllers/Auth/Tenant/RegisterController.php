<?php

namespace Sanjay\Ragbot\Http\Controllers\Auth\Tenant;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Fortify\Contracts\RegisterResponse;
use Sanjay\Ragbot\Services\Auth\Tenant\RegisterService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for handling tenant-specific user registration.
 */
class RegisterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  RegisterService  $service Resolved as Tenant\RegisterService via DI.
     * @param  RegisterResponse  $response Contextual binding resolves this to Sanjay\Ragbot\Actions\Fortify\RegisterResponse.
     * @param  StatefulGuard  $guard Contextual binding resolves this to the 'ragbot' guard.
     */
    public function __construct(
        protected RegisterService $service,
        protected RegisterResponse $response,
        protected StatefulGuard $guard
    ) {}

    /**
     * Show the registration view.
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function create(Request $request): \Illuminate\Contracts\View\View
    {
        return view('ragbot::auth.tenant.register');
    }

    /**
     * Create a new registered user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        // The RegisterService::register method uses contextual binding to get Sanjay\Ragbot\Actions\Fortify\CreateNewUser
        event(new \Illuminate\Auth\Events\Registered($user = $this->service->register($request->all())));

        return redirect()->route('ragbot.tenant.login', ['project_slug' => $request->route('project_slug')])
            ->with('status', 'Registration successful. Please log in.');
    }
}
