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
use App\Http\Controllers\MaintenancierController;
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
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ONBOARDING (Choix du rôle)
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding/select', [OnboardingController::class, 'selectRole'])->name('onboarding.select');

    // GESTION (Full pour Admin/Gestionnaire)
    Route::middleware('role:admin,gestionnaire')->group(function() {
        Route::resource('proprietaires', ProprietaireController::class)->except(['index', 'show']);
        Route::resource('biens', BienController::class)->except(['index', 'show']);
        Route::resource('locataires', LocataireController::class)->except(['index', 'show']);
        Route::resource('contrats', ContratController::class)->except(['index', 'show']);
        Route::resource('unites-locatives', \App\Http\Controllers\UniteLocativeController::class);
    });

    // GESTION (Lecture pour Staff + Clients selon logique controlleur)
    Route::middleware('role:admin,gestionnaire,comptable,proprietaire,locataire')->group(function() {
        Route::get('/proprietaires', [ProprietaireController::class, 'index'])->name('proprietaires.index');
        Route::get('/proprietaires/{proprietaire}', [ProprietaireController::class, 'show'])->name('proprietaires.show');
        Route::get('/contrats', [ContratController::class, 'index'])->name('contrats.index');
        Route::get('/contrats/{contrat}', [ContratController::class, 'show'])->name('contrats.show');
    });

    Route::middleware('role:proprietaire,locataire,admin,gestionnaire,comptable')->group(function() {
        Route::get('/biens', [BienController::class, 'index'])->name('biens.index');
        Route::get('/biens/{bien}', [BienController::class, 'show'])->name('biens.show');
        Route::get('/locataires', [LocataireController::class, 'index'])->name('locataires.index');
        Route::get('/locataires/{locataire}', [LocataireController::class, 'show'])->name('locataires.show');
    });

    // FINANCE (Consultation pour tous, Création pour locataires, Gestion pour Admin/Comptable)
    Route::middleware('role:admin,gestionnaire,locataire,comptable')->group(function() {
        Route::get('/paiements/create', [PaiementController::class, 'create'])->name('paiements.create');
        Route::post('/paiements', [PaiementController::class, 'store'])->name('paiements.store');
    });

    Route::middleware('role:admin,gestionnaire,proprietaire,locataire,comptable')->group(function() {
        Route::get('/paiements', [PaiementController::class, 'index'])->name('paiements.index');
        Route::get('/paiements/{paiement}', [PaiementController::class, 'show'])->name('paiements.show');
        Route::get('/quittances', [QuittanceController::class, 'index'])->name('quittances.index');
        Route::get('/quittances/{quittance}', [QuittanceController::class, 'show'])->name('quittances.show');
    });

    Route::middleware('role:admin,gestionnaire,comptable')->group(function() {
        Route::patch('/paiements/{paiement}', [PaiementController::class, 'update'])->name('paiements.update');
        Route::delete('/paiements/{paiement}', [PaiementController::class, 'destroy'])->name('paiements.destroy');
        Route::post('/paiements/{paiement}/relancer', [PaiementController::class, 'relancer'])->name('paiements.relancer');
        Route::get('/quittances/generate/{paiement}', [QuittanceController::class, 'generate'])->name('quittances.generate');
        Route::resource('quittances', QuittanceController::class)->except(['index', 'show']);
        Route::resource('relances', RelanceController::class);
    });

    // SUIVI (Incidents et Documents)
    Route::middleware('role:admin,gestionnaire')->group(function() {
        Route::resource('incidents', IncidentController::class)->except(['index', 'show', 'create', 'store']);
        Route::resource('documents', DocumentController::class)->except(['index', 'show']);
    });
    
    Route::middleware('role:admin,gestionnaire,locataire')->group(function() {
        Route::get('/incidents/create', [IncidentController::class, 'create'])->name('incidents.create');
        Route::post('/incidents', [IncidentController::class, 'store'])->name('incidents.store');
    });
    
    // Workflow incidents
    Route::middleware('role:admin,gestionnaire')->group(function() {
        Route::post('/incidents/{incident}/assigner-maintenancier', [IncidentController::class, 'assignerMaintenancier'])->name('incidents.assignerMaintenancier');
        Route::post('/incidents/{incident}/envoyer-devis', [IncidentController::class, 'envoyerDevisProprietaire'])->name('incidents.envoyerDevis');
        Route::post('/incidents/{incident}/cloturer', [IncidentController::class, 'cloturer'])->name('incidents.cloturer');
        
        // Gestion des maintenanciers
        Route::resource('maintenanciers', MaintenancierController::class);
    });
    
    Route::middleware('role:proprietaire')->group(function() {
        Route::post('/incidents/{incident}/accepter-devis', [IncidentController::class, 'accepterDevis'])->name('incidents.accepterDevis');
        Route::post('/incidents/{incident}/refuser-devis', [IncidentController::class, 'refuserDevis'])->name('incidents.refuserDevis');
    });

    Route::get('/incidents', [IncidentController::class, 'index'])->name('incidents.index');
    Route::get('/incidents/{incident}', [IncidentController::class, 'show'])->name('incidents.show');
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');

    // ADMINISTRATION
    Route::middleware('role:admin')->group(function() {
        Route::get('/parametres', [ParametreController::class, 'index'])->name('parametres.index');
        Route::get('/parametres/edit', [ParametreController::class, 'edit'])->name('parametres.edit');
        Route::post('/parametres/update', [ParametreController::class, 'update'])->name('parametres.update');
        Route::resource('activity-logs', ActivityLogController::class);
    });

    // DIFFUSION DE MASSE (BROADCAST)
    Route::middleware('role:admin,gestionnaire')->group(function() {
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
    });

    // RÉINITIALISATION MOT DE PASSE : Admin + Gestionnaire
    Route::middleware('role:admin,gestionnaire')->group(function() {
        Route::post('/admin/users/{user}/reset-password', [AdminActionController::class, 'resetPassword'])->name('admin.users.reset-password');
    });

    Route::middleware('role:admin,gestionnaire,comptable')->group(function() {
        Route::resource('depenses', \App\Http\Controllers\DepenseController::class);
    });

    Route::get('/reports/monthly', [DashboardController::class, 'exportMonthly'])
        ->name('reports.monthly')
        ->middleware('role:admin,proprietaire,gestionnaire,comptable');

    Route::post('/reports/cloturer', [DashboardController::class, 'cloturer'])
        ->name('reports.cloturer')
        ->middleware('role:admin,gestionnaire,comptable');

    // REVERSEMENTS
    Route::get('/reversements', [\App\Http\Controllers\ReversementController::class, 'index'])
        ->name('reversements.index')
        ->middleware('role:admin,comptable,proprietaire');

    Route::get('/reversements/{bilan}', [\App\Http\Controllers\ReversementController::class, 'show'])
        ->name('reversements.show')
        ->middleware('role:admin,comptable,proprietaire');

    Route::post('/reversements/{bilan}/payer', [\App\Http\Controllers\ReversementController::class, 'markAsPaid'])
        ->name('reversements.markAsPaid')
        ->middleware('role:admin,comptable');

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // RECHERCHE DE LOGEMENT (POUR LOCATAIRES)
    Route::get('/recherche', [\App\Http\Controllers\RechercheController::class, 'index'])->name('recherche.index');
    Route::get('/recherche/{unite}', [\App\Http\Controllers\RechercheController::class, 'show'])->name('recherche.show');
    Route::post('/recherche/{unite}/postuler', [\App\Http\Controllers\RechercheController::class, 'postuler'])->name('recherche.postuler');

    // EXPORTS EXCEL (CSV)
    Route::middleware('role:admin,gestionnaire,proprietaire,comptable')->group(function() {
        Route::get('/export/locataires', [\App\Http\Controllers\ExportController::class, 'exportLocataires'])->name('export.locataires');
        Route::get('/export/biens', [\App\Http\Controllers\ExportController::class, 'exportBiens'])->name('export.biens');
        Route::get('/export/paiements', [\App\Http\Controllers\ExportController::class, 'exportPaiements'])->name('export.paiements');
    });

    Route::middleware('role:admin,gestionnaire,comptable')->group(function() {
        Route::get('/export/proprietaires', [\App\Http\Controllers\ExportController::class, 'exportProprietaires'])->name('export.proprietaires');
        Route::get('/export/contrats', [\App\Http\Controllers\ExportController::class, 'exportContrats'])->name('export.contrats');
    });

    // GESTION DES DEMANDES DE LOCATION
    Route::middleware('role:admin,gestionnaire,locataire,proprietaire')->group(function() {
        Route::get('/demandes-location', [\App\Http\Controllers\DemandeLocationController::class, 'index'])->name('demandes-location.index');
    });

    Route::middleware('role:admin,gestionnaire')->group(function() {
        Route::post('/demandes-location/{demande}/valider-admin', [\App\Http\Controllers\DemandeLocationController::class, 'validerAdmin'])->name('demandes-location.valider-admin');
        Route::post('/demandes-location/{demande}/rejeter', [\App\Http\Controllers\DemandeLocationController::class, 'rejeter'])->name('demandes-location.rejeter');
    });
    
    // Le propriétaire peut juste donner un avis (optionnel dans un vrai système mais on garde une trace)
    Route::post('/demandes-location/{demande}/valider-proprietaire', [\App\Http\Controllers\DemandeLocationController::class, 'validerProprietaire'])->name('demandes-location.valider-proprietaire')->middleware('role:proprietaire');
});

Route::post('/notifications/read-all', function () {
    auth()->user()->unreadNotifications->markAsRead();
    return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
})->name('notifications.readAll')->middleware('auth');

// ASSISTANCE IA : COMPLETION OU SUPPRESSION DE PROFIL
Route::middleware('auth')->group(function () {
    Route::post('/profile/complete', [\App\Http\Controllers\ProfileController::class, 'complete'])->name('profile.complete');
    Route::post('/profile/abort', [\App\Http\Controllers\ProfileController::class, 'abort'])->name('profile.abort');
});

require __DIR__.'/auth.php';