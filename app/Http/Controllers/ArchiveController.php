<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Locataire;
use App\Models\Proprietaire;
use App\Models\Message;
use App\Models\Contrat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArchiveController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $selectedYear = session('selected_year', date('Y'));
        $selectedMonth = session('selected_month');
        
        // Requêtes de base pour les éléments supprimés
        $biens = Bien::onlyTrashed()->with('proprietaire.user')->whereYear('created_at', $selectedYear);
        $locataires = Locataire::onlyTrashed()->whereYear('created_at', $selectedYear);
        $proprietaires = Proprietaire::onlyTrashed()->with('user')->whereYear('created_at', $selectedYear);
        $messages = Message::onlyTrashed()->with(['sender', 'receiver'])->whereYear('created_at', $selectedYear);
        $contrats = Contrat::onlyTrashed()->with(['bien', 'locataire'])->whereYear('created_at', $selectedYear);

        if ($selectedMonth) {
            $biens->whereMonth('created_at', $selectedMonth);
            $locataires->whereMonth('created_at', $selectedMonth);
            $proprietaires->whereMonth('created_at', $selectedMonth);
            $messages->whereMonth('created_at', $selectedMonth);
            $contrats->whereMonth('created_at', $selectedMonth);
        }

        // Filtrage par rôle si ce n'est pas un admin
        if ($user->role !== 'admin') {
            if ($user->isProprietaire()) {
                $pId = $user->proprietaire->id ?? 0;
                $biens->where('proprietaire_id', $pId);
                $messages->where(fn($q) => $q->where('sender_id', $user->id)->orWhere('receiver_id', $user->id));
                $contrats->whereHas('bien', fn($q) => $q->where('proprietaire_id', $pId));
                $locataires->whereHas('contrats.bien', fn($q) => $q->where('proprietaire_id', $pId));
                $proprietaires->where('id', -1); // Un proprio ne voit pas d'autres proprios
            } elseif ($user->isLocataire()) {
                $lId = $user->locataire->id ?? 0;
                $biens->whereHas('contrats', fn($q) => $q->where('locataire_id', $lId));
                $messages->where(fn($q) => $q->where('sender_id', $user->id)->orWhere('receiver_id', $user->id));
                $contrats->where('locataire_id', $lId);
                $locataires->where('id', $lId);
                $proprietaires->where('id', -1);
            }
        }

        return view('archives.index', [
            'biens' => $biens->get(),
            'locataires' => $locataires->get(),
            'proprietaires' => $proprietaires->get(),
            'messages' => $messages->get(),
            'contrats' => $contrats->get(),
        ]);
    }

    public function restore($type, $id)
    {
        $modelClass = match($type) {
            'bien' => Bien::class,
            'locataire' => Locataire::class,
            'proprietaire' => Proprietaire::class,
            'message' => Message::class,
            'contrat' => Contrat::class,
            default => null
        };

        if (!$modelClass) abort(404);

        $item = $modelClass::withTrashed()->findOrFail($id);
        $item->restore();

        return back()->with('success', 'L\'élément a été restauré avec succès.');
    }
}
