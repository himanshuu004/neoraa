<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Models\TraineeProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $traineeId = auth()->id();
        $user = User::with('traineeProfile')->findOrFail($traineeId);

        if (! $user->traineeProfile || ! $user->traineeProfile->name) {
            TraineeProfile::updateOrCreate(
                ['user_id' => $traineeId],
                ['name' => $user->username ?? 'Trainee']
            );
            $user->load('traineeProfile');
        }

        $profile = $user->traineeProfile;

        return view('trainee.dashboard', [
            'trainee' => (object) array_merge($user->toArray(), [
                'name' => $profile?->name,
                'contact' => $profile?->contact,
                'email' => $profile?->email,
                'profile_image' => $profile?->profile_image ?? $user->profile_image,
            ]),
            'pdo' => DB::connection()->getPdo(),
        ]);
    }
}
