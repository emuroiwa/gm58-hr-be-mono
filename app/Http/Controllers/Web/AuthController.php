<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterCompanyRequest;
use App\Services\AuthService;
use App\Events\CompanyRegistered;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    /**
     * Show login form
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login submission
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        try {
            $credentials = $request->only('email', 'password');
            $remember = $request->boolean('remember');

            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                
                return redirect()->intended('/dashboard')
                    ->with('success', 'Welcome back!');
            }

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');

        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'Login failed. Please try again.',
            ])->onlyInput('email');
        }
    }

    /**
     * Show registration form
     */
    public function showRegister(): View
    {
        $currencies = \App\Models\Currency::where('is_active', true)
            ->orderBy('name')
            ->get();

        $timezones = collect(timezone_identifiers_list())
            ->map(function ($timezone) {
                return [
                    'value' => $timezone,
                    'label' => $timezone . ' (' . now($timezone)->format('P') . ')',
                ];
            })
            ->groupBy(function ($item) {
                return explode('/', $item['value'])[0];
            });

        return view('auth.register', compact('currencies', 'timezones'));
    }

    /**
     * Handle registration submission
     */
    public function register(RegisterCompanyRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $result = $this->authService->registerCompany($data);

            // Log the user in
            Auth::login($result['user']);

            // Fire company registration event
            event(new CompanyRegistered($result['company'], $result['user']));

            return redirect('/dashboard')
                ->with('success', 'Welcome to your new HR system! Your company has been set up successfully.');

        } catch (\Exception $e) {
            return back()->withErrors([
                'general' => 'Registration failed: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle forgot password submission
     */
    public function forgotPassword(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Show reset password form
     */
    public function showResetPassword(string $token): View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Handle reset password submission
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('auth.login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * Verify email
     */
    public function verifyEmail(Request $request, string $id, string $hash): RedirectResponse
    {
        $user = \App\Models\User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect('/login')->withErrors(['email' => 'Invalid verification link']);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect('/dashboard')->with('status', 'Email already verified');
        }

        $user->markEmailAsVerified();

        return redirect('/dashboard')->with('success', 'Email verified successfully!');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}
