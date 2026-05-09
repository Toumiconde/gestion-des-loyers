@extends('layouts.app')

@section('title', 'Détails de l\'Incident & Chantier')

@section('content')

<div class="max-w-6xl mx-auto py-8">
    {{-- Fil d'ariane --}}
    <div class="flex items-center justify-between mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm font-medium">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-blue-600 transition-colors">
                        <i class="fa-solid fa-house mr-2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-slate-300 mx-2 text-xs"></i>
                        <a href="{{ route('incidents.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">Incidents</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-slate-300 mx-2 text-xs"></i>
                        <span class="text-slate-600">{{ $incident->titre }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="flex gap-3">
            @if(!in_array(auth()->user()->role, ['proprietaire', 'locataire']))
            <a href="{{ route('incidents.edit', $incident) }}" 
               class="px-5 py-2.5 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 flex items-center gap-2">
                <i class="fa-solid fa-screwdriver-wrench"></i> Gérer le chantier
            </a>
            @endif
        </div>
    </div>

    {{-- BARRE DE PROGRESSION DU WORKFLOW --}}
    <div class="mb-10 bg-white rounded-[30px] p-8 border border-slate-100 shadow-sm overflow-hidden relative">
        <div class="flex items-center justify-between relative z-10">
            @php
                $steps = [
                    'ouvert' => ['label' => 'Signalé', 'icon' => 'fa-bell'],
                    'en_devis' => ['label' => 'Devis', 'icon' => 'fa-file-invoice'],
                    'en_travaux' => ['label' => 'Travaux', 'icon' => 'fa-hammer'],
                    'resolu' => ['label' => 'Résolu', 'icon' => 'fa-check-double'],
                    'paye' => ['label' => 'Payé & Clos', 'icon' => 'fa-receipt']
                ];
                $reached = true;
            @endphp

            @foreach($steps as $key => $step)
                <div class="flex flex-col items-center gap-3 flex-1 relative">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-lg transition-all duration-500 {{ $incident->statut === $key ? 'bg-blue-600 text-white shadow-xl shadow-blue-200 scale-125 z-20' : ($reached ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-300') }}">
                        <i class="fa-solid {{ $step['icon'] }}"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest {{ $incident->statut === $key ? 'text-blue-600' : 'text-slate-400' }}">{{ $step['label'] }}</span>
                    
                    @if($incident->statut === $key) @php $reached = false; @endphp @endif
                </div>
            @endforeach
        </div>
        {{-- Ligne de fond --}}
        <div class="absolute top-[52px] left-[10%] right-[10%] h-1 bg-slate-100 rounded-full z-0"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Informations Techniques & Coûts --}}
        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-6 border-b border-slate-50 pb-4 flex items-center justify-between">
                    Budget & Coûts
                    <i class="fa-solid fa-coins text-amber-500"></i>
                </h3>
                <div class="space-y-6">
                    <div class="p-4 bg-slate-50 rounded-2xl">
                        <p class="text-[10px] text-slate-400 font-black uppercase mb-1">Estimation</p>
                        <p class="text-xl font-black text-slate-800">{{ number_format($incident->cout_estime ?: 0, 0, ',', ' ') }} GNF</p>
                    </div>
                    <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                        <p class="text-[10px] text-emerald-600 font-black uppercase mb-1">Coût Réel (Facturé)</p>
                        <p class="text-2xl font-black text-emerald-700">{{ number_format($incident->cout_reel ?: 0, 0, ',', ' ') }} GNF</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-6 border-b border-slate-50 pb-4">Intervenant & Devis</h3>
                
                @if($incident->maintenancier_id)
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-user-gear"></i>
                        </div>
                        <div>
                            @if(in_array(auth()->user()->role, ['admin', 'gestionnaire', 'comptable']))
                                <p class="font-black text-slate-800">{{ $incident->maintenancier->nom }}</p>
                                <p class="text-xs text-slate-500">{{ $incident->maintenancier->specialite }}</p>
                            @else
                                <p class="font-black text-slate-800">Prestataire Agréé</p>
                                <p class="text-xs text-slate-500">Assigné par l'agence</p>
                            @endif
                        </div>
                    </div>
                    
                    @if(in_array(auth()->user()->role, ['admin', 'gestionnaire', 'comptable']) && $incident->maintenancier->telephone)
                    <a href="tel:{{ $incident->maintenancier->telephone }}" class="w-full flex items-center justify-center gap-3 py-3 bg-slate-900 text-white rounded-xl font-black text-xs hover:bg-blue-600 transition-all">
                        <i class="fa-solid fa-phone"></i> {{ $incident->maintenancier->telephone }}
                    </a>
                    @endif

                    <div class="mt-4 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <p class="text-[10px] text-slate-400 font-black uppercase mb-1">Montant du devis</p>
                        <p class="text-xl font-black text-slate-800">{{ number_format($incident->devis_montant, 0, ',', ' ') }} GNF</p>
                        @if($incident->devis_note)
                            <p class="text-xs text-slate-500 mt-2 italic">{{ $incident->devis_note }}</p>
                        @endif
                        
                        <div class="mt-4">
                            @if($incident->devis_statut === 'en_attente')
                                <span class="px-2 py-1 rounded-md text-[10px] font-black uppercase bg-slate-200 text-slate-600">Devis en attente d'envoi</span>
                            @elseif($incident->devis_statut === 'envoye_proprio')
                                <span class="px-2 py-1 rounded-md text-[10px] font-black uppercase bg-purple-100 text-purple-700">Envoyé au propriétaire</span>
                            @elseif($incident->devis_statut === 'accepte')
                                <span class="px-2 py-1 rounded-md text-[10px] font-black uppercase bg-emerald-100 text-emerald-700">Devis Accepté</span>
                            @elseif($incident->devis_statut === 'refuse')
                                <span class="px-2 py-1 rounded-md text-[10px] font-black uppercase bg-rose-100 text-rose-700">Devis Refusé</span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions Gestionnaire : Envoyer le devis --}}
                    @if(in_array(auth()->user()->role, ['admin', 'gestionnaire']) && $incident->devis_statut === 'en_attente')
                    <form action="{{ route('incidents.envoyerDevis', $incident) }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-purple-600 text-white font-black rounded-xl text-xs uppercase hover:bg-purple-700 transition-all shadow-lg shadow-purple-200 flex justify-center items-center gap-2">
                            <i class="fa-solid fa-paper-plane"></i> Envoyer au propriétaire
                        </button>
                    </form>
                    @endif
                </div>
                @else
                
                {{-- Formulaire Assignation Maintenancier (Admin/Gestionnaire) --}}
                @if(in_array(auth()->user()->role, ['admin', 'gestionnaire']))
                    <form action="{{ route('incidents.assignerMaintenancier', $incident) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Maintenancier</label>
                            <select name="maintenancier_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                                <option value="">Sélectionner un prestataire...</option>
                                @foreach($maintenanciers as $m)
                                    <option value="{{ $m->id }}">{{ $m->nom }} ({{ $m->specialite }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Montant du devis (GNF)</label>
                            <input type="number" name="devis_montant" required min="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-blue-500 font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Notes du devis (Optionnel)</label>
                            <textarea name="devis_note" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <button type="submit" class="w-full py-3 bg-blue-600 text-white font-black rounded-xl text-xs uppercase hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                            Enregistrer & Assigner
                        </button>
                    </form>
                @else
                    <p class="text-xs text-slate-400 italic">Aucun technicien assigné pour le moment.</p>
                @endif
                
                @endif
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-6 border-b border-slate-50 pb-4">Localisation</h3>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-building text-slate-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase">Bien concerné</p>
                            <p class="text-slate-800 font-bold">{{ $incident->contrat->bien->libelle }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-user-tag text-slate-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase">Locataire actuel</p>
                            <p class="text-slate-800 font-bold">{{ $incident->contrat->locataire->prenom }} {{ $incident->contrat->locataire->nom }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Description & Historique --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Validation Devis (Propriétaire) --}}
            @if(auth()->user()->isProprietaire() && $incident->devis_statut === 'envoye_proprio')
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-[30px] shadow-lg p-10 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-xl backdrop-blur-sm">
                            <i class="fa-solid fa-file-invoice-dollar text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black">Validation requise</h3>
                            <p class="text-purple-100 text-sm">Le gestionnaire a soumis un devis pour ces travaux.</p>
                        </div>
                    </div>
                    
                    <div class="bg-white/10 rounded-2xl p-6 backdrop-blur-md mb-8 border border-white/20">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-purple-100 uppercase tracking-widest text-[10px] font-black">Montant du Devis</span>
                            <span class="text-2xl font-black">{{ number_format($incident->devis_montant, 0, ',', ' ') }} GNF</span>
                        </div>
                        @if($incident->devis_note)
                            <p class="text-sm text-purple-50 italic">" {{ $incident->devis_note }} "</p>
                        @endif
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <form action="{{ route('incidents.accepterDevis', $incident) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-4 bg-emerald-500 hover:bg-emerald-400 text-white font-black rounded-xl transition-all shadow-lg shadow-emerald-500/30 flex justify-center items-center gap-2">
                                <i class="fa-solid fa-check"></i> Accepter & Démarrer les travaux
                            </button>
                        </form>
                        
                        <div x-data="{ openRefus: false }" class="flex-1">
                            <button @click="openRefus = !openRefus" type="button" class="w-full py-4 bg-rose-500 hover:bg-rose-400 text-white font-black rounded-xl transition-all shadow-lg shadow-rose-500/30 flex justify-center items-center gap-2">
                                <i class="fa-solid fa-xmark"></i> Refuser le devis
                            </button>
                            
                            <div x-show="openRefus" style="display: none;" class="mt-4 bg-white rounded-2xl p-4 text-slate-800 shadow-xl relative z-50">
                                <form action="{{ route('incidents.refuserDevis', $incident) }}" method="POST">
                                    @csrf
                                    <label class="block text-xs font-bold text-slate-500 mb-2">Motif du refus :</label>
                                    <textarea name="refus_note" required rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-rose-500 mb-3" placeholder="Ex: Trop cher, demandez un autre prestataire..."></textarea>
                                    <button type="submit" class="w-full py-3 bg-rose-600 text-white font-black rounded-xl text-xs uppercase hover:bg-rose-700 transition-all">Confirmer le refus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-10">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-2xl font-black text-slate-800">{{ $incident->titre }}</h3>
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $incident->priorite === 'urgent' ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-500' }}">
                        {{ $incident->priorite }}
                    </span>
                </div>
                
                <div class="p-8 bg-slate-50 rounded-[30px] border border-slate-100 text-slate-600 leading-relaxed italic mb-10">
                    " {{ $incident->description }} "
                </div>

                @if($incident->devis_statut === 'refuse' && $incident->refus_note)
                <div class="mb-10 p-6 bg-rose-50 rounded-2xl border border-rose-100 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black text-rose-800 uppercase tracking-widest mb-1">Devis refusé par le propriétaire</p>
                        <p class="text-rose-700 text-sm">" {{ $incident->refus_note }} "</p>
                        @if(in_array(auth()->user()->role, ['admin', 'gestionnaire']))
                            <p class="text-xs text-rose-600 mt-2 font-medium">Veuillez assigner un nouveau maintenancier ou ajuster le devis.</p>
                        @endif
                    </div>
                </div>
                @endif

                @if($incident->photo_incident)
                <div class="mb-10">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 italic">Photo de l'incident / Reçu</h3>
                    <div class="relative group rounded-[30px] overflow-hidden border border-slate-200">
                        <img src="{{ asset('storage/' . $incident->photo_incident) }}" class="w-full h-auto object-cover max-h-[500px]" alt="Photo Incident">
                        <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <a href="{{ asset('storage/' . $incident->photo_incident) }}" target="_blank" class="px-6 py-3 bg-white text-slate-900 font-black rounded-2xl hover:bg-blue-600 hover:text-white transition-all flex items-center gap-2">
                                <i class="fa-solid fa-download"></i> Voir / Télécharger
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                @if($incident->statut === 'paye')
                <div class="p-6 bg-blue-50 rounded-2xl border border-blue-100 flex items-center gap-6">
                    <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center text-xl shadow-lg shadow-blue-100">
                        <i class="fa-solid fa-check-double"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black text-blue-800 uppercase tracking-widest">Dossier Clos</p>
                        <p class="text-blue-700 font-medium">Cet incident a été résolu et la dépense a été automatiquement comptabilisée dans votre bilan financier.</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Support Technique --}}
            <div class="bg-slate-900 rounded-[40px] p-12 text-white relative overflow-hidden group">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-blue-600/10 rounded-full group-hover:scale-110 transition-transform duration-700"></div>
                <div class="relative z-10">
                    <h3 class="text-2xl font-black mb-4">Assistance IA & Maintenance</h3>
                    <p class="text-slate-400 text-sm leading-relaxed max-w-md mb-8">Utilisez notre outil d'assistance pour trouver des artisans certifiés à Conakry ou pour estimer le coût moyen de cette réparation.</p>
                    <a href="{{ route('maintenance.index') }}" class="inline-flex items-center gap-3 px-8 py-4 bg-white text-slate-900 font-black rounded-2xl hover:bg-blue-50 transition-all shadow-xl shadow-white/5">
                        Consulter l'IA Maintenance
                        <i class="fa-solid fa-brain text-blue-600"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection