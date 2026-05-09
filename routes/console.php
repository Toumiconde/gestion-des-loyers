<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ═══════════════════════════════════════════════════════════════
// PLANIFICATION DES TÂCHES AUTOMATIQUES
// ═══════════════════════════════════════════════════════════════

// Rappels J-5 : Chaque jour à 8h du matin
// Envoie un message aux locataires dont l'échéance est dans 5 jours
Schedule::command('app:send-payment-reminders')->dailyAt('08:00');

// Détection des retards : Chaque jour à 9h du matin
// Marque les paiements manquants/partiels comme "en_retard"
Schedule::command('app:mark-late-payments')->dailyAt('09:00');

// Génération des bilans mensuels : Le 1er de chaque mois à minuit
Schedule::command('bilans:generate')->monthlyOn(1, '00:00');

// Notification des propriétaires : Le 1er de chaque mois à 00:30 (après génération)
Schedule::command('app:notify-owners-report')->monthlyOn(1, '00:30');
