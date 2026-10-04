<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReseauEauController as AdminReseauEauController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReseauEauController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Front (citizen-facing) routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/reseaux', [ReseauEauController::class, 'index'])->name('reseaux.index');

    // Placeholders reserved for the pairs building citizen-facing participation
    // and renovation-funding tracking — not implemented yet.
    Route::get('/signalements', fn () => view('front.placeholder', [
        'title' => 'Signaler un problème',
        'icon' => 'megaphone',
    ]))->name('signalements.index');

    Route::get('/projets', fn () => view('front.placeholder', [
        'title' => 'Projets de rénovation',
        'icon' => 'building',
    ]))->name('projets.index');
});

// Back-office (gestionnaire / admin) routes — infrastructure management
Route::middleware(['auth', 'role:gestionnaire,admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('zones', ZoneController::class)->except('show');
    Route::resource('reseaux', AdminReseauEauController::class)->parameters(['reseaux' => 'reseau']);

    // Placeholders reserved for the other pairs — routes exist so the sidebar
    // reflects the full app, but no CRUD is implemented behind them yet.
    Route::get('/incidents', fn () => view('back.placeholder', [
        'title' => 'Incidents',
        'icon' => 'alert-triangle',
    ]))->name('incidents.index');

    Route::get('/interventions', fn () => view('back.placeholder', [
        'title' => 'Interventions',
        'icon' => 'wrench',
    ]))->name('interventions.index');

    Route::get('/capteurs', fn () => view('back.placeholder', [
        'title' => 'Capteurs',
        'icon' => 'radar',
    ]))->name('capteurs.index');

    Route::get('/releves', fn () => view('back.placeholder', [
        'title' => 'Relevés',
        'icon' => 'chart',
    ]))->name('releves.index');

    Route::get('/projets', fn () => view('back.placeholder', [
        'title' => 'Projets de rénovation',
        'icon' => 'building',
    ]))->name('projets.index');

    Route::get('/financements', fn () => view('back.placeholder', [
        'title' => 'Financements',
        'icon' => 'coin',
    ]))->name('financements.index');

    Route::get('/signalements', fn () => view('back.placeholder', [
        'title' => 'Signalements',
        'icon' => 'megaphone',
    ]))->name('signalements.index');

    Route::get('/commentaires', fn () => view('back.placeholder', [
        'title' => 'Commentaires',
        'icon' => 'chat',
    ]))->name('commentaires.index');
});

// Back-office routes reserved for admin only (user account management)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
});

require __DIR__.'/auth.php';
