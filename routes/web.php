<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Coordinator\DashboardController as CoordinatorDashboardController;
use App\Http\Controllers\LegacyApiController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PublicApiController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\Therapist\DashboardController as TherapistDashboardController;
use App\Http\Controllers\Trainee\DashboardController as TraineeDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/gallery', [PageController::class, 'gallery'])->name('gallery');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/careers', [PageController::class, 'careers'])->name('careers');
Route::get('/testimonials', [PageController::class, 'testimonials'])->name('testimonials');
Route::get('/reviews', [PageController::class, 'reviews'])->name('reviews');
Route::get('/apply', [PageController::class, 'apply'])->name('apply');
Route::post('/apply', [PublicApiController::class, 'storeApplyForm'])->name('apply.store');
Route::get('/apply/confirmation', [PageController::class, 'applyConfirmation'])->name('apply.confirmation');

Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
Route::get('/resources/{slug}', [ResourceController::class, 'pillar'])->name('resources.pillar');
Route::get('/guides/{slug}', [ResourceController::class, 'article'])->name('resources.article');
Route::get('/sitemap.xml', [ResourceController::class, 'sitemap'])->name('sitemap');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login.store');
});
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::post('/api/book-session', [PublicApiController::class, 'bookSession'])->name('api.book-session');
Route::post('/api/submit-review', [PublicApiController::class, 'submitReview'])->name('api.submit-review');
Route::post('/api/submit-application', [PublicApiController::class, 'submitApplication'])->name('api.submit-application');

/*
|--------------------------------------------------------------------------
| Staff portals
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,coordinator'])->group(function () {
    Route::get('/admin/applications', [AdminDashboardController::class, 'applications'])->name('admin.applications');
    Route::any('/admin/api/update-application', function () {
        return app(LegacyApiController::class)->admin(request(), 'update-application');
    })->name('admin.api.update-application');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/bookings', [AdminDashboardController::class, 'bookings'])->name('bookings');
    Route::get('/reviews', [AdminDashboardController::class, 'reviews'])->name('reviews');
    Route::get('/my-session', [AdminDashboardController::class, 'mySession'])->name('my-session');
    Route::any('/api/{action}', [LegacyApiController::class, 'admin'])
        ->where('action', '[A-Za-z0-9\-_]+')
        ->name('api');
});

Route::middleware(['auth', 'role:therapist'])->prefix('therapist')->name('therapist.')->group(function () {
    Route::get('/dashboard', [TherapistDashboardController::class, 'index'])->name('dashboard');
    Route::any('/api/{action}', [LegacyApiController::class, 'therapist'])
        ->where('action', '[A-Za-z0-9\-_]+')
        ->name('api');
});

Route::middleware(['auth', 'role:trainee'])->prefix('trainee')->name('trainee.')->group(function () {
    Route::get('/dashboard', [TraineeDashboardController::class, 'index'])->name('dashboard');
    Route::any('/api/{action}', [LegacyApiController::class, 'trainee'])
        ->where('action', '[A-Za-z0-9\-_]+')
        ->name('api');
});

Route::middleware(['auth', 'role:coordinator'])->prefix('coordinator')->name('coordinator.')->group(function () {
    Route::get('/dashboard', [CoordinatorDashboardController::class, 'index'])->name('dashboard');
});
