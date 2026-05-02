<?php

namespace Sanjay\Ragbot\Http\Controllers\Auth\Tenant;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Fortify;
use Sanjay\Ragbot\Services\Auth\Tenant\LoginService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for handling tenant-specific user login.
 */
class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  LoginService  $service Resolved as Tenant\LoginService via DI.
     * @param  LoginResponse  $loginResponse Contextual binding resolves this to Sanjay\Ragbot\Actions\Fortify\LoginResponse.
     * @param  LogoutResponse  $logoutResponse Contextual binding resolves this to Sanjay\Ragbot\Actions\Fortify\LogoutResponse.
     * @param  StatefulGuard  $guard Contextual binding resolves this to the 'ragbot' guard.
     */
    public function __construct(
        protected LoginService $service,
        protected LoginResponse $loginResponse,
        protected LogoutResponse $logoutResponse,
        protected StatefulGuard $guard
    ) {}

    /**
     * Show the login view.
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function create(Request $request): \Illuminate\Contracts\View\View
    {
        return view('ragbot::auth.tenant.login');
    }

    /**
     * Attempt to authenticate a new session.
     *
     * @param  Request  $request
     * @return Response
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
     *
     * @param  Request  $request
     * @return Response
     */
    public function destroy(Request $request): Response
    {
        $this->service->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $this->logoutResponse->toResponse($request);
    }
}
