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
                <h2 class="text-2xl font-black text-slate-800">Paiement de {{ number_format($paiement->montant, 0, ',', ' ') }} GNF</h2>
                <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">Période : {{ \Carbon\Carbon::parse($paiement->mois_concerne)->format('F Y') }}</p>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <span class="px-6 py-2 rounded-full font-black text-xs uppercase tracking-widest 
                {{ $paiement->statut === 'paye' ? 'bg-emerald-50 text-emerald-600' : ($paiement->statut === 'en_attente' ? 'bg-amber-50 text-amber-600 animate-pulse' : 'bg-rose-50 text-rose-600') }}">
                {{ $paiement->statut === 'paye' ? 'Validé & Encaissé' : ($paiement->statut === 'en_attente' ? 'En attente de validation' : 'Partiel / Erreur') }}
            </span>
            
            @if(auth()->user()->role === 'admin' && $paiement->statut === 'en_attente')
                <form action="{{ route('paiements.update', $paiement) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="valider">
                    <button type="submit" class="px-8 py-3 bg-emerald-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-emerald-700 shadow-xl shadow-emerald-200 transition-all">
                        Valider ce paiement
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Colonne Infos --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Détails --}}
            <div class="bg-white rounded-[40px] p-10 border border-slate-100 shadow-sm">
                <h3 class="text-lg font-black text-slate-800 mb-8 uppercase tracking-widest text-sm flex items-center gap-3">
                    <i class="fa-solid fa-circle-info text-blue-500"></i>
                    Informations Transactionnelles
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-12">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Locataire</p>
                        <p class="font-bold text-slate-700 text-lg">{{ $paiement->contrat->locataire->prenom }} {{ $paiement->contrat->locataire->nom }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Bien Immobilier</p>
                        <p class="font-bold text-slate-700 text-lg">{{ $paiement->contrat->bien->libelle }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Date d'encaissement</p>
                        <p class="font-bold text-slate-700">{{ $paiement->date_paiement->format('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Mode de règlement</p>
                        <span class="px-4 py-1 bg-slate-100 rounded-full text-xs font-black text-slate-600 uppercase">{{ $paiement->mode_reglement }}</span>
                    </div>
                    @if($paiement->reference)
                    <div class="md:col-span-2">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Référence / ID Transaction</p>
                        <p class="font-mono font-bold text-blue-600">{{ $paiement->reference }}</p>
                    </div>
                    @endif
                </div>

                @if($paiement->notes)
                <div class="mt-10 p-6 bg-slate-50 rounded-3xl border border-slate-100">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Notes & Observations</p>
                    <p class="text-sm text-slate-600 italic font-medium leading-relaxed">{{ $paiement->notes }}</p>
                </div>
                @endif
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