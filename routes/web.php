<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Calendar;
use App\Http\Livewire\GestorDashboard;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/calendar', Calendar::class)->name('calendar');

Route::get('/gestor-dashboard', GestorDashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('gestor-dashboard');

require __DIR__.'/auth.php';
