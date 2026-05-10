<?php

namespace Sanjay\Ragbot\Services\Auth\Tenant;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Validation\ValidationException;

/**
 * Service for handling tenant user login logic.
 */
class LoginService
{
    /**
     * Create a new service instance.
     *
     * @param  StatefulGuard  $guard  Contextual binding resolves this to the 'ragbot' guard.
     */
    public function __construct(protected StatefulGuard $guard) {}

    /**
     * Attempt to authenticate the tenant user.
     *
     * @param  array<string, string>  $credentials
     *
     * @throws ValidationException
     */
    public function login(array $credentials, bool $remember = false): void
    {
        if (! $this->guard->attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }
    }

    /**
     * Log the tenant user out.
     */
    public function logout(): void
    {
        $this->guard->logout();
    }
}
