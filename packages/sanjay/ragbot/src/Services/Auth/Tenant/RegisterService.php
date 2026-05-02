<?php

namespace Sanjay\Ragbot\Services\Auth\Tenant;

use Laravel\Fortify\Contracts\CreatesNewUsers as FortifyCreateNewUser;

/**
 * Service for handling tenant user registration logic.
 */
class RegisterService
{
    /**
     * Create a new service instance.
     *
     * @param  FortifyCreateNewUser  $creator Contextual binding resolves this to Sanjay\Ragbot\Actions\Fortify\CreateNewUser.
     */
    public function __construct(protected FortifyCreateNewUser $creator) {}

    /**
     * Register a new tenant user.
     *
     * @param  array<string, mixed>  $input
     * @return mixed The created user instance.
     */
    public function register(array $input): mixed
    {
        return $this->creator->create($input);
    }
}
