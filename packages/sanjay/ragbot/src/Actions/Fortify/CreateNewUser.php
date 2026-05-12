<?php

namespace Sanjay\Ragbot\Actions\Fortify;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Sanjay\Ragbot\Models\Project;
use Sanjay\Ragbot\Models\RagbotUser;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): RagbotUser
    {
        Validator::make($input, [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ])->validate();

        /** @var Project $project */
        $project = app('ragbot.project');

        return RagbotUser::create([
            'project_id' => $project->id,
            'firstname' => $input['firstname'],
            'lastname' => $input['lastname'],
            'name' => $input['firstname'].' '.$input['lastname'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
