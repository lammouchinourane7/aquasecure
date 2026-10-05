<?php

use App\Http\Controllers\Admin\AnalyseQualiteController;
use App\Http\Controllers\Admin\CapteurController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FinancementController;
use App\Http\Controllers\Admin\PointPrelevementController;
use App\Http\Controllers\Admin\ProjetRenovationController as AdminProjetRenovationController;
use App\Http\Controllers\Admin\ReleveController;
use App\Http\Controllers\Admin\ReseauEauController as AdminReseauEauController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjetRenovationController;
use App\Http\Controllers\QualiteEauController;
use App\Http\Controllers\ReseauEauController;
use App\Http\Controllers\SurveillanceController;
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

    // Binôme 4 — Surveillance / Capteurs (consultation citoyenne)
    Route::get('/surveillance', [SurveillanceController::class, 'index'])->name('surveillance.index');

    // Binôme 5 — Qualité de l'eau (consultation citoyenne)
    Route::get('/qualite-eau', [QualiteEauController::class, 'index'])->name('qualite.index');

    // Placeholders reserved for the pairs building citizen-facing participation
    // and renovation-funding tracking — not implemented yet.
    Route::get('/signalements', fn () => view('front.placeholder', [
        'title' => 'Signaler un problème',
        'icon' => 'megaphone',
    ]))->name('signalements.index');

    // Binôme 3 — Financement des rénovations (consultation citoyenne)
    Route::get('/projets', [ProjetRenovationController::class, 'index'])->name('projets.index');
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

    // Binôme 4 — Surveillance / Capteurs
    Route::resource('capteurs', CapteurController::class);
    Route::resource('releves', ReleveController::class)->parameters(['releves' => 'releve']);

    // Binôme 3 — Financement des rénovations
    Route::resource('projets', AdminProjetRenovationController::class)->parameters(['projets' => 'projet']);
    Route::resource('financements', FinancementController::class);

    // Binôme 5 — Qualité de l'eau
    Route::resource('points', PointPrelevementController::class);
    Route::resource('analyses', AnalyseQualiteController::class)->parameters(['analyses' => 'analyse']);
});

// Back-office routes reserved for admin only (user account management)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
});

require __DIR__.'/auth.php';
