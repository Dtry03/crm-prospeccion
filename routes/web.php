<?php

use App\Http\Controllers\CalendarEventController;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/admin'));

Route::middleware(['web', Authenticate::class])
    ->get('/admin/calendar/events', CalendarEventController::class)
    ->name('calendar.events');
