<?php

namespace Sanjay\Ragbot\Actions\Fortify;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Symfony\Component\HttpFoundation\Response;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  Request  $request
     * @return Response
     */
    public function toResponse($request)
    {
        $slug = app('ragbot.project')->slug;

        return $request->wantsJson()
                    ? response()->json(['two_factor' => false], 201)
                    : redirect()->intended(route('ragbot.dashboard', ['project_slug' => $slug]));
    }
}
