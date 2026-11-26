<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.forgot');
});

Route::post('/locale', [AuthController::class, 'switchLocale'])->name('locale.switch');

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::get('/members/{member}', [MemberController::class, 'show'])->name('members.show');

    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

    Route::put('/profile/member', [ProfileController::class, 'updateMember'])->name('profile.member.update');

    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::get('/portal/search', [NotificationController::class, 'search'])->name('portal.search');

    Route::middleware('admin')->group(function (): void {
        Route::post('/members', [MemberController::class, 'store'])->name('members.store');
        Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');
        Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
        Route::delete('/members/{member}/avatar', [MemberController::class, 'deleteAvatar'])->name('members.avatar.delete');
        Route::post('/members/{member}/points', [MemberController::class, 'awardPoints'])->name('members.points');

        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::patch('/attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
        Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');

        Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
        Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
        Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');
        Route::delete('/activities/{activity}/banner', [ActivityController::class, 'deleteBanner'])->name('activities.banner.delete');
        Route::post('/activities/{activity}/check-in', [ActivityController::class, 'checkIn'])->name('activities.check-in');

        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
    });
});
