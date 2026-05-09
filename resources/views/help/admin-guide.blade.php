@extends('layouts.app')

@section('title', 'Manuel d\'Exploitation & Administration - GESTLOYER')

@section('content')

<div class="max-w-6xl mx-auto py-12 px-6">
    {{-- Top Bar --}}
    <div class="flex items-center justify-between mb-12 no-print">
        <a href="{{ route('help.index') }}" class="flex items-center gap-2 text-slate-500 hover:text-slate-800 transition-colors font-black text-xs uppercase tracking-widest">
            <i class="fa-solid fa-arrow-left"></i> Retour à l'espace aide
        </a>
        <div class="flex gap-4">
            <button onclick="window.print()" class="px-8 py-4 bg-slate-900 text-white font-black rounded-2xl hover:bg-blue-600 shadow-xl transition-all active:scale-95 flex items-center gap-3">
                <i class="fa-solid fa-file-pdf"></i> Exporter le Manuel Complet
            </button>
        </div>
    </div>

    {{-- Cover Page (Print Only) --}}
    <div class="hidden print:block h-[280mm] flex flex-col justify-center items-center text-center border-8 border-slate-900 p-20 mb-20">
        <div class="w-32 h-32 bg-slate-900 rounded-[40px] flex items-center justify-center text-white text-6xl font-black mb-12">G</div>
        <h1 class="text-7xl font-black text-slate-900 mb-6 tracking-tighter">GESTLOYER PRO</h1>
        <p class="text-3xl font-bold text-blue-600 uppercase tracking-[0.3em] mb-20">Manuel de Haute Administration</p>
        <div class="w-40 h-2 bg-blue-600 mb-20"></div>
        <p class="text-xl text-slate-500 font-medium max-w-2xl leading-relaxed">
            Ce document constitue le référentiel unique d'utilisation et de configuration du système de gestion locative GESTLOYER. Toute modification de la structure logicielle doit être reportée dans ce manuel.
        </p>
        <div class="mt-auto pt-20">
            <p class="font-black text-slate-900">Version 2.5 (Édition Professionnelle)</p>
            <p class="text-slate-400 font-bold uppercase tracking-widest text-xs mt-2">Dernière mise à jour : {{ date('d F Y') }}</p>
        </div>
    </div>

    {{-- Introduction --}}
    <div class="bg-white rounded-[60px] p-20 shadow-2xl shadow-slate-200/50 border border-slate-100 mb-16 relative overflow-hidden print:hidden">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-600/5 rounded-full"></div>
        <div class="relative z-10">
            <span class="px-6 py-2 bg-blue-50 text-blue-600 rounded-full text-[11px] font-black uppercase tracking-[0.2em] mb-8 inline-block">Référentiel Administrateur</span>
            <h1 class="text-6xl font-black text-slate-800 mb-8 leading-[1.1] tracking-tighter">Maîtrisez <span class="text-blue-600">chaque aspect</span> de votre plateforme.</h1>
            <p class="text-slate-500 text-2xl font-medium max-w-3xl leading-relaxed mb-12">
                Bienvenue dans le manuel d'exploitation GESTLOYER. Ce guide a été conçu pour transformer tout nouvel administrateur en expert du système en moins d'une heure.
            </p>
            <div class="flex flex-wrap gap-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-blue-600 text-xl shadow-sm"><i class="fa-solid fa-shield-check"></i></div>
                    <p class="font-bold text-slate-700">Sécurité Maximale</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-emerald-600 text-xl shadow-sm"><i class="fa-solid fa-chart-line"></i></div>
                    <p class="font-bold text-slate-700">Finance Auditée</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-purple-600 text-xl shadow-sm"><i class="fa-solid fa-bolt"></i></div>
                    <p class="font-bold text-slate-700">Flux Automatisés</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Sommaire Interactif --}}
    <div class="bg-slate-900 rounded-[50px] p-16 mb-24 no-print shadow-2xl shadow-slate-300">
        <h2 class="text-2xl font-black text-white mb-10 uppercase tracking-widest flex items-center gap-4">
            <i class="fa-solid fa-list-ul text-blue-500"></i> Sommaire Détaillé
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            <div class="space-y-4">
                <p class="text-blue-500 font-black text-xs uppercase tracking-widest border-b border-white/10 pb-4">01. Fondations & Accès</p>
                <ul class="space-y-3">
                    <li><a href="#roles" class="text-slate-400 hover:text-white transition-colors text-sm font-medium flex items-center gap-2"><i class="fa-solid fa-circle-chevron-right text-[10px]"></i> Hiérarchie des Rôles (RBAC)</a></li>
                    <li><a href="#auth" class="text-slate-400 hover:text-white transition-colors text-sm font-medium flex items-center gap-2"><i class="fa-solid fa-circle-chevron-right text-[10px]"></i> Sécurité des Comptes</a></li>
                    <li><a href="#audit" class="text-slate-400 hover:text-white transition-colors text-sm font-medium flex items-center gap-2"><i class="fa-solid fa-circle-chevron-right text-[10px]"></i> Journal d'Audit Système</a></li>
                </ul>
            </div>
            <div class="space-y-4">
                <p class="text-blue-500 font-black text-xs uppercase tracking-widest border-b border-white/10 pb-4">02. Gestion Opérationnelle</p>
                <ul class="space-y-3">
                    <li><a href="#patrimoine" class="text-slate-400 hover:text-white transition-colors text-sm font-medium flex items-center gap-2"><i class="fa-solid fa-circle-chevron-right text-[10px]"></i> Cycle de Vie des Biens</a></li>
                    <li><a href="#locataires" class="text-slate-400 hover:text-white transition-colors text-sm font-medium flex items-center gap-2"><i class="fa-solid fa-circle-chevron-right text-[10px]"></i> Onboarding Locataire</a></li>
                    <li><a href="#contrats" class="text-slate-400 hover:text-white transition-colors text-sm font-medium flex items-center gap-2"><i class="fa-solid fa-circle-chevron-right text-[10px]"></i> Intelligence Contractuelle</a></li>
                </ul>
            </div>
            <div class="space-y-4">
                <p class="text-blue-500 font-black text-xs uppercase tracking-widest border-b border-white/10 pb-4">03. Ingénierie Financière</p>
                <ul class="space-y-3">
                    <li><a href="#paiements" class="text-slate-400 hover:text-white transition-colors text-sm font-medium flex items-center gap-2"><i class="fa-solid fa-circle-chevron-right text-[10px]"></i> Flux d'Encaissement & Quittances</a></li>
                    <li><a href="#impayes" class="text-slate-400 hover:text-white transition-colors text-sm font-medium flex items-center gap-2"><i class="fa-solid fa-circle-chevron-right text-[10px]"></i> Relances &Contentieux</a></li>
                    <li><a href="#bilans" class="text-slate-400 hover:text-white transition-colors text-sm font-medium flex items-center gap-2"><i class="fa-solid fa-circle-chevron-right text-[10px]"></i> Rapports & Bilans Mensuels</a></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="space-y-16">
        
        {{-- Section 1: RBAC --}}
        <section id="roles" class="scroll-mt-24">
            <div class="flex items-center gap-6 mb-12">
                <span class="text-8xl font-black text-slate-100 absolute -mt-16 -ml-8 select-none">01</span>
                <h2 class="text-4xl font-black text-slate-800 relative z-10">La Hiérarchie des Rôles (RBAC)</h2>
            </div>
            <div class="prose prose-slate max-w-none space-y-12">
                <p class="text-xl text-slate-600 leading-relaxed">
                    GESTLOYER utilise un système de contrôle d'accès basé sur les rôles (RBAC) extrêmement granulaire. Chaque utilisateur appartient à une catégorie stricte définissant ses permissions.
                </p>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    <div class="bg-white p-10 rounded-[40px] border border-slate-100 shadow-sm group hover:border-blue-500 transition-all">
                        <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center text-2xl mb-8 group-hover:rotate-6 transition-transform">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <h4 class="text-xl font-black text-slate-800 mb-4">Administrateur (Super-User)</h4>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">Contrôle total sur l'infrastructure. Seul rôle capable de modifier les paramètres de l'agence, de supprimer des données comptables et de gérer les accès des autres membres du personnel.</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[9px] font-black uppercase">Full Access</span>
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[9px] font-black uppercase">Settings Management</span>
                        </div>
                    </div>

                    <div class="bg-white p-10 rounded-[40px] border border-slate-100 shadow-sm group hover:border-emerald-500 transition-all">
                        <div class="w-16 h-16 bg-emerald-600 text-white rounded-2xl flex items-center justify-center text-2xl mb-8 group-hover:rotate-6 transition-transform">
                            <i class="fa-solid fa-building-user"></i>
                        </div>
                        <h4 class="text-xl font-black text-slate-800 mb-4">Propriétaire (Bailleur)</h4>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">Vision limitée à son propre patrimoine. Peut consulter ses revenus, télécharger ses relevés de gestion et communiquer avec ses locataires.</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[9px] font-black uppercase">Dashboard Privé</span>
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[9px] font-black uppercase">Reports Only</span>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-amber-50 rounded-3xl border border-amber-200">
                    <h5 class="font-black text-amber-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation"></i> Règle de Sécurité Critique
                    </h5>
                    <p class="text-sm text-amber-700 leading-relaxed font-medium">
                        Un utilisateur ne peut jamais changer son propre rôle. Cette action doit être effectuée par un autre administrateur pour éviter toute escalade de privilèges. Toutes les modifications de rôles sont consignées dans le journal d'activité.
                    </p>
                </div>
            </div>
        </section>

        {{-- Section 2: Patrimoine --}}
        <section id="patrimoine" class="scroll-mt-24">
            <div class="flex items-center gap-6 mb-12">
                <span class="text-8xl font-black text-slate-100 absolute -mt-16 -ml-8 select-none">02</span>
                <h2 class="text-4xl font-black text-slate-800 relative z-10">Gestion Avancée du Patrimoine</h2>
            </div>
            <div class="space-y-12">
                <div class="bg-white p-12 rounded-[50px] border border-slate-100 shadow-sm">
                    <h3 class="text-2xl font-black text-slate-800 mb-8 tracking-tighter">Le Cycle de Vie d'une Unité Locative</h3>
                    <div class="relative">
                        <div class="absolute left-6 top-0 bottom-0 w-1 bg-slate-100"></div>
                        <div class="space-y-16 relative">
                            <div class="flex gap-10 items-start">
                                <div class="w-12 h-12 rounded-full bg-slate-900 text-white flex items-center justify-center font-black relative z-10 shadow-xl shadow-slate-200">1</div>
                                <div class="flex-1">
                                    <h4 class="text-xl font-black text-slate-800 mb-3">Enregistrement & Association</h4>
                                    <p class="text-slate-500 leading-relaxed">Lors de la création d'un bien, l'association à un <strong>Propriétaire</strong> est obligatoire. C'est cette liaison qui permet au système de savoir vers quel compte reverser les loyers et à qui envoyer les bilans mensuels.</p>
                                </div>
                            </div>
                            <div class="flex gap-10 items-start">
                                <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-black relative z-10 shadow-xl shadow-blue-200">2</div>
                                <div class="flex-1">
                                    <h4 class="text-xl font-black text-slate-800 mb-3">Inventaire Documentaire</h4>
                                    <p class="text-slate-500 leading-relaxed">Utilisez le module <strong>Documents</strong> pour uploader les titres de propriété, les photos haute définition et les plans. Ces documents sont chiffrés sur le serveur et accessibles uniquement par l'admin et le propriétaire concerné.</p>
                                </div>
                            </div>
                            <div class="flex gap-10 items-start">
                                <div class="w-12 h-12 rounded-full bg-emerald-600 text-white flex items-center justify-center font-black relative z-10 shadow-xl shadow-emerald-200">3</div>
                                <div class="flex-1">
                                    <h4 class="text-xl font-black text-slate-800 mb-3">Mise en Location</h4>
                                    <p class="text-slate-500 leading-relaxed">Dès qu'un contrat est validé sur un bien, son statut passe de <span class="text-emerald-600 font-black">Libre</span> à <span class="text-blue-600 font-black">Occupé</span>. Le bien disparaît alors automatiquement des listes de disponibilité pour éviter les doubles locations.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Section 3: Finance --}}
        <section id="paiements" class="scroll-mt-24">
            <div class="flex items-center gap-6 mb-12">
                <span class="text-8xl font-black text-slate-100 absolute -mt-16 -ml-8 select-none">03</span>
                <h2 class="text-4xl font-black text-slate-800 relative z-10">Ingénierie Financière & Flux</h2>
            </div>
            <div class="space-y-16">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="bg-white p-10 rounded-[40px] border-l-8 border-l-blue-600 shadow-sm">
                        <h4 class="font-black text-slate-800 mb-6 uppercase text-xs tracking-widest">Encaissements Automatisés</h4>
                        <p class="text-sm text-slate-500 leading-relaxed mb-6">Lorsqu'un paiement est reçu, l'administrateur doit valider la <strong>Preuve de Paiement</strong> (reçu de virement, capture d'écran Mobile Money). Dès validation :</p>
                        <ul class="space-y-3 text-xs font-bold text-slate-600">
                            <li class="flex items-center gap-2 text-emerald-600"><i class="fa-solid fa-circle-check"></i> La quittance est générée avec numéro unique.</li>
                            <li class="flex items-center gap-2 text-emerald-600"><i class="fa-solid fa-circle-check"></i> Le locataire reçoit une notification par email.</li>
                            <li class="flex items-center gap-2 text-emerald-600"><i class="fa-solid fa-circle-check"></i> Les statistiques du dashboard sont mises à jour.</li>
                        </ul>
                    </div>
                    <div class="bg-white p-10 rounded-[40px] border-l-8 border-l-rose-600 shadow-sm">
                        <h4 class="font-black text-slate-800 mb-6 uppercase text-xs tracking-widest">Le Split Annuel</h4>
                        <p class="text-sm text-slate-500 leading-relaxed mb-6">Si un locataire paie 12 mois d'avance, le système GESTLOYER ne crée pas une seule transaction. Il <strong>ventile</strong> intelligemment le montant sur 12 mensualités distinctes.</p>
                        <div class="p-4 bg-rose-50 rounded-2xl text-rose-800 font-bold text-xs italic">
                            "Cette méthode permet une comptabilité analytique mensuelle précise pour les propriétaires."
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 p-12 rounded-[40px] border border-slate-200">
                    <h4 class="text-xl font-black text-slate-800 mb-8">Comprendre les Statuts de Paiement</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-200 pb-4">
                                    <th class="pb-4">Statut</th>
                                    <th class="pb-4">Signification Technique</th>
                                    <th class="pb-4">Action Requise</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr>
                                    <td class="py-4"><span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-[9px] font-black">EN ATTENTE</span></td>
                                    <td class="py-4 text-xs font-medium text-slate-600">Le locataire a déclaré son paiement mais les fonds n'ont pas encore été vérifiés sur le compte bancaire de l'agence.</td>
                                    <td class="py-4 text-xs font-bold text-blue-600 uppercase">Vérification Manuelle</td>
                                </tr>
                                <tr>
                                    <td class="py-4"><span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[9px] font-black">PAYÉ</span></td>
                                    <td class="py-4 text-xs font-medium text-slate-600">Fonds confirmés. Transaction irréversible. Quittance disponible.</td>
                                    <td class="py-4 text-xs font-bold text-slate-400 uppercase">Aucune</td>
                                </tr>
                                <tr>
                                    <td class="py-4"><span class="px-3 py-1 bg-rose-100 text-rose-700 rounded-full text-[9px] font-black">RETARD / IMPAYÉ</span></td>
                                    <td class="py-4 text-xs font-medium text-slate-600">L'échéance du 5 du mois est dépassée sans action. Le système a bloqué l'accès aux services premium du locataire.</td>
                                    <td class="py-4 text-xs font-bold text-rose-600 uppercase">Relance Immédiate</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        {{-- Section 4: Communication --}}
        <section id="comm" class="scroll-mt-24">
            <div class="flex items-center gap-6 mb-12">
                <span class="text-8xl font-black text-slate-100 absolute -mt-16 -ml-8 select-none">04</span>
                <h2 class="text-4xl font-black text-slate-800 relative z-10">Communication & Intelligence</h2>
            </div>
            <div class="space-y-12">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    <div class="bg-white p-12 rounded-[50px] border border-slate-100 shadow-sm h-full">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-xl"><i class="fa-solid fa-robot"></i></div>
                            <h4 class="text-xl font-black text-slate-800">Filtrage d'Urgence</h4>
                        </div>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">
                            Le système de messagerie analyse les mots-clés dans les messages locataires (ex: "incendie", "inondation", "fuite", "urgent"). 
                            Les messages contenant ces termes sont automatiquement marqués du tag <span class="bg-rose-100 text-rose-600 px-2 py-0.5 rounded font-black text-[10px]">URGENT</span> et remontent en priorité sur votre dashboard.
                        </p>
                    </div>
                    <div class="bg-white p-12 rounded-[50px] border border-slate-100 shadow-sm h-full">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl"><i class="fa-solid fa-bullhorn"></i></div>
                            <h4 class="text-xl font-black text-slate-800">Broadcast (Diffusion)</h4>
                        </div>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">
                            L'administrateur peut envoyer un message à TOUS les locataires en un clic. Utile pour les rappels de loyers globaux ou pour annoncer des travaux dans l'agence. 
                            <strong>Note :</strong> Évitez d'abuser de cette fonction pour ne pas saturer les boîtes mail.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Section 5: Maintenance --}}
        <section id="maintenance" class="scroll-mt-24">
            <div class="flex items-center gap-6 mb-12">
                <span class="text-8xl font-black text-slate-100 absolute -mt-16 -ml-8 select-none">05</span>
                <h2 class="text-4xl font-black text-slate-800 relative z-10">Maintenance & Audit Technique</h2>
            </div>
            <div class="bg-slate-900 rounded-[60px] p-20 text-white relative overflow-hidden">
                <div class="absolute right-0 bottom-0 w-96 h-96 bg-white/5 rounded-full -mr-48 -mb-48"></div>
                <div class="relative z-10 max-w-4xl">
                    <h3 class="text-3xl font-black mb-10">Protocole de Sécurité & Audit</h3>
                    <div class="space-y-10">
                        <div class="flex gap-8">
                            <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 text-2xl text-blue-400"><i class="fa-solid fa-fingerprint"></i></div>
                            <div>
                                <h5 class="text-xl font-bold mb-3">Traçabilité Intégrale (ActivityLogs)</h5>
                                <p class="text-slate-400 leading-relaxed">Chaque action (connexion, modification de prix, validation de paiement, suppression de document) est enregistrée avec l'IP de l'auteur, l'horodatage précis et le détail des modifications (Ancienne vs Nouvelle valeur).</p>
                            </div>
                        </div>
                        <div class="flex gap-8">
                            <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 text-2xl text-emerald-400"><i class="fa-solid fa-recycle"></i></div>
                            <div>
                                <h5 class="text-xl font-bold mb-3">Système de Corbeille (Soft Deletes)</h5>
                                <p class="text-slate-400 leading-relaxed">Aucune donnée critique (Biens, Locataires, Contrats) n'est supprimée définitivement lors du premier clic. Elles sont marquées comme "archivées". Seul l'administrateur peut les restaurer depuis le centre d'archives ou les purger définitivement.</p>
                            </div>
                        </div>
                        <div class="flex gap-8">
                            <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 text-2xl text-amber-400"><i class="fa-solid fa-envelope-open-text"></i></div>
                            <div>
                                <h5 class="text-xl font-bold mb-3">Logs d'Emailing</h5>
                                <p class="text-slate-400 leading-relaxed">Le système conserve une trace de chaque email envoyé (quittances, relances). En cas de contestation d'un locataire, l'administrateur peut prouver l'envoi de la notification.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Section 6: FAQ & Tips --}}
        <section id="tips" class="scroll-mt-24">
            <div class="flex items-center gap-6 mb-12">
                <span class="text-8xl font-black text-slate-100 absolute -mt-16 -ml-8 select-none">06</span>
                <h2 class="text-4xl font-black text-slate-800 relative z-10">Astuces & Dépannage</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                    <h5 class="font-black text-slate-800 mb-4 uppercase text-xs tracking-widest">Le locataire ne voit pas sa quittance ?</h5>
                    <p class="text-sm text-slate-500 leading-relaxed italic">"Vérifiez que le paiement est bien au statut 'PAYÉ'. S'il est 'EN ATTENTE', le PDF n'est pas encore accessible."</p>
                </div>
                <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                    <h5 class="font-black text-slate-800 mb-4 uppercase text-xs tracking-widest">Le Dashboard semble erroné ?</h5>
                    <p class="text-sm text-slate-500 leading-relaxed italic">"Vérifiez l'année sélectionnée en haut du Dashboard. Par défaut, le système affiche l'année en cours."</p>
                </div>
                <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                    <h5 class="font-black text-slate-800 mb-4 uppercase text-xs tracking-widest">Comment modifier le logo de l'agence ?</h5>
                    <p class="text-sm text-slate-500 leading-relaxed italic">"Allez dans 'Paramètres' -> 'Configuration Agence'. Upload de logo recommandé : format PNG transparent, 500x500px."</p>
                </div>
                <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                    <h5 class="font-black text-slate-800 mb-4 uppercase text-xs tracking-widest">Problème d'envoi de mail ?</h5>
                    <p class="text-sm text-slate-500 leading-relaxed italic">"Vérifiez les variables MAIL_MAILER et MAIL_PASSWORD dans le fichier de configuration technique (.env)."</p>
                </div>
            </div>
        </section>

    </div>

    {{-- Final Quote --}}
    <div class="mt-40 text-center">
        <div class="w-24 h-1 bg-slate-200 mx-auto mb-12"></div>
        <p class="text-slate-400 font-black uppercase tracking-[0.4em] text-[10px] mb-4">Documentation Fin de Manuel</p>
        <p class="text-slate-300 italic text-sm">"Un système bien documenté est un système qui dure. Gérez avec rigueur, décidez avec des données."</p>
    </div>

    {{-- Footer --}}
    <div class="mt-20 pt-10 border-t border-slate-100 text-center text-slate-400 text-[10px] font-black uppercase tracking-widest no-print">
        GESTLOYER Pro v2.5 — Document de Propriété Exclusive
    </div>
</div>

<style>
    @font-face {
        font-family: 'Outfit';
        src: url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap');
    }
    body { font-family: 'Outfit', sans-serif; }
    
    @media print {
        @page { margin: 15mm; }
        body { background: white !important; -webkit-print-color-adjust: exact !important; }
        .no-print { display: none !important; }
        .print\:block { display: block !important; }
        .print\:hidden { display: none !important; }
        .bg-slate-900 { background-color: #0f172a !important; color: white !important; }
        .bg-blue-600 { background-color: #2563eb !important; color: white !important; }
        .text-blue-600 { color: #2563eb !important; }
        .bg-slate-50, .bg-blue-50, .bg-emerald-50, .bg-rose-50, .bg-amber-50 { background-color: #f8fafc !important; border: 1px solid #e2e8f0 !important; }
    }
</style>

@endsection
