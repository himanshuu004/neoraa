<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class LegacyApiController extends Controller
{
    public function admin(Request $request, string $action): Response
    {
        return $this->handle($request, 'admin/api/'.$this->legacyFile($action));
    }

    public function therapist(Request $request, string $action): Response
    {
        return $this->handle($request, 'therapist/api/'.$this->legacyFile($action));
    }

    public function trainee(Request $request, string $action): Response
    {
        return $this->handle($request, 'trainee/api/'.$this->legacyFile($action));
    }

    public function handle(Request $request, string $legacyFile): Response
    {
        if (preg_match('#/(debug_|test_)#', $legacyFile)) {
            abort(404);
        }

        $fullPath = base_path('app/Legacy/'.$legacyFile);
        if (! is_file($fullPath)) {
            abort(404);
        }

        $pdo = DB::connection()->getPdo();
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['role'] = $user->roleName();

        if (! defined('BASE_URL')) {
            define('BASE_URL', base_url());
        }
        if (! defined('SITE_NAME')) {
            define('SITE_NAME', site_name());
        }

        ob_start();
        try {
            include $fullPath;
        } catch (\Throwable $e) {
            ob_end_clean();

            $message = app()->environment('production')
                ? 'Request could not be completed.'
                : $e->getMessage();

            return response(json_encode([
                'success' => false,
                'message' => $message,
            ]), 500)->header('Content-Type', 'application/json');
        }
        $output = ob_get_clean();

        $contentType = 'application/json';
        foreach (headers_list() as $header) {
            if (stripos($header, 'Content-Type:') === 0) {
                $contentType = trim(substr($header, 13));
            }
        }

        if (($contentType === 'application/json' || $contentType === '') && is_string($output) && preg_match('/^\s*</', $output)) {
            $contentType = 'text/html; charset=UTF-8';
        }

        return response($output === '' || $output === false ? '' : $output)
            ->header('Content-Type', $contentType);
    }

    private function legacyFile(string $action): string
    {
        $slug = strtolower(str_replace('-', '_', $action));
        $slug = preg_replace('/\.php$/', '', $slug);

        return $slug.'.php';
    }
}
