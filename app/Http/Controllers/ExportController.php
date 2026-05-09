<?php

namespace App\Http\Controllers;

use App\Models\Locataire;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    private function getImageUrl($path)
    {
        if (!$path) return null;
        return asset('storage/' . $path);
    }

    private function getCommonData($title)
    {
        return [
            'logo'         => $this->getImageUrl(\App\Models\Parametre::getValue('logo')),
            'stamp'        => $this->getImageUrl(\App\Models\Parametre::getValue('cachet')),
            'company_name' => \App\Models\Parametre::getValue('nom_agence'),
            'title'        => $title,
        ];
    }

    public function exportProprietaires()
    {
        ob_end_clean();
        
        $headers = ['ID', 'Nom', 'Email', 'Téléphone', 'Adresse', 'Nombre de Biens', 'Date Création'];
        $data = [];
        
        Proprietaire::with('user', 'biens')->get()->each(function($p) use (&$data) {
            $data[] = [
                $p->id,
                $p->user->name ?? 'N/A',
                $p->user->email ?? 'N/A',
                $p->telephone ?? 'N/A',
                $p->adresse ?? 'N/A',
                $p->biens->count(),
                $p->created_at ? $p->created_at->format('d/m/Y') : 'N/A'
            ];
        });

        $exportData = array_merge($this->getCommonData('LISTE OFFICIELLE DES PROPRIÉTAIRES'), [
            'headers' => $headers,
            'data'    => $data,
        ]);

        $html = view('exports.excel', $exportData)->render();

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="proprietaires_premium_' . date('Y-m-d') . '.xls"');
    }

    public function exportLocataires()
    {
        ob_end_clean();
        
        $headers = ['ID', 'Nom', 'Prénom', 'Email', 'Téléphone', 'Adresse', 'Pièce Identité', 'Date Création'];
        $data = [];
        
        Locataire::all()->each(function($l) use (&$data) {
            $data[] = [
                $l->id,
                $l->nom,
                $l->prenom,
                $l->email ?? 'N/A',
                $l->telephone ?? 'N/A',
                $l->adresse ?? 'N/A',
                $l->piece_identite ?? 'N/A',
                $l->created_at ? $l->created_at->format('d/m/Y') : 'N/A'
            ];
        });

        $exportData = array_merge($this->getCommonData('REPERTOIRE GENERAL DES LOCATAIRES'), [
            'headers' => $headers,
            'data'    => $data,
        ]);

        $html = view('exports.excel', $exportData)->render();

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="locataires_premium_' . date('Y-m-d') . '.xls"');
    }

    public function exportBiens()
    {
        ob_end_clean();
        
        $headers = ['ID', 'Libellé', 'Type', 'Adresse', 'Surface (m2)', 'Loyer Base', 'Charges', 'Propriétaire', 'Statut'];
        $data = [];

        Bien::with('proprietaire.user')->all()->each(function($b) use (&$data) {
            $data[] = [
                $b->id,
                $b->libelle,
                $b->type,
                $b->adresse,
                $b->surface,
                $b->loyer_base,
                $b->charges,
                $b->proprietaire->user->name ?? 'N/A',
                $b->statut
            ];
        });

        $exportData = array_merge($this->getCommonData('ETAT GENERAL DU PARC IMMOBILIER'), [
            'headers' => $headers,
            'data'    => $data,
        ]);

        $html = view('exports.excel', $exportData)->render();

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="biens_premium_' . date('Y-m-d') . '.xls"');
    }

    public function exportContrats()
    {
        ob_end_clean();
        
        $headers = ['ID', 'Bien', 'Locataire', 'Date Début', 'Date Fin', 'Loyer', 'Caution', 'Statut'];
        $data = [];

        Contrat::with('bien', 'locataire')->all()->each(function($c) use (&$data) {
            $data[] = [
                $c->id,
                $c->bien->libelle ?? 'N/A',
                $c->locataire->nom_complet ?? 'N/A',
                $c->date_debut ? $c->date_debut->format('d/m/Y') : 'N/A',
                $c->date_fin ? $c->date_fin->format('d/m/Y') : 'Indéterminée',
                $c->loyer,
                $c->depot_garantie,
                $c->statut
            ];
        });

        $exportData = array_merge($this->getCommonData('REGISTRE DES CONTRATS DE BAIL'), [
            'headers' => $headers,
            'data'    => $data,
        ]);

        $html = view('exports.excel', $exportData)->render();

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="contrats_premium_' . date('Y-m-d') . '.xls"');
    }

    public function exportPaiements()
    {
        ob_end_clean();
        
        $headers = ['ID', 'Locataire', 'Bien', 'Mois Concerné', 'Montant', 'Date Paiement', 'Mode', 'Référence', 'Statut'];
        $data = [];

        Paiement::with('contrat.locataire', 'contrat.bien')->all()->each(function($p) use (&$data) {
            $data[] = [
                $p->id,
                $p->contrat->locataire->nom_complet ?? 'N/A',
                $p->contrat->bien->libelle ?? 'N/A',
                $p->mois_concerne ? $p->mois_concerne->format('M Y') : 'N/A',
                $p->montant,
                $p->date_paiement ? $p->date_paiement->format('d/m/Y') : 'N/A',
                $p->mode_reglement,
                $p->reference ?? 'N/A',
                $p->statut
            ];
        });

        $exportData = array_merge($this->getCommonData('JOURNAL DES ENCAISSEMENTS ET QUITTANCES'), [
            'headers' => $headers,
            'data'    => $data,
        ]);

        $html = view('exports.excel', $exportData)->render();

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="paiements_premium_' . date('Y-m-d') . '.xls"');
    }
}
