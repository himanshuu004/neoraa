<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    private const DUMMY_HASH = '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

    private const MAX_ATTEMPTS = 5;

    private const LOCKOUT_SECONDS = 900;

    public function showLogin(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->to($this->dashboardUrl(Auth::user()));
        }

        return view('auth.login', [
            'error' => (string) $request->session()->get('login_error', ''),
            'saved_username' => $request->cookie('remember_username', ''),
            'saved_user_type' => $request->cookie('remember_user_type', 'admin'),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:64'],
            'password' => ['required', 'string', 'max:255'],
            'user_type' => ['required', 'in:admin,coordinator,therapist,trainee'],
        ]);

        if (filled($request->input('website'))) {
            $this->delay();

            return $this->failedLogin($request);
        }

        $username = trim($validated['username']);
        $password = $validated['password'];
        $userType = $validated['user_type'];
        $throttleKey = $this->throttleKey($request, $username);
        $ipKey = 'login-ip:'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)
            || RateLimiter::tooManyAttempts($ipKey, 20)) {
            $seconds = max(
                RateLimiter::availableIn($throttleKey),
                RateLimiter::availableIn($ipKey)
            );

            return back()
                ->withInput($request->only('username', 'user_type'))
                ->with('login_error', 'Too many sign-in attempts. Please wait '.max(1, (int) ceil($seconds / 60)).' minute(s) and try again.');
        }

        $user = User::query()
            ->where('username', $username)
            ->whereHas('role', fn ($q) => $q->where('role_name', $userType))
            ->with('role')
            ->first();

        $passwordOk = Hash::check($password, $user->password ?? self::DUMMY_HASH);

        if (! $user || ! $passwordOk) {
            RateLimiter::hit($throttleKey, self::LOCKOUT_SECONDS);
            RateLimiter::hit($ipKey, self::LOCKOUT_SECONDS);
            $this->delay();

            return $this->failedLogin($request);
        }

        RateLimiter::clear($throttleKey);
        RateLimiter::clear($ipKey);

        Auth::login($user, false);
        $request->session()->regenerate();

        try {
            Auth::logoutOtherDevices($password);
            $user->refresh();
        } catch (\Throwable) {
            // File sessions still rotate below; other-device logout is best-effort.
        }

        $request->session()->put(
            'password_hash_'.Auth::getDefaultDriver(),
            $user->getAuthPassword()
        );

        $role = $user->roleName();
        $request->session()->put([
            'user_id' => $user->id,
            'username' => $user->username,
            'role' => $role,
        ]);
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['role'] = $role;

        $this->queueRememberCookies($request, $username, $userType);

        return redirect()->intended($this->dashboardUrl($user));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function failedLogin(Request $request): RedirectResponse
    {
        return back()
            ->withInput($request->only('username', 'user_type'))
            ->with('login_error', 'Invalid username or password.');
    }

    private function throttleKey(Request $request, string $username): string
    {
        return Str::transliterate(Str::lower($username).'|'.$request->ip());
    }

    private function delay(): void
    {
        usleep(random_int(180000, 420000));
    }

    private function queueRememberCookies(Request $request, string $username, string $userType): void
    {
        $path = parse_url(url('/'), PHP_URL_PATH) ?: '/';
        $secure = $request->isSecure();

        if ($request->boolean('remember_me')) {
            Cookie::queue(cookie('remember_username', $username, 60 * 24 * 30, $path, null, $secure, true, false, 'lax'));
            Cookie::queue(cookie('remember_user_type', $userType, 60 * 24 * 30, $path, null, $secure, true, false, 'lax'));
        } else {
            Cookie::queue(Cookie::forget('remember_username', $path));
            Cookie::queue(Cookie::forget('remember_user_type', $path));
        }
    }

    private function dashboardUrl(User $user): string
    {
        return route($user->dashboardRouteName());
    }
}
