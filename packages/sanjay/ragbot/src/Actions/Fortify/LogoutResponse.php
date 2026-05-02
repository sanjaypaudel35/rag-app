<?php

namespace Sanjay\Ragbot\Actions\Fortify;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LogoutResponse implements LogoutResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  Request  $request
     * @return Response
     */
    public function toResponse($request)
    {
        $projectSlug = $request->route('project_slug') ?? optional(app('ragbot.project'))->slug;

        return $request->wantsJson()
                    ? response()->json(['logged_out' => true])
                    : redirect()->route('ragbot.login', ['project_slug' => $projectSlug]);
    }
}
