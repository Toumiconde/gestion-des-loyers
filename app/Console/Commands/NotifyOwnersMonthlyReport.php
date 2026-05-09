<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\MonthlyReportAvailable;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class NotifyOwnersMonthlyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-owners-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie une notification aux propriétaires pour leur signaler la disponibilité de leur relevé de gestion mensuel.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // On récupère le mois dernier (car on exécute la commande le 1er du mois en cours)
        $lastMonth = Carbon::now()->subMonth();
        $month = $lastMonth->month;
        $year = $lastMonth->year;

        $this->info("Début de l'envoi des notifications pour la période {$month}/{$year}...");

        $owners = User::where('role', 'proprietaire')->get();

        if ($owners->isEmpty()) {
            $this->warn("Aucun propriétaire trouvé.");
            return;
        }

        foreach ($owners as $owner) {
            $owner->notify(new MonthlyReportAvailable($month, $year));
            $this->line("Notification envoyée à : {$owner->name} ({$owner->email})");
        }

        $this->success("Terminé ! Toutes les notifications ont été envoyées.");
    }
}
