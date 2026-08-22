<?php

if (! function_exists('landing_asset')) {
    /**
     * Public landing media URL. Laravel's document root is /public,
     * so paths must not include a leading "public/" segment.
     */
    function landing_asset(string $path): string
    {
        $path = ltrim(preg_replace('#^public/#', '', str_replace('\\', '/', $path)), '/');

        $aliases = [
            'landing/images/2296b393-0309-42ba-b6cc-405569f55368.JPG' => 'landing/images/2296b393-0309-42ba-b6cc-405569f55368.webp',
            'landing/images/005c7ff0-5885-426a-9a03-f846973c7987.JPG' => 'landing/images/005c7ff0-5885-426a-9a03-f846973c7987.webp',
            'landing/images/494bd119-204d-4c5f-9b19-29fb0ae98677.JPG' => 'landing/images/494bd119-204d-4c5f-9b19-29fb0ae98677.webp',
            'landing/Director/Director.png' => 'landing/Director/Director.webp',
            'landing/sliders/2296b393-0309-42ba-b6cc-405569f55368.JPG' => 'landing/images/2296b393-0309-42ba-b6cc-405569f55368.webp',
            'landing/sliders/494bd119-204d-4c5f-9b19-29fb0ae98677.JPG' => 'landing/images/494bd119-204d-4c5f-9b19-29fb0ae98677.webp',
            'landing/sliders/2.JPG' => 'landing/sliders/2.webp',
        ];

        if (isset($aliases[$path])) {
            $path = $aliases[$path];
        }

        return asset($path);
    }
}

if (! function_exists('base_url')) {
    /**
     * Trailing-slash base URL matching the original BASE_URL constant.
     */
    function base_url(string $path = ''): string
    {
        $base = rtrim(url('/'), '/').'/';

        return $path === '' ? $base : $base.ltrim($path, '/');
    }
}

if (! function_exists('site_name')) {
    function site_name(): string
    {
        return config('app.name', 'Neora Therapy Management');
    }
}

if (! function_exists('public_file_exists')) {
    function public_file_exists(?string $relativePath): bool
    {
        if (! $relativePath) {
            return false;
        }

        return is_file(public_path($relativePath));
    }
}

if (! function_exists('role_name')) {
    function role_name(): string
    {
        $user = auth()->user();

        return strtolower((string) ($user?->role?->role_name ?? session('role', '')));
    }
}

if (! function_exists('neora_pdo')) {
    function neora_pdo(): PDO
    {
        return Illuminate\Support\Facades\DB::connection()->getPdo();
    }
}

if (! function_exists('ensure_notice_board_table')) {
    function ensure_notice_board_table(): void
    {
        static $ensured = false;
        if ($ensured) {
            return;
        }
        $ensured = true;

        if (\Illuminate\Support\Facades\Schema::hasTable('notice_board')) {
            return;
        }

        \Illuminate\Support\Facades\DB::statement("CREATE TABLE IF NOT EXISTS notice_board (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            created_by INT UNSIGNED DEFAULT NULL,
            priority ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
            status ENUM('active','inactive') NOT NULL DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }
}

if (! function_exists('isLoggedIn')) {
    function isLoggedIn(): bool
    {
        return auth()->check();
    }
}

if (! function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }
}

if (! function_exists('isTherapist')) {
    function isTherapist(): bool
    {
        return auth()->check() && auth()->user()->isTherapist();
    }
}

if (! function_exists('isCoordinator')) {
    function isCoordinator(): bool
    {
        return auth()->check() && auth()->user()->isCoordinator();
    }
}

if (! function_exists('isTrainee')) {
    function isTrainee(): bool
    {
        return auth()->check() && auth()->user()->isTrainee();
    }
}
