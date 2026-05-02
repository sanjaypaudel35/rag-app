<?php

namespace Sanjay\Ragbot\Services\Auth;

use Laravel\Fortify\Contracts\CreatesNewUsers as FortifyCreateNewUser;

/**
 * Service for handling platform-level user registration logic.
 */
class RegisterService
{
    /**
     * Create a new service instance.
     *
     * @param  FortifyCreateNewUser  $creator Contextual binding resolves this to App\Actions\Fortify\CreateNewUser.
     */
    public function __construct(protected FortifyCreateNewUser $creator) {}

    /**
     * Register a new user.
     *
     * @param  array<string, mixed>  $input
     * @return mixed The created user instance.
     */
    public function register(array $input): mixed
    {
        return $this->creator->create($input);
    }
}
