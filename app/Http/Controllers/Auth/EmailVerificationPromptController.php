<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        return $user->hasVerifiedEmail()
                    ? redirect()->intended(AuthenticatedSessionController::homeForUser($user))
                    : view('auth.verify-email');
    }
}
