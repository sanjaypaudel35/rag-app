<?php

namespace Sanjay\Ragbot\Http\Controllers\Auth\Tenant;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Sanjay\Ragbot\Models\RagbotUser;

class EmailVerificationController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(string $project_slug, string $id, string $hash): RedirectResponse
    {
        /** @var RagbotUser|null $user */
        $user = RagbotUser::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('ragbot.tenant.login', ['project_slug' => $project_slug])
                ->with('status', 'Email already verified.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->route('ragbot.tenant.login', ['project_slug' => $project_slug])
            ->with('status', 'Email verified successfully. You can now log in.');
    }
}
