<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Notifications\MaintenanceAutoRepliedNotification;
use App\Notifications\MaintenanceNewRequestNotification;
use App\Notifications\MaintenanceManualRepliedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class MaintenanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            $requests = MaintenanceRequest::with('user')->latest()->paginate(15);
            return view('maintenance.admin_index', compact('requests'));
        } else {
            $requests = MaintenanceRequest::where('user_id', $user->id)->latest()->get();
            return view('maintenance.owner_index', compact('requests'));
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $message = $request->message;
        $autoResponse = $this->generateAutoResponse($message);

        $maintenanceRequest = MaintenanceRequest::create([
            'user_id'       => Auth::id(),
            'subject'       => $request->subject,
            'message'       => $message,
            'auto_response' => $autoResponse,
            'status'        => $autoResponse ? 'auto_replied' : 'pending_admin',
        ]);

        $admins = User::where('role', 'admin')->get();
        
        if ($autoResponse) {
            // Notifier les admins de la réponse auto
            Notification::send($admins, new MaintenanceAutoRepliedNotification($maintenanceRequest));
            return back()->with('success', '✅ Analyse terminée ! L\'Assistant Expert a trouvé la solution à votre demande ci-dessous.');
        }

        // Sinon, notifier les admins d'une nouvelle demande manuelle
        Notification::send($admins, new MaintenanceNewRequestNotification($maintenanceRequest));
        return back()->with('success', 'Votre demande a été transmise à l\'administration.');
    }

    private function generateAutoResponse($text)
    {
        $text = Str::lower($text);
        
        $knowledgeBase = [
            // === INVESTISSEMENT & PERFORMANCE ===
            'rentabilite|rendement|profit' => 'La rentabilité nette est calculée après déduction de nos frais de gestion (10%) et des frais de maintenance. Pour l\'optimiser, nous conseillons de valider rapidement les réparations mineures pour éviter qu\'elles ne deviennent majeures et coûteuses.',
            'investissement|patrimoine|valeur' => 'Pour valoriser votre patrimoine, l\'agence effectue une veille constante du marché. Un bien entretenu prend en moyenne 3 à 5% de valeur par an. Nous pouvons vous conseiller sur les rénovations à forte valeur ajoutée (peinture, sanitaires, cuisine).',
            'fiscalite|impots|declaration|revenus fonciers' => 'Chaque année en mars, nous générons un "Récapitulatif Fiscal Annuel" disponible dans vos documents. Il contient le total des loyers perçus et des charges déductibles (travaux, taxes) pour faciliter votre déclaration d\'impôts.',
            'net|revenu net|virement' => 'Vos virements sont effectués entre le 5 et le 10 de chaque mois, après encaissement effectif des loyers. Le montant net correspond au loyer brut moins les charges de l\'immeuble et nos honoraires de gestion.',
            'vacance|vide|inoccupe' => 'En cas de logement vide, notre protocole est immédiat : 1. Révision du prix selon le marché actuel. 2. Shooting photo professionnel. 3. Diffusion prioritaire sur nos canaux (SMS/Web). Nous visons un relocation en moins de 15 jours.',

            // === JURIDIQUE & PROCÉDURES ===
            'expulsion|impaye|litige|contentieux' => 'En cas d\'impayé : Jour 5 (Relance amiable), Jour 15 (Mise en demeure par voie d\'huissier), Jour 30 (Engagement de la procédure de résiliation de bail). L\'agence gère toute la relation avec les avocats et huissiers pour vous protéger.',
            'caution|depot de garantie|remboursement' => 'Le dépôt de garantie est conservé par l\'agence (ou par vous selon le contrat). Il est restitué au locataire sous 1 à 2 mois après son départ, déduction faite des éventuelles dégradations constatées lors de l\'état des lieux.',
            'bail|contrat|signature' => 'Tous nos baux sont conformes à la législation en vigueur. Ils incluent une clause de solidarité et une clause résolutoire pour sécuriser votre investissement au maximum.',
            'etat des lieux|entree|sortie' => 'Nous réalisons des états des lieux numériques avec photos haute définition. Ce document est votre seule preuve juridique en cas de dégradations causées par le locataire.',
            'preavis|depart|quitter' => 'Le locataire doit respecter un préavis (généralement 1 à 3 mois). Dès réception du préavis, nous commençons la recherche d\'un nouveau locataire pour éviter toute rupture de revenus.',

            // === TECHNIQUE & MAINTENANCE ===
            'incident|panne|reparation|fuite|electricite' => 'Notre réseau de techniciens agréés intervient sous 24h pour les urgences (eau, électricité). Pour les travaux de plus de 500 000 GNF, nous demandons systématiquement votre validation via le système avant d\'engager les frais.',
            'devis|prix travaux|facture maintenance' => 'Chaque devis est scanné et disponible dans votre menu "Documents". Vous pouvez les comparer ou nous demander de faire appel à votre propre technicien si vous préférez.',
            'sinistre|assurance|degat des eaux' => 'En cas de sinistre majeur, l\'agence gère la déclaration auprès des assurances. Nous vous demandons simplement de nous fournir votre numéro de police d\'assurance propriétaire non-occupant (PNO).',

            // === VIE DE L'AGENCE & FRAIS ===
            'commission|frais de gestion|honoraires' => 'Nos honoraires de gestion (10%) couvrent : la recherche de locataire, la rédaction des baux, l\'encaissement des loyers, la gestion des pannes et le suivi juridique. C\'est le prix de votre tranquillité d\'esprit.',
            'copropriete|syndic|charges' => 'Si votre bien est en copropriété, nous pouvons payer vos charges de syndic directement depuis vos revenus locatifs sur simple demande de votre part.',
            'horaires|contact|agence' => 'L\'agence est ouverte du Lundi au Vendredi (8h-18h) et le Samedi (9h-13h). Cependant, ce Dashboard et notre IA sont disponibles 24h/24 pour répondre à vos besoins urgents.',

            // === REDIRECTION ADMIN (ZONES RÉSERVÉES) ===
            'serveur|technique|bug|erreur' => 'Pour tout problème technique lié au logiciel (bug d\'affichage, erreur 500), merci de contacter directement notre support technique via le bouton "Support" de la sidebar.',
            'admin|config|reglage' => 'Certaines configurations (changement de logo, modification des taxes globales) sont réservées à la direction. Veuillez envoyer un message au "Directeur" via la messagerie pour ces demandes.',
        ];

        foreach ($knowledgeBase as $keywords => $response) {
            $keywordArray = explode('|', $keywords);
            foreach ($keywordArray as $keyword) {
                if (Str::contains($text, $keyword)) {
                    return "🤖 [Moteur de Recherche IA GestLoyer] : " . $response;
                }
            }
        }

        return "🤖 [Assistant IA] : Je ne suis pas certain de comprendre votre demande. Pouvez-vous préciser si cela concerne la 'rentabilité', les 'impayés', les 'travaux' ou votre 'virement' ? Je suis là pour vous conseiller sur tous les aspects de votre investissement.";
    }

    public function manualResponse(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        
        $request->validate([
            'admin_response' => 'required|string',
        ]);

        $maintenanceRequest->update([
            'admin_response' => $request->admin_response,
            'status' => 'resolved'
        ]);

        // Notifier le propriétaire de la réponse manuelle
        $maintenanceRequest->user->notify(new MaintenanceManualRepliedNotification($maintenanceRequest));

        return back()->with('success', 'Votre réponse manuelle a été enregistrée et le ticket est marqué comme résolu.');
    }

    public function resolve(MaintenanceRequest $maintenanceRequest)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        $maintenanceRequest->update(['status' => 'resolved']);
        return back()->with('success', 'Demande marquée comme résolue.');
    }
}
