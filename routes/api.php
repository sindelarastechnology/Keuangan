<?php

use App\Http\Controllers\Api\V1\Admin\ChatModerationController;
use App\Http\Controllers\Api\V1\Admin\DonasiController;
use App\Http\Controllers\Api\V1\Admin\NotifyController;
use App\Http\Controllers\Api\V1\Admin\PlanController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Khusus akses sistem eksternal. Semua endpoint dijamin oleh middleware
| auth.integration (header X-Integration-Key) dan dibatasi throttle.
|
*/

Route::prefix('v1')
    ->middleware(['auth.integration', 'throttle:60,1'])
    ->group(function (): void {
        // --- User management (Flutter App Admin) ---
        Route::get('/users', [UserController::class, 'index'])->name('api.v1.users.index');
        Route::post('/users', [UserController::class, 'store'])->name('api.v1.users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('api.v1.users.show');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('api.v1.users.update');
        Route::patch('/users/{user}/status', [UserController::class, 'status'])->name('api.v1.users.status');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('api.v1.users.destroy');

        // --- Paket langganan (Fase 4) ---
        Route::get('/plans', [PlanController::class, 'index'])->name('api.v1.plans.index');
        Route::patch('/users/{user}/plan', [PlanController::class, 'setPlan'])->name('api.v1.plans.set');

        // --- Moderasi chat (Fase 4) ---
        Route::get('/chat/reports', [ChatModerationController::class, 'index'])->name('api.v1.chat.reports.index');
        Route::get('/chat/rooms', [ChatModerationController::class, 'rooms'])->name('api.v1.chat.rooms.index');
        Route::get('/chat/rooms/{room}/messages', [ChatModerationController::class, 'messages'])->name('api.v1.chat.messages.index');
        Route::post('/chat/rooms/{room}/messages', [ChatModerationController::class, 'send'])->name('api.v1.chat.messages.store');
        Route::patch('/chat/messages/{message}/moderate', [ChatModerationController::class, 'moderate'])->name('api.v1.chat.messages.moderate');
        Route::patch('/chat/reports/{report}', [ChatModerationController::class, 'resolve'])->name('api.v1.chat.reports.resolve');

        // --- Notifikasi (Fase 4) ---
        Route::get('/users/{user}/notifications', [NotifyController::class, 'index'])->name('api.v1.notifications.index');
        Route::post('/users/{user}/notify', [NotifyController::class, 'send'])->name('api.v1.notifications.send');

        // --- Manajemen donasi (Fase 5) ---
        Route::get('/donasi', [DonasiController::class, 'index'])->name('api.v1.donasi.index');
        Route::get('/donasi/{donasi}', [DonasiController::class, 'show'])->name('api.v1.donasi.show');
        Route::patch('/donasi/{donasi}/status', [DonasiController::class, 'status'])->name('api.v1.donasi.status');
    });
