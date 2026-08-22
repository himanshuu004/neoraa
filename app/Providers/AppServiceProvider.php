<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (! defined('BASE_URL')) {
            define('BASE_URL', rtrim((string) config('app.url'), '/').'/');
        }
        if (! defined('SITE_NAME')) {
            define('SITE_NAME', site_name());
        }

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
            config(['session.secure' => true]);
        }

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function (Request $request) {
                    return redirect()
                        ->route('login')
                        ->withInput($request->only('username', 'user_type'))
                        ->with('login_error', 'Too many sign-in attempts. Please wait a minute and try again.');
                });
        });

        View::composer('*', function ($view) {
            $view->with('baseUrl', base_url());
            $view->with('siteName', site_name());
            try {
                $view->with('pdo', neora_pdo());
            } catch (\Throwable $e) {
                $view->with('pdo', null);
            }
        });
    }
}
