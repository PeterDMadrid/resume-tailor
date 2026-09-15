<?php

use App\Http\Controllers\ConstantsController;
use App\Http\Controllers\TailorController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/tailor');

// Resume tailoring.
Route::get('/tailor', [TailorController::class, 'create'])->name('tailor.create');
Route::post('/tailor', [TailorController::class, 'store'])->name('tailor.store');
Route::get('/tailor/result/{run}', [TailorController::class, 'result'])->name('tailor.result');

// Preview the resume template from config only (no AI) — streamed inline.
Route::get('/tailor/preview', [TailorController::class, 'preview'])->name('tailor.preview');

// History + re-download.
Route::get('/tailor/history', [TailorController::class, 'index'])->name('tailor.index');
Route::get('/tailor/history/{run}/download', [TailorController::class, 'download'])->name('tailor.download');
Route::delete('/tailor/history/{run}', [TailorController::class, 'destroy'])->name('tailor.destroy');

// Constant (always-on) skills management.
Route::get('/constants', [ConstantsController::class, 'index'])->name('constants.index');
Route::post('/constants', [ConstantsController::class, 'store'])->name('constants.store');
Route::delete('/constants/{constantSkill}', [ConstantsController::class, 'destroy'])->name('constants.destroy');
