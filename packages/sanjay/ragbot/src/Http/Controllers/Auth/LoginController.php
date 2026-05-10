<?php

namespace Sanjay\Ragbot\Http\Controllers\Auth;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Fortify;
use Sanjay\Ragbot\Services\Auth\LoginService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for handling platform-level user login.
 */
class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  LoginService  $service  Resolved as platform LoginService via DI.
     * @param  LoginResponse  $loginResponse  Contextual binding resolves this to the application's default LoginResponse.
     * @param  LogoutResponse  $logoutResponse  Contextual binding resolves this to the application's default LogoutResponse.
     * @param  StatefulGuard  $guard  Contextual binding resolves this to the application's default guard.
     */
    public function __construct(
        protected LoginService $service,
        protected LoginResponse $loginResponse,
        protected LogoutResponse $logoutResponse,
        protected StatefulGuard $guard
    ) {}

    /**
     * Show the login view.
     */
    public function create(Request $request): View
    {
        return view('ragbot::auth.platform.login');
    }

    /**
     * Attempt to authenticate a new session.
     */
    public function store(Request $request): Response
    {
        $this->service->login(
            $request->only(Fortify::username(), 'password'),
            $request->boolean('remember')
        );

        $request->session()->regenerate();

        return $this->loginResponse->toResponse($request);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        $this->service->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $this->logoutResponse->toResponse($request);
    }
}
