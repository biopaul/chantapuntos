<?php

use App\Http\Controllers\ActionController;
use App\Http\Controllers\ChildCardController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\PointTransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RedemptionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/c/{token}', [ChildCardController::class, 'show'])->name('child.card');

Route::get('/invitations/accept/{token}', [InvitationController::class, 'showAccept'])->name('invitations.accept');
Route::post('/invitations/accept/{token}', [InvitationController::class, 'processAccept'])->name('invitations.accept.process')->middleware('auth');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'landingImages' => [
            'dashboard' => asset('images/landing/mockup-dashboard.png'),
            'tareas' => asset('images/landing/mockup-tareas.png'),
            'recompensas' => asset('images/landing/mockup-recompensas.png'),
        ],
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/onboarding', [ChildController::class, 'index'])->name('onboarding');
    Route::post('/children', [ChildController::class, 'store'])->name('children.store');
    Route::put('/children/{child}', [ChildController::class, 'update'])->name('children.update');
    Route::get('/children/{child}/share-url', [ChildController::class, 'shareUrl'])->name('children.share-url');
    Route::delete('/children/{child}', [ChildController::class, 'destroy'])->name('children.destroy');
    Route::get('/actions', [ActionController::class, 'index'])->name('actions.index');
    Route::post('/actions', [ActionController::class, 'store'])->name('actions.store');
    Route::put('/actions/{action}', [ActionController::class, 'update'])->name('actions.update');
    Route::delete('/actions/{action}', [ActionController::class, 'destroy'])->name('actions.destroy');
    Route::post('/points/task', [PointTransactionController::class, 'storeTask'])->name('points.task');
    Route::post('/points/redeem', [PointTransactionController::class, 'storeRedeem'])->name('points.redeem');
    Route::get('/redemptions/create', [RedemptionController::class, 'create'])->name('redemptions.create');
    Route::get('/history', HistoryController::class)->name('history.index');
    Route::get('/invitations', [InvitationController::class, 'index'])->name('invitations.index');
    Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
