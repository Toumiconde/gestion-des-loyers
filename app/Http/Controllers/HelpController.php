<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $faqs = [];
        $advancedGuides = [];
        $admin = \App\Models\User::where('role', 'admin')->first(); // Pour le support technique

        if ($user->role === 'admin') {
            $faqs = [
                [
                    'question' => 'Comment gérer les accès et rôles ?',
                    'answer' => 'Allez dans les paramètres système pour définir qui peut accéder aux finances ou à la gestion des biens.'
                ],
                [
                    'question' => 'Où est le journal d\'audit ?',
                    'answer' => 'Le journal d\'activité est disponible dans le menu Administration. Il trace chaque clic et chaque action sur la plateforme.'
                ],
                [
                    'question' => 'Configuration du cachet agence ?',
                    'answer' => 'Dans Paramètres > Infos Agence, vous pouvez uploader le cachet officiel qui sera utilisé par défaut sur les quittances.'
                ]
            ];
            $advancedGuides = [
                [
                    'title' => 'Supervision des flux financiers',
                    'content' => 'Utilisez le Dashboard pour voir le résumé Flash 24h et le Top 3 des actions pour un contrôle total.'
                ],
                [
                    'title' => 'Gestion du recouvrement',
                    'content' => 'Utilisez l\'icône "Relancer" pour envoyer des SMS/Emails automatiques aux locataires en retard.'
                ]
            ];
        } elseif ($user->role === 'proprietaire') {
            $faqs = [
                [
                    'question' => 'Comment voir mes revenus ?',
                    'answer' => 'Votre dashboard affiche uniquement les loyers encaissés pour vos propres biens.'
                ],
                [
                    'question' => 'Comment signer mes quittances ?',
                    'answer' => 'Allez dans votre profil, cliquez sur "Signer maintenant" pour enregistrer votre signature digitale.'
                ]
            ];
            $advancedGuides = [
                [
                    'title' => 'Suivi des locataires',
                    'content' => 'Consultez la liste de vos biens pour voir quel locataire est à jour et lequel a des retards.'
                ],
                [
                    'title' => 'Besoin d\'assistance technique ?',
                    'content' => 'Pour toute question sur l\'utilisation de l\'application ou un blocage technique, allez dans le menu "Incidents" et cliquez sur le bouton indigo "Besoin d\'aide maintenance ?". Notre IA vous répondra instantanément.'
                ]
            ];
        } elseif ($user->role === 'locataire') {
            $faqs = [
                [
                    'question' => 'Comment payer mon loyer ?',
                    'answer' => 'Dès que vous effectuez un virement ou un dépôt, informez l\'administrateur ou téléchargez votre preuve de paiement.'
                ],
                [
                    'question' => 'Où trouver mes quittances ?',
                    'answer' => 'Allez dans "Mes Paiements", chaque mois validé possède un bouton de téléchargement PDF.'
                ]
            ];
            $advancedGuides = [
                [
                    'title' => 'Signaler un problème',
                    'content' => 'Utilisez le module "Incidents" pour envoyer une photo et une description d\'un problème dans votre logement.'
                ]
            ];
        }

        return view('help.index', compact('faqs', 'advancedGuides', 'admin'));
    }

    public function downloadGuide(Request $request)
    {
        $role = $request->get('role', auth()->user()->role);
        
        // Génération dynamique avec DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.guide', ['role' => $role]);
        
        return $pdf->download('Guide_' . ucfirst($role) . '_Gestloyer.pdf');
    }

    public function adminGuide()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $path = \App\Models\Parametre::getValue('guide_admin');
        if ($path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return redirect(\Illuminate\Support\Facades\Storage::url($path));
        }

        return view('help.admin-guide');
    }

    public function gestionnaireGuide()
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isGestionnaire()) {
            abort(403);
        }
        return view('help.gestionnaire-guide');
    }

    public function locataireGuide()
    {
        return view('help.locataire-guide');
    }
}
