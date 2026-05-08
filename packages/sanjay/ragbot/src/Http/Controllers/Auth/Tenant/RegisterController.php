<?php

namespace Sanjay\Ragbot\Http\Controllers\Auth\Tenant;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
     * @param  RegisterService  $service  Resolved as Tenant\RegisterService via DI.
     * @param  RegisterResponse  $response  Contextual binding resolves this to Sanjay\Ragbot\Actions\Fortify\RegisterResponse.
     * @param  StatefulGuard  $guard  Contextual binding resolves this to the 'ragbot' guard.
     */
    public function __construct(
        protected RegisterService $service,
        protected RegisterResponse $response,
        protected StatefulGuard $guard
    ) {}

    /**
     * Show the registration view.
     */
    public function create(Request $request): View
    {
        return view('ragbot::auth.tenant.register');
    }

    /**
     * Create a new registered user.
     */
    public function store(Request $request): Response
    {
        DB::beginTransaction();

        try {
            // The RegisterService::register method uses contextual binding to get Sanjay\Ragbot\Actions\Fortify\CreateNewUser
            $user = $this->service->register($request->all());

            event(new Registered($user));

            DB::commit();

            return redirect()->route('ragbot.tenant.login', ['project_slug' => $request->route('project_slug')])
                ->with('status', 'Registration successful. Please log in.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Tenant registration failed: '.$e->getMessage());

            return back()->withInput()->withErrors(['email' => 'Registration failed. Please try again.']);
        }
    }
}
