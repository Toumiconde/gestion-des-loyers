<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Guide Utilisateur GESTLOYER</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #334155; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 50px; border-bottom: 2px solid #3b82f6; padding-bottom: 20px; }
        h1 { color: #1e293b; font-size: 28px; margin-bottom: 10px; }
        h2 { color: #3b82f6; border-left: 4px solid #3b82f6; padding-left: 10px; margin-top: 30px; }
        .role-badge { background: #dbeafe; color: #1e40af; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 12px; }
        .footer { margin-top: 50px; font-size: 10px; text-align: center; color: #94a3b8; }
        .step { margin-bottom: 15px; }
        .step-title { font-weight: bold; color: #0f172a; }
    </style>
</head>
<body>
    <div class="header">
        @php $settings = \App\Models\Parametre::all()->pluck('valeur', 'cle'); @endphp
        @if(!empty($settings['logo']))
            <img src="{{ public_path('storage/' . $settings['logo']) }}" style="height: 60px; margin-bottom: 10px;">
        @endif
        <h1>GESTLOYER</h1>
        <p>Guide d'utilisation Officiel - Version 2026</p>
        <span class="role-badge">{{ strtoupper($role) }}</span>
    </div>

    @if($role === 'admin')
        <div>
            <h2>1. INTRODUCTION À L'ADMINISTRATION</h2>
            <p>GESTLOYER est un système de gestion locative de haute précision. En tant qu'administrateur, vous avez la responsabilité de configurer l'agence, de superviser les flux financiers et de garantir l'intégrité des données.</p>
            
            <h3>La Hiérarchie des Accès (RBAC)</h3>
            <p>Le système distingue 5 niveaux d'accès :</p>
            <ul>
                <li><strong>Administrateur :</strong> Accès total (Finances, Paramètres, Utilisateurs).</li>
                <li><strong>Propriétaire :</strong> Consultation de ses biens et bilans financiers personnels.</li>
                <li><strong>Locataire :</strong> Déclaration de paiement, messagerie et signalement d'incidents.</li>
                <li><strong>Gestionnaire :</strong> Gestion opérationnelle (Contrats, Biens) sans accès aux paramètres critiques.</li>
                <li><strong>Comptable :</strong> Focus sur les encaissements et la validation des quittances.</li>
            </ul>
        </div>

        <div>
            <h2>2. GESTION DU PATRIMOINE (BIENS)</h2>
            <p>Chaque bien immobilier est le point central du système. Sans bien, il n'y a pas de contrat, et donc pas de revenu.</p>
            <h3>Enregistrement d'un Bien</h3>
            <p>Lors de l'ajout d'un bien, veillez à renseigner :</p>
            <ul>
                <li><strong>Le Propriétaire :</strong> Obligatoire pour la ventilation des revenus.</li>
                <li><strong>Loyer de Base & Charges :</strong> Le système sépare ces deux montants pour une quittance légale.</li>
                <li><strong>Photos & Documents :</strong> Indispensables pour le marketing et les assurances.</li>
            </ul>
            <h3>Statuts des Biens</h3>
            <p>Un bien peut être <strong>Libre</strong>, <strong>Occupé</strong> (contrat actif), ou <strong>En Maintenance</strong> (en cas de travaux lourds).</p>
        </div>

        <div>
            <h2>3. INGÉNIERIE FINANCIÈRE</h2>
            <p>La comptabilité de GESTLOYER est automatisée pour éviter les erreurs humaines.</p>
            <h3>Workflow des Paiements</h3>
            <p>1. Le locataire déclare son paiement (Statut : En attente).<br>
               2. L'admin vérifie la preuve visuelle et les fonds.<br>
               3. L'admin valide (Statut : Payé).<br>
               4. La quittance est générée instantanément.</p>
            
            <h3>Le Système de Ventilation (Split)</h3>
            <p>Lorsqu'un paiement annuel est reçu, GESTLOYER le divise automatiquement en 12 lignes de crédit mensuelles. Cela permet au propriétaire de voir une évolution constante de sa trésorerie plutôt qu'un pic unique suivi de 11 mois vides.</p>
        </div>

        <div>
            <h2>4. COMMUNICATION & MESSAGERIE</h2>
            <p>Le module de messagerie est doté d'une intelligence de tri.</p>
            <ul>
                <li><strong>Urgences :</strong> Les messages contenant des mots comme "Fuite", "Feu" ou "Danger" sont marqués en rouge sur le dashboard.</li>
                <li><strong>Broadcast :</strong> Vous pouvez envoyer un rappel de loyer à tous les locataires en retard en un seul clic.</li>
            </ul>
        </div>

        <div>
            <h2>5. AUDIT & SÉCURITÉ TECHNIQUE</h2>
            <p>Pour la transparence totale, chaque action est logguée.</p>
            <ul>
                <li><strong>Activity Logs :</strong> Consultez la liste de qui a modifié un prix ou validé un paiement avec l'heure exacte.</li>
                <li><strong>Soft Deletes :</strong> Si un bien est supprimé par erreur, il va dans la "Corbeille" et peut être restauré. Rien n'est jamais perdu définitivement sans double validation.</li>
            </ul>
        </div>

        <div>
            <h2>6. CONFIGURATION DE L'AGENCE</h2>
            <p>Allez dans <strong>Paramètres</strong> pour :</p>
            <ul>
                <li>Changer le Logo (apparaît sur toutes les quittances).</li>
                <li>Mettre à jour la Signature digitale de l'agence.</li>
                <li>Configurer les coordonnées bancaires pour les virements.</li>
            </ul>
        </div>
    @elseif($role === 'locataire')
        <h2>1. Signaler un incident</h2>
        <div class="step">
            <p class="step-title">Étape 1 : Accéder au module</p>
            <p>Cliquez sur "Incidents" dans votre menu latéral. C'est ici que vous gérez tous les problèmes techniques de votre logement.</p>
        </div>
        <div class="step">
            <p class="step-title">Étape 2 : Décrire le problème</p>
            <p>Cliquez sur "Signaler un incident". Donnez un titre clair, choisissez le type (plomberie, électricité) et décrivez précisément la situation.</p>
        </div>
        <div class="step">
            <p class="step-title">Étape 3 : Ajouter une photo</p>
            <p>Une photo vaut mille mots. Téléchargez une image de la panne pour que le gestionnaire puisse agir plus vite.</p>
        </div>

        <h2>2. Paiements & Quittances</h2>
        <div class="step">
            <p class="step-title">Suivi financier</p>
            <p>Dans la section "Mes Paiements", vous pouvez voir l'état de vos loyers. Dès qu'un paiement est validé par l'agence, vous pouvez télécharger votre quittance PDF certifiée.</p>
        </div>
    @elseif($role === 'proprietaire')
        <h2>1. Suivi des Revenus</h2>
        <div class="step">
            <p class="step-title">Tableau de bord</p>
            <p>Votre dashboard vous donne une vision instantanée de vos revenus nets. Le graphique compare vos encaissements mois par mois.</p>
        </div>
        <h2>2. Bilans de Gestion</h2>
        <div class="step">
            <p class="step-title">Automatisation</p>
            <p>Le système clôture vos comptes le 1er de chaque mois. Vous recevez alors automatiquement votre bilan mensuel dans la section "Relevés Officiels".</p>
        </div>
    @endif

    <div class="footer">
        <p>© 2026 GESTLOYER - Plateforme de Gestion Locative Intelligente</p>
        <p>Ce document est généré dynamiquement pour votre profil administrateur.</p>
    </div>
</body>
</html>
