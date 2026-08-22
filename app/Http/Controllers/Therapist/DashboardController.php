<?php

namespace App\Http\Controllers\Therapist;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $therapist = User::query()
            ->with('therapistProfile')
            ->findOrFail(auth()->id());

        $profile = $therapist->therapistProfile;

        return view('therapist.dashboard', [
            'therapist' => (object) array_merge($therapist->toArray(), [
                'name' => $profile?->name,
                'contact' => $profile?->contact,
                'email' => $profile?->email,
                'profile_image' => $profile?->profile_image ?? $therapist->profile_image,
            ]),
            'pdo' => DB::connection()->getPdo(),
        ]);
    }
}
