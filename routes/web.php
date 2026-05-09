<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProprietaireController;
use App\Http\Controllers\BienController;
use App\Http\Controllers\LocataireController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\QuittanceController;
use App\Http\Controllers\RelanceController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ParametreController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\AdminActionController;
use Illuminate\Support\Facades\Route;

// AUTH GOOGLE
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ONBOARDING (Choix du rôle)
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding/select', [OnboardingController::class, 'selectRole'])->name('onboarding.select');

    // GESTION
    Route::resource('proprietaires', ProprietaireController::class)->middleware('role:admin,gestionnaire');
    Route::resource('biens', BienController::class)->middleware('role:admin,gestionnaire,proprietaire,locataire');
    Route::resource('locataires', LocataireController::class)->middleware('role:admin,gestionnaire,proprietaire,locataire');
    Route::resource('contrats', ContratController::class);
    Route::resource('unites-locatives', \App\Http\Controllers\UniteLocativeController::class);

    // FINANCE
    Route::resource('paiements', PaiementController::class);
    Route::post('/paiements/{paiement}/relancer', [PaiementController::class, 'relancer'])->name('paiements.relancer');
    Route::get('/quittances/generate/{paiement}', [QuittanceController::class, 'generate'])->name('quittances.generate');
    Route::resource('quittances', QuittanceController::class);
    Route::resource('relances', RelanceController::class)->middleware('role:admin,gestionnaire,proprietaire');

    // SUIVI
    Route::resource('incidents', IncidentController::class);
    Route::resource('documents', DocumentController::class);

    // ADMINISTRATION
    Route::middleware('role:admin')->group(function() {
        Route::get('/parametres', [ParametreController::class, 'index'])->name('parametres.index');
        Route::get('/parametres/edit', [ParametreController::class, 'edit'])->name('parametres.edit');
        Route::post('/parametres/update', [ParametreController::class, 'update'])->name('parametres.update');
        Route::resource('activity-logs', ActivityLogController::class);
    });

    // DIFFUSION DE MASSE (BROADCAST)
    Route::middleware('role:admin,proprietaire')->group(function() {
        Route::get('/broadcast', [BroadcastController::class, 'index'])->name('broadcast.index');
        Route::post('/broadcast/send', [BroadcastController::class, 'send'])->name('broadcast.send');
    });

    Route::get('/messages/archived', [MessageController::class, 'archived'])->name('messages.archived');
    Route::post('/messages/{id}/restore', [MessageController::class, 'restore'])->name('messages.restore');
    Route::post('/messages/mark-as-read', [MessageController::class, 'markAllAsRead'])->name('messages.markAllAsRead');
    Route::resource('messages', MessageController::class);

    // HELP
    Route::get('/aide', [HelpController::class, 'index'])->name('help.index');
    Route::get('/aide/guide-admin', [HelpController::class, 'adminGuide'])->name('help.adminGuide');
    Route::get('/aide/guide-gestionnaire', [HelpController::class, 'gestionnaireGuide'])->name('help.gestionnaireGuide');
    Route::get('/aide/guide-locataire', [HelpController::class, 'locataireGuide'])->name('help.locataireGuide');
    Route::get('/aide/download', [HelpController::class, 'downloadGuide'])->name('help.downloadGuide');

    // FEEDBACK & CRITIQUES
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedbacks.index');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedbacks.store');
    Route::patch('/feedback/{feedback}/status', [FeedbackController::class, 'updateStatus'])->name('feedbacks.updateStatus')->middleware('role:admin');

    // MAINTENANCE & ASSISTANCE IA
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::post('/maintenance/{maintenanceRequest}/manual-response', [MaintenanceController::class, 'manualResponse'])->name('maintenance.manualResponse')->middleware('role:admin');
    Route::patch('/maintenance/{maintenanceRequest}/resolve', [MaintenanceController::class, 'resolve'])->name('maintenance.resolve')->middleware('role:admin');

    // NOTIFICATIONS & ARCHIVES
    Route::get('/archives', [ArchiveController::class, 'index'])->name('archives.index');
    Route::post('/archives/restore/{type}/{id}', [ArchiveController::class, 'restore'])->name('archives.restore');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    // NOUVELLES FONCTIONNALITÉS ADMIN (STAFF & DÉPENSES)
    Route::middleware('role:admin')->group(function() {
        Route::resource('staff', \App\Http\Controllers\StaffController::class);
        Route::resource('depenses', \App\Http\Controllers\DepenseController::class);
        Route::post('/admin/users/{user}/reset-password', [AdminActionController::class, 'resetPassword'])->name('admin.users.reset-password');
    });

    Route::get('/reports/monthly', [DashboardController::class, 'exportMonthly'])
        ->name('reports.monthly')
        ->middleware('role:admin,proprietaire');

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // RECHERCHE DE LOGEMENT (POUR LOCATAIRES)
    Route::get('/recherche', [\App\Http\Controllers\RechercheController::class, 'index'])->name('recherche.index');
    Route::get('/recherche/{unite}', [\App\Http\Controllers\RechercheController::class, 'show'])->name('recherche.show');
    Route::post('/recherche/{unite}/postuler', [\App\Http\Controllers\RechercheController::class, 'postuler'])->name('recherche.postuler');

    // EXPORTS EXCEL (CSV)
    Route::get('/export/proprietaires', [\App\Http\Controllers\ExportController::class, 'exportProprietaires'])->name('export.proprietaires');
    Route::get('/export/locataires', [\App\Http\Controllers\ExportController::class, 'exportLocataires'])->name('export.locataires');
    Route::get('/export/biens', [\App\Http\Controllers\ExportController::class, 'exportBiens'])->name('export.biens');
    Route::get('/export/contrats', [\App\Http\Controllers\ExportController::class, 'exportContrats'])->name('export.contrats');
    Route::get('/export/paiements', [\App\Http\Controllers\ExportController::class, 'exportPaiements'])->name('export.paiements');

    // GESTION DES DEMANDES DE LOCATION
    Route::get('/demandes-location', [\App\Http\Controllers\DemandeLocationController::class, 'index'])->name('demandes-location.index');
    Route::post('/demandes-location/{demande}/valider-proprietaire', [\App\Http\Controllers\DemandeLocationController::class, 'validerProprietaire'])->name('demandes-location.valider-proprietaire');
    Route::post('/demandes-location/{demande}/valider-admin', [\App\Http\Controllers\DemandeLocationController::class, 'validerAdmin'])->name('demandes-location.valider-admin');
    Route::post('/demandes-location/{demande}/rejeter', [\App\Http\Controllers\DemandeLocationController::class, 'rejeter'])->name('demandes-location.rejeter');
});

require __DIR__.'/auth.php';