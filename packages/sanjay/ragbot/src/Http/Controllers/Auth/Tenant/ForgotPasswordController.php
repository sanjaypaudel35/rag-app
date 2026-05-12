<?php

namespace Sanjay\Ragbot\Http\Controllers\Auth\Tenant;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password view.
     */
    public function create(Request $request)
    {
        return view('ragbot::auth.tenant.forgot-password');
    }

    /**
     * Send a reset link to the given user.
     */
    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Since we are in project context, we should ideally check if user belongs to this project
        // But the notification will handle the project context.

        $status = Password::broker('ragbot_users')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
