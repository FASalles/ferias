<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Calendar;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/calendar', Calendar::class)->name('calendar');

require __DIR__.'/auth.php';
