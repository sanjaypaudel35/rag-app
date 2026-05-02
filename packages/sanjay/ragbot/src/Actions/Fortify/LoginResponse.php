<?php

namespace Sanjay\Ragbot\Actions\Fortify;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
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
                    ? response()->json(['two_factor' => false])
                    : redirect()->intended(route('ragbot.dashboard', ['project_slug' => $slug]));
    }
}
