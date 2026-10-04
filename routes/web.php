<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Front (citizen-facing) routes
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('front.home');
    })->name('home');

    Route::get('/signalements/create', function () {
        return view('front.signalements.create');
    })->name('signalements.create');
});

// Back-office (gestionnaire / admin) routes
Route::middleware(['auth', 'role:gestionnaire,admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('back.dashboard');
    })->name('dashboard');

    Route::get('/reseaux', function () {
        return view('back.reseaux.index');
    })->name('reseaux.index');

    Route::get('/incidents', function () {
        return view('back.incidents.index');
    })->name('incidents.index');

    Route::get('/projets', function () {
        return view('back.projets.index');
    })->name('projets.index');

    Route::get('/capteurs', function () {
        return view('back.capteurs.index');
    })->name('capteurs.index');

    Route::get('/signalements', function () {
        return view('back.signalements.index');
    })->name('signalements.index');
});

require __DIR__.'/auth.php';
