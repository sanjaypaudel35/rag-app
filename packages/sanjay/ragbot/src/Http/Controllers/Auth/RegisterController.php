<?php

namespace Sanjay\Ragbot\Http\Controllers\Auth;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Contracts\RegisterResponse;
use Sanjay\Ragbot\Services\Auth\RegisterService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for handling platform-level user registration.
 */
class RegisterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  RegisterService  $service  Resolved as platform RegisterService via DI.
     * @param  RegisterResponse  $response  Contextual binding resolves this to the application's default RegisterResponse.
     * @param  StatefulGuard  $guard  Contextual binding resolves this to the application's default guard.
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
        return view('ragbot::auth.platform.register');
    }

    /**
     * Create a new registered user.
     */
    public function store(Request $request): Response
    {
        DB::beginTransaction();

        try {
            // The RegisterService::register method uses contextual binding to get App\Actions\Fortify\CreateNewUser
            $user = $this->service->register($request->all());

            event(new Registered($user));

            DB::commit();

            return redirect()->route('ragbot.login')->with('status', 'Registration successful. Please log in.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Registration failed: '.$e->getMessage());

            return back()->withInput()->withErrors(['email' => 'Registration failed. Please try again.']);
        }
    }
}
