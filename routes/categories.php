<?php
// routes/categories.php

use App\Http\Controllers\TicketCategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::resource('categories', TicketCategoryController::class)
        ->except(['show']);
});
