<?php

use App\Http\Controllers\ChatController;
use App\Livewire\ChatComponent;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    
    Route::group(['prefix' => 'chat', 'as' => 'chat.'], function () {
        Route::get('/users', [ChatController::class, 'users'])->name('users');
        Route::get('/{receiverId}', [ChatController::class, 'chat'])->name('chat');
        Route::post('/{receiverId}/send', [ChatController::class, 'sendMessage'])->name('send');
        Route::post('/typing', [ChatController::class, 'typing'])->name('typing');
        Route::post('/online', [ChatController::class, 'setOnline'])->name('online');
        Route::post('/offline', [ChatController::class, 'setOffline'])->name('offline');
    });
});

require __DIR__ . '/auth.php';
