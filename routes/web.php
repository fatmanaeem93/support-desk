<?php

use App\Livewire\TicketDesk;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::middleware('auth')->group(function () {
    Route::get('/tickets', TicketDesk::class)->name('tickets.index');
// });