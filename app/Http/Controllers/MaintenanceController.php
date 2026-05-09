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
            // === GESTION DES LOCATAIRES & CONTRATS ===
            'locataire' => 'Pour gérer ou créer un locataire : Allez dans le menu "Contrats". Un locataire est toujours lié à un contrat de bail. Cliquez sur "Nouveau Contrat", sélectionnez le bien vide, et remplissez les informations du nouveau locataire. C\'est cette action qui crée officiellement le locataire dans votre système.',
            'guide' => 'Guide de création : 1. Allez dans "Contrats". 2. Cliquez sur "Nouveau". 3. Choisissez le Bien. 4. Saisissez les infos du locataire (Nom, Tél, Loyer). 5. Validez. Le système s\'occupe du reste !',
            'creation' => 'La création d\'un nouvel élément se fait par les menus dédiés : "Biens Immobiliers" pour les maisons, et "Contrats" pour les nouveaux locataires.',
            'contrat' => 'Chaque contrat définit la durée, le loyer et la caution. Vous pouvez consulter, modifier ou télécharger le bail en format PDF directement depuis la liste des contrats.',
            'bail' => 'Le bail (contrat) est généré automatiquement. Assurez-vous que les informations du bien et du locataire sont correctes avant de l\'imprimer.',
            'caution' => 'La caution (ou dépôt de garantie) est enregistrée lors de la création du contrat. Elle apparaît dans le résumé financier du locataire.',
            'resiliation' => 'Pour mettre fin à un contrat : Allez dans "Contrats", sélectionnez le contrat actif et utilisez l\'option "Clôturer" ou "Résilier". Cela libérera le bien automatiquement.',
            'depart' => 'Lors du départ d\'un locataire, assurez-vous de marquer le contrat comme terminé pour que le bien repasse en statut "Libre" dans vos statistiques.',

            // === GESTION DES BIENS IMMOBILIERS ===
            'bien' => 'Pour gérer vos biens : Allez dans le menu "Biens Immobiliers". Vous pouvez y voir la liste complète, ajouter une photo pour chaque maison/appartement, et vérifier le statut (Occupé ou Libre). Si vous voulez en ajouter un nouveau, utilisez le bouton indigo "Ajouter un bien" en haut à droite.',
            'maison' => 'Pour vos maisons, vous pouvez préciser l\'adresse exacte et le nombre de pièces dans la fiche descriptive du bien.',
            'appartement' => 'La gestion des appartements permet d\'indiquer l\'étage et le numéro de porte pour une meilleure organisation de votre parc.',
            'ajouter' => 'L\'ajout d\'un bien est instantané. Cliquez sur le bouton "+" dans "Biens Immobiliers" et remplissez le formulaire.',
            'modifier' => 'Pour modifier une information, cliquez sur l\'icône de "Crayon" à droite de la ligne concernée dans vos tableaux.',
            'supprimer' => 'Attention : La suppression déplace l\'élément dans le "Centre d\'Archives". Vous pourrez le restaurer si vous changez d\'avis.',

            // === FINANCES, LOYERS & QUITTANCES ===
            'loyer' => 'Le montant du loyer est celui fixé dans le contrat. Le système génère un appel de loyer chaque mois. Vous pouvez suivre les paiements dans le Dashboard.',
            'paiement' => 'Les paiements peuvent être "Complets", "Partiels" ou "En retard". Chaque transaction est enregistrée avec sa date et son mode de règlement.',
            'versement' => 'Tout versement effectué par un locataire doit être validé par vous ou l\'admin pour générer la quittance correspondante.',
            'quittance' => 'La quittance est le reçu officiel. Elle est générée en PDF dès que le paiement est marqué comme "Payé". Elle porte votre signature numérique par défaut.',
            'recu' => 'Le reçu de paiement est identique à la quittance. Vous pouvez l\'envoyer par email en un clic depuis la liste des quittances.',
            'retard' => 'En cas de retard, le système affiche une alerte rouge. Utilisez l\'icône "Avion" pour envoyer une relance immédiate par SMS ou Email.',
            'impaye' => 'Pour les impayés persistants, vous pouvez consulter l\'historique global du locataire pour préparer un dossier de recouvrement.',

            // === OUTILS, STATS & TECHNIQUE ===
            'chiffre' => 'Vos statistiques (Dashboard) incluent : Revenus mensuels, Taux d\'occupation et Total des créances en attente.',
            'graphique' => 'Les graphiques vous permettent de visualiser la rentabilité de vos biens sur l\'année en cours.',
            'bilan' => 'Pour un bilan annuel, filtrez vos quittances par année pour obtenir le total de vos revenus fonciers.',
            'pdf' => 'Tous nos documents (Baux, Quittances, Rapports) sont exportables en format PDF haute qualité.',
            'telecharger' => 'Cherchez l\'icône de "Flèche vers le bas" ou de "Fichier" pour télécharger vos documents sur votre ordinateur ou téléphone.',
            'imprimer' => 'Ouvrez le fichier PDF généré et utilisez la commande "Imprimer" de votre navigateur ou de votre lecteur PDF.',
            'archive' => 'Le Centre d\'Archives stocke vos éléments supprimés. C\'est une sécurité pour ne jamais perdre de données importantes.',
            'restaurer' => 'Pour restaurer, allez dans "Centre d\'Archives", trouvez l\'élément et cliquez sur le bouton de restauration bleu.',

            // === COMPTE & SECURITE ===
            'profil' => 'Dans votre profil, vous pouvez changer votre photo, votre numéro de téléphone et surtout votre Signature Digitale.',
            'signature' => 'La signature se fait à la souris ou au doigt. Elle est cruciale pour que vos quittances soient juridiquement valables.',
            'password' => 'Pour changer votre mot de passe, allez dans "Profil" > "Sécurité". Utilisez un mot de passe robuste (lettres, chiffres, symboles).',
            'connexion' => 'Si vous avez des problèmes de connexion, vérifiez que votre email est correct ou utilisez la fonction "Mot de passe oublié".',

            // === COMMUNICATION ===
            'message' => 'La messagerie permet d\'envoyer des notifications à vos locataires. Les messages de l\'Admin sont par contre non-répondables (infos officielles).',
            'sms' => 'Les SMS de relance sont envoyés automatiquement si vous utilisez la fonction de relance rapide sur les impayés.',
            'email' => 'Chaque quittance est automatiquement envoyée par email au locataire si son adresse est renseignée dans sa fiche.',
            
            // === REDIRECTION ADMIN (ZONES RÉSERVÉES) ===
            'serveur' => 'Requête Serveur : Cette question touche à l\'infrastructure technique profonde. Seul l\'administrateur peut intervenir physiquement sur le serveur.',
            'config' => 'Configuration Système : Les réglages globaux (taxes, noms d\'agence, logo système) sont gérés par l\'Admin. Veuillez le contacter pour toute modification.',
            'admin' => 'Droits Admin : Vous demandez l\'accès à une zone réservée à la direction de l\'agence. Merci de vous présenter à l\'agence pour obtenir ces droits.',
            'securite' => 'Protocole de Sécurité : Pour modifier les paramètres de sécurité avancés, une vérification d\'identité physique à l\'agence est obligatoire.',
        ];

        foreach ($knowledgeBase as $keyword => $response) {
            if (Str::contains($text, $keyword)) {
                return "🤖 [Assistant IA Expert] : " . $response;
            }
        }

        return null;
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
