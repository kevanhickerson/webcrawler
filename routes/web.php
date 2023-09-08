<?php

use App\Http\Controllers\CrawlController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::controller(CrawlController::class)->group(function() {
    Route::get('/', 'search');
    Route::get('/results', 'results');
});
