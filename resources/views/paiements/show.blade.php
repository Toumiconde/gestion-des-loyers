@extends('layouts.app')

@section('title', 'Détails du Paiement — ' . \Carbon\Carbon::parse($paiement->mois_concerne)->format('F Y'))

@section('content')

<div class="max-w-6xl mx-auto py-8">
    
    {{-- Header avec Statut --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 mb-8 flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 rounded-2xl {{ $paiement->statut === 'paye' ? 'bg-emerald-100 text-emerald-600' : ($paiement->statut === 'en_attente' ? 'bg-amber-100 text-amber-600' : 'bg-rose-100 text-rose-600') }} flex items-center justify-center text-3xl">
                <i class="fa-solid fa-money-check-dollar"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-800">Détails du Paiement</h2>
                <p class="text-slate-400 font-bold uppercase tracking-widest text-xs mt-1">
                    Référence : <span class="text-slate-900 font-black">{{ $paiement->reference ?: 'SANS RÉFÉRENCE' }}</span>
                </p>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <span class="px-6 py-2 rounded-full font-black text-xs uppercase tracking-widest 
                {{ $paiement->statut === 'paye' ? 'bg-emerald-50 text-emerald-600' : ($paiement->statut === 'en_attente' ? 'bg-amber-50 text-amber-600 animate-pulse' : 'bg-rose-50 text-rose-600') }}">
                {{ $paiement->statut === 'paye' ? 'Validé & Encaissé' : ($paiement->statut === 'en_attente' ? 'Vérification en cours' : 'Partiel / Erreur') }}
            </span>
            
            @if(in_array(auth()->user()->role, ['admin', 'gestionnaire', 'comptable']) && $paiement->statut === 'en_attente')
                @php
                    $soldeRestant = $paiement->solde_restant ?? 0;
                    $peutValider = $soldeRestant <= 0;
                @endphp
                @if($peutValider)
                    <form action="{{ route('paiements.update', $paiement) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="action" value="valider">
                        <button type="submit" class="px-8 py-3 bg-emerald-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-emerald-700 shadow-xl shadow-emerald-200 transition-all flex items-center gap-2">
                            <i class="fa-solid fa-check-double"></i>
                            {{ str_contains($paiement->notes, '[Paiement Annuel') ? 'Confirmer l\'encaissement Annuel' : 'Confirmer l\'encaissement' }}
                        </button>
                    </form>
                @else
                    <div class="flex flex-col items-end gap-1">
                        <div class="px-6 py-3 bg-amber-100 text-amber-700 rounded-2xl font-black text-xs uppercase tracking-widest flex items-center gap-2 border border-amber-200 cursor-not-allowed opacity-80">
                            <i class="fa-solid fa-hourglass-half animate-pulse"></i>
                            Validation bloquée — Paiement incomplet
                        </div>
                        <p class="text-[10px] text-amber-600 font-bold">
                            Reste à payer : {{ number_format($soldeRestant, 0, ',', ' ') }} GNF avant validation
                        </p>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Colonne Infos --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Détails Style Formulaire Figé --}}
            <div class="bg-white rounded-[40px] p-10 border border-slate-100 shadow-sm relative">
                <div class="absolute top-8 right-10 flex items-center gap-2 px-3 py-1 bg-slate-100 rounded-full border border-slate-200">
                    <i class="fa-solid fa-lock text-slate-400 text-[10px]"></i>
                    <span class="text-[8px] font-black text-slate-500 uppercase tracking-widest">Données Figées</span>
                </div>

                <h3 class="text-lg font-black text-slate-800 mb-10 uppercase tracking-widest text-sm flex items-center gap-3">
                    <i class="fa-solid fa-file-invoice-dollar text-blue-500"></i>
                    Vérification des informations
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2 space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Locataire concerné</label>
                        <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl font-bold text-slate-700 flex justify-between items-center">
                            <span>{{ $paiement->contrat->locataire->prenom }} {{ $paiement->contrat->locataire->nom }}</span>
                            <span class="text-[10px] text-blue-500 bg-blue-50 px-2 py-1 rounded-lg">Contrat : {{ $paiement->contrat->numero_contrat }}</span>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Période payée</label>
                        <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl font-black text-slate-800 flex items-center gap-3">
                            <i class="fa-solid fa-calendar-day text-slate-300"></i>
                            {{ \Carbon\Carbon::parse($paiement->mois_concerne)->locale('fr')->isoFormat('MMMM YYYY') }}
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-emerald-600 uppercase tracking-widest ml-1">Montant versé (ce versement)</label>
                        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-2xl font-black text-emerald-700 text-xl flex justify-between items-center">
                            <span>{{ number_format($paiement->montant, 0, ',', ' ') }}</span>
                            <span class="text-xs">GNF</span>
                        </div>
                        @if($paiement->total_verse && $paiement->total_verse != $paiement->montant)
                        <div class="mt-1 px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-[10px] font-black flex items-center gap-2">
                            <i class="fa-solid fa-sigma"></i>
                            Total cumulé versé : {{ number_format($paiement->total_verse, 0, ',', ' ') }} GNF
                        </div>
                        @endif
                        @if($paiement->loyer_attendu)
                        <div class="mt-1 px-3 py-1 bg-slate-50 text-slate-600 rounded-lg text-[10px] font-black flex items-center gap-2">
                            <i class="fa-solid fa-file-contract"></i>
                            Loyer attendu : {{ number_format($paiement->loyer_attendu, 0, ',', ' ') }} GNF
                        </div>
                        @endif
                        @if($paiement->solde_restant > 0)
                            <div class="mt-2 px-4 py-3 bg-amber-100 text-amber-800 rounded-xl text-sm font-black flex items-center gap-3 border border-amber-200">
                                <i class="fa-solid fa-circle-exclamation text-amber-500 text-lg"></i>
                                <div>
                                    <p>Paiement partiel — Solde restant à payer :</p>
                                    <p class="text-xl mt-0.5">{{ number_format($paiement->solde_restant, 0, ',', ' ') }} <span class="text-xs font-bold">GNF</span></p>
                                </div>
                            </div>
                        @elseif($paiement->solde_restant === 0.0 || $paiement->solde_restant === '0.00')
                            <div class="mt-2 px-4 py-2 bg-emerald-100 text-emerald-800 rounded-xl text-[10px] font-black flex items-center gap-2 border border-emerald-200">
                                <i class="fa-solid fa-check-double text-emerald-600"></i>
                                Loyer du mois intégralement couvert ✅
                            </div>
                        @elseif($paiement->montant < $paiement->contrat->loyer)
                            <div class="mt-2 px-3 py-1 bg-amber-100 text-amber-700 rounded-lg text-[10px] font-black uppercase flex items-center gap-2">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                Attention : Paiement partiel (Loyer prévu: {{ number_format($paiement->contrat->loyer, 0, ',', ' ') }} GNF)
                            </div>
                        @endif
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mode de règlement</label>
                        <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl font-bold text-slate-700 capitalize flex items-center gap-3">
                            <i class="fa-solid fa-wallet text-slate-300"></i>
                            {{ str_replace('_', ' ', $paiement->mode_reglement) }}
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Référence Transaction</label>
                        <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl font-mono font-bold text-blue-600 flex items-center gap-3">
                            <i class="fa-solid fa-hashtag text-slate-300 text-xs"></i>
                            {{ $paiement->reference ?: 'Non spécifiée' }}
                        </div>
                    </div>

                    <div class="md:col-span-2 space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Notes du locataire / Détails internes</label>
                        <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl text-sm text-slate-600 italic leading-relaxed">
                            {{ $paiement->notes ?: 'Aucune note particulière.' }}
                            
                            @if(str_contains($paiement->notes, '[Paiement Annuel'))
                            <div class="mt-4 p-4 bg-purple-50 border border-purple-100 rounded-xl">
                                <p class="text-[10px] font-black text-purple-700 uppercase tracking-widest mb-1">Détail du paiement annuel</p>
                                <p class="text-xs text-purple-600 font-medium">Couvre la période du <b>{{ \Carbon\Carbon::parse($paiement->mois_concerne)->format('d/m/Y') }}</b> au <b>{{ \Carbon\Carbon::parse($paiement->mois_concerne)->addMonths(11)->format('d/m/Y') }}</b>.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Preuve de Paiement --}}
            <div class="bg-white rounded-[40px] p-10 border border-slate-100 shadow-sm">
                <h3 class="text-lg font-black text-slate-800 mb-8 uppercase tracking-widest text-sm flex items-center gap-3">
                    <i class="fa-solid fa-camera text-purple-500"></i>
                    Pièce Justificative (Preuve)
                </h3>
                
                @if($paiement->preuve_paiement)
                    <div class="relative group rounded-3xl overflow-hidden border border-slate-100">
                        @if(Str::endsWith($paiement->preuve_paiement, '.pdf'))
                            <div class="bg-slate-50 p-12 text-center">
                                <i class="fa-solid fa-file-pdf text-6xl text-rose-500 mb-4"></i>
                                <p class="text-sm font-bold text-slate-700">Document PDF - Preuve de paiement</p>
                            </div>
                        @else
                            <img src="{{ Storage::url($paiement->preuve_paiement) }}" class="w-full h-auto max-h-[500px] object-contain bg-slate-50">
                        @endif
                        
                        <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center gap-4">
                            <a href="{{ Storage::url($paiement->preuve_paiement) }}" target="_blank" class="px-6 py-3 bg-white text-slate-900 rounded-xl font-black text-xs uppercase tracking-widest hover:scale-105 transition-all">
                                <i class="fa-solid fa-eye mr-2"></i> Voir en plein écran
                            </a>
                            <a href="{{ Storage::url($paiement->preuve_paiement) }}" download class="px-6 py-3 bg-blue-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:scale-105 transition-all">
                                <i class="fa-solid fa-download mr-2"></i> Télécharger
                            </a>
                        </div>
                    </div>
                @else
                    <div class="p-12 border-2 border-dashed border-slate-100 rounded-[40px] text-center">
                        <i class="fa-solid fa-image-slash text-4xl text-slate-200 mb-4"></i>
                        <p class="text-slate-400 font-bold italic text-sm">Aucune preuve visuelle n'a été jointe à cette déclaration.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Colonne Quittance --}}
        <div>
            <div class="bg-slate-900 rounded-[40px] p-10 text-white shadow-2xl shadow-slate-200 sticky top-8">
                <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest mb-8">Documents Comptables</h3>
                
                @if($paiement->quittance)
                    <div class="space-y-6">
                        <div class="p-6 bg-white/5 rounded-3xl border border-white/10">
                            <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-2">Quittance Officielle</p>
                            <p class="font-black text-xl mb-1">{{ $paiement->quittance->numero_quittance }}</p>
                            <p class="text-xs text-slate-400 font-medium">Générée le {{ $paiement->quittance->created_at->format('d/m/Y') }}</p>
                        </div>
                        
                        <a href="{{ route('quittances.show', $paiement->quittance) }}" class="flex items-center justify-center gap-3 w-full py-4 bg-white text-slate-900 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all shadow-xl">
                            <i class="fa-solid fa-file-pdf"></i> Consulter la quittance
                        </a>
                        <button onclick="printQuittance('{{ route('quittances.show', $paiement->quittance) }}?print=1')" class="flex items-center justify-center gap-3 w-full py-4 bg-slate-800 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-700 transition-all shadow-xl">
                            <i class="fa-solid fa-print"></i> Imprimer le reçu
                        </button>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center text-slate-500 mx-auto mb-6 text-2xl">
                            <i class="fa-solid fa-file-circle-exclamation"></i>
                        </div>
                        <p class="text-slate-400 text-sm font-medium mb-8 leading-relaxed">
                            @if($paiement->statut === 'en_attente')
                                La quittance sera générée automatiquement dès que vous aurez validé l'encaissement de ce paiement.
                            @else
                                Aucune quittance disponible pour ce type de statut.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection