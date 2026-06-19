# Cahier des Charges - GESTLOYER

## 1. Présentation du Projet
**GESTLOYER** est une plateforme SaaS de gestion immobilière professionnelle conçue pour automatiser les interactions entre une agence immobilière, les propriétaires et les locataires. L'objectif principal est de centraliser la collecte des loyers, de sécuriser les reversements aux propriétaires et de simplifier la maintenance technique des biens.

## 2. Objectifs Stratégiques
*   **Automatisation financière** : Gestion intelligente des paiements partiels, calcul automatique des soldes restants et génération de quittances.
*   **Transparence** : Offrir aux propriétaires une vision en temps réel de leurs revenus et des charges déduites.
*   **Communication centralisée** : Système de messagerie interne et support technique intégré.
*   **Sécurité** : Contrôle d'accès basé sur les rôles (RBAC) pour protéger les données sensibles.

## 3. Analyse des Utilisateurs (Rôles)
| Rôle | Description |
|------|-------------|
| **Administrateur** | Accès total au système, gestion du personnel, configuration de l'agence. |
| **Gestionnaire** | Gestion opérationnelle (biens, contrats, locataires, incidents). |
| **Comptable** | Validation des paiements, clôture des mois, exécution des reversements proprios. |
| **Propriétaire** | Consultation de ses biens, relevés de reversements, validation de devis travaux. |
| **Locataire** | Déclaration de paiements, consultation de quittances, signalement d'incidents. |

## 4. Spécifications Fonctionnelles

### 4.1. Module de Gestion Locative
*   **Inventaire des Biens** : Gestion des immeubles, appartements et unités locatives avec charges spécifiques.
*   **Base de données Tiers** : Fiches détaillées pour Propriétaires et Locataires.
*   **Gestion des Contrats** : Génération de baux, suivi des dates de début/fin, activation automatique au premier paiement.

### 4.2. Module Financier (Cœur du Système)
*   **Workflow de Paiement** :
    *   Déclaration par le locataire (Espèces, Virement, Mobile Money).
    *   **Paiements Partiels** : Cumul automatique des versements sur un même mois avec suivi du "Solde restant".
    *   **Validation Comptable** : Le comptable valide une fois que le loyer est complet (Solde = 0).
*   **Documents Financiers** :
    *   Quittances de loyer automatisées.
    *   Bordereaux de reversement pour les propriétaires.
*   **Reversements Propriétaires** : 
    *   Clôture mensuelle automatisée.
    *   Déduction des frais de gestion (commission agence) et des frais de maintenance.

### 4.3. Module de Suivi & Maintenance
*   **Gestion des Incidents** : Signalement par le locataire, assignation à un maintenancier par l'agence.
*   **Workflow de Devis** : Soumission de devis au propriétaire pour approbation avant travaux.
*   **Dépenses** : Suivi des charges de l'agence et des frais de maintenance.

### 4.4. Communication & Notifications
*   **Messagerie Interne** : Système de tickets support.
*   **Notifications** : Alertes par email/système pour les retards de paiement, nouveaux messages, et validations requises.

## 5. Spécifications Techniques
*   **Framework** : Laravel 11.x (PHP 8.2+).
*   **Base de données** : MySQL.
*   **Frontend** : TailwindCSS, Alpine.js (Design Premium & Responsive).
*   **Sécurité** : Authentification Laravel Breeze, protection CSRF, Validation stricte des entrées.
*   **Stockage** : Gestion des fichiers (Baux, Preuves de paiement, Devis) via le disque local/S3.

## 6. Design & Ergonomie (Aesthetics)
*   **Identité Visuelle** : Interface "Sky Blue" moderne, typographie lisible (Inter/Outfit).
*   **Dashboard Dynamique** : Graphiques Chart.js pour les revenus, taux d'occupation et statistiques globales.
*   **Responsive Design** : Utilisable sur PC, Tablette et Smartphone pour les locataires en déplacement.

## 7. Plan de Déploiement & Évolutions
1.  **Phase 1** : Stabilisation du noyau financier (Paiements & Quittances). **[EN COURS]**
2.  **Phase 2** : Module de reversement propriétaire et clôture mensuelle. **[TERMINÉ]**
3.  **Phase 3** : Automatisation des relances par SMS/WhatsApp.
4.  **Phase 4** : Exportation comptable avancée (Format Excel/PDF).

---
*Document généré le 13 Mai 2026 pour GESTLOYER.*
