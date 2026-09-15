<?php

use App\Http\Controllers\TailorController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/tailor');

// Resume tailoring.
Route::get('/tailor', [TailorController::class, 'create'])->name('tailor.create');
Route::post('/tailor', [TailorController::class, 'store'])->name('tailor.store');
Route::get('/tailor/result/{run}', [TailorController::class, 'result'])->name('tailor.result');

// History + re-download.
Route::get('/tailor/history', [TailorController::class, 'index'])->name('tailor.index');
Route::get('/tailor/history/{run}/download', [TailorController::class, 'download'])->name('tailor.download');
