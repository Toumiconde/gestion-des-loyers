<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProprietaireController;
use App\Http\Controllers\BienController;
use App\Http\Controllers\LocataireController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\QuittanceController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\RelanceController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ParametreController;

// ============================================================
// ROUTE PUBLIQUE — Page d'accueil redirige vers login
// ============================================================
Route::get('/', function () {
    return redirect()->route('login');
});

// ============================================================
// ROUTES PROTÉGÉES — Uniquement pour les utilisateurs connectés
// ============================================================
Route::middleware(['auth', 'verified'])->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])
         ->name('dashboard');

    // PROFIL — généré par Breeze, on le garde
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // PROPRIÉTAIRES
    Route::resource('proprietaires', ProprietaireController::class);

    // BIENS
    Route::resource('biens', BienController::class);

    // LOCATAIRES
    Route::resource('locataires', LocataireController::class);

    // CONTRATS
    Route::resource('contrats', ContratController::class);

    // PAIEMENTS
    Route::resource('paiements', PaiementController::class);

    // QUITTANCES
    Route::resource('quittances', QuittanceController::class);

    // INCIDENTS
    Route::resource('incidents', IncidentController::class);

    // RELANCES
    Route::resource('relances', RelanceController::class);

    // DOCUMENTS
    Route::resource('documents', DocumentController::class);

    // PARAMÈTRES
    Route::resource('parametres', ParametreController::class);

    // LOGS — Seulement admin
    Route::resource('activity-logs', ActivityLogController::class);

});

// ============================================================
// ROUTES D'AUTHENTIFICATION — Générées par Breeze
// ============================================================
require __DIR__.'/auth.php';