<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HiringApplication;
use App\Models\Kid;
use App\Models\SessionBooking;
use App\Models\TherapySession;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = User::findOrFail(auth()->id());
        ensure_notice_board_table();

        return view('admin.dashboard', [
            'admin' => $admin,
            'pdo' => DB::connection()->getPdo(),
        ]);
    }

    public function bookings()
    {
        if (! Schema::hasTable('session_bookings')) {
            DB::statement("CREATE TABLE IF NOT EXISTS session_bookings (
                id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name         VARCHAR(255) NOT NULL,
                phone        VARCHAR(50)  NOT NULL,
                service      VARCHAR(255) DEFAULT NULL,
                message      TEXT         DEFAULT NULL,
                status       ENUM('new','reached_out','talked','closed') NOT NULL DEFAULT 'new',
                created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        $bookings = SessionBooking::query()->orderByDesc('created_at')->get();

        return view('admin.bookings', [
            'bookings' => $bookings,
            'pdo' => DB::connection()->getPdo(),
        ]);
    }

    public function reviews()
    {
        return view('admin.reviews', [
            'pdo' => DB::connection()->getPdo(),
        ]);
    }

    public function applications()
    {
        $applications = HiringApplication::query()
            ->leftJoin('users', 'hiring_applications.generated_by', '=', 'users.id')
            ->select('hiring_applications.*', 'users.username as generated_by_name')
            ->orderByDesc('hiring_applications.created_at')
            ->get();

        return view('admin.application', [
            'applications' => $applications,
            'isCoordinator' => auth()->user()->isCoordinator(),
            'pdo' => DB::connection()->getPdo(),
        ]);
    }

    public function mySession()
    {
        $timeSlots = TimeSlot::query()->orderBy('sort_order')->orderBy('time_start')->get();
        $kids = Kid::query()->orderBy('kid_name')->get();
        $sessions = TherapySession::query()
            ->where('therapist_id', auth()->id())
            ->orderBy('day_of_week')
            ->orderBy('time_slot')
            ->get();

        return view('admin.my_session', [
            'timeSlots' => $timeSlots,
            'kids' => $kids,
            'sessions' => $sessions,
            'pdo' => DB::connection()->getPdo(),
        ]);
    }
}
