<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = User::findOrFail(auth()->id());

        return view('coordinator.dashboard', [
            'user' => $user,
            'pdo' => DB::connection()->getPdo(),
        ]);
    }
}
