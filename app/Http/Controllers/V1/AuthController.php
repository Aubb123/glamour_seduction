<?php

namespace App\Http\Controllers\V1;

use App\Enums\Users\UserStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

use function __;

class AuthController extends Controller
{
    protected string $backendLangPath = 'v1.backend.auth';

    public function login(): View
    {
        return view('v1.auth.email.login');
    }

    public function loginStore(Request $request): RedirectResponse
    {
        // Validation
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        // Tentative de connexion
        if (! Auth::attempt($validated, $request->boolean('remember'))) {
            return redirect()->back()->withInput($request->only('email'))->with('error', __("{$this->backendLangPath}.login.invalid_credentials"));
        }

        // Régénérer la session immédiatement après connexion
        $request->session()->regenerate();

        $user = Auth::user();

        // Compte suspendu ou banni → déconnecter et rediriger
        if (in_array($user->status, [UserStatus::Suspended, UserStatus::Banned], true)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('account.disabled')->with('error', __("{$this->backendLangPath}.login.account_suspended"));
        }

        // Email non vérifié → envoyer OTP (le middleware gère la redirection vers la vérification)
        if (! $user->email_verified_at && function_exists('sendEmailOtpCode')) {
            sendEmailOtpCode($user);
        }

        return redirect()->intended(route('frontend.index'))->with('success', __("{$this->backendLangPath}.login.success"));
    }

    // ─────────────────────────────────────────────
    // Pages de statut / vérification (cibles des middlewares)
    // ─────────────────────────────────────────────
    public function accountDisabled(): View
    {
        return view('v1.auth.account.disabled');
    }

    public function emailVerificationOtp(): View
    {
        return view('v1.auth.email.verify-otp');
    }

    public function phoneVerificationOtp(): View
    {
        return view('v1.auth.phone.verify-otp');
    }

    public function logout(Request $request): RedirectResponse
    {

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', __("{$this->backendLangPath}.logout.success"));

    }
}
