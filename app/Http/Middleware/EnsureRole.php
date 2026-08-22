<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            return redirect()->guest(route('login'));
        }

        $current = strtolower((string) auth()->user()->roleName());
        $allowed = array_map('strtolower', $roles);

        if ($allowed && ! in_array($current, $allowed, true)) {
            abort(403, 'You do not have access to this page.');
        }

        $this->syncLegacySession();

        return $next($request);
    }

    private function syncLegacySession(): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['role'] = $user->roleName();
    }
}
