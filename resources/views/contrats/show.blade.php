@extends('layouts.app')

@section('title', 'Détails du Contrat')

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
                        <a href="{{ route('contrats.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">Contrats</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-slate-300 mx-2 text-xs"></i>
                        <span class="text-slate-600">Contrat {{ $contrat->numero_contrat }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="flex gap-3">
            @if(auth()->user()->isAdmin() || auth()->user()->isProprietaire())
            @if($contrat->statut === 'actif')
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="px-5 py-2.5 bg-rose-50 text-rose-600 font-bold rounded-xl hover:bg-rose-600 hover:text-white transition-all flex items-center gap-2">
                        <i class="fa-solid fa-file-circle-xmark"></i> Résilier le contrat
                    </button>
                    
                    {{-- Modal résiliation --}}
                    <div x-show="open" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
                        <div @click.away="open = false" class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 border border-slate-100">
                            <h3 class="text-2xl font-black text-slate-800 mb-6">Motif de résiliation</h3>
                            <form method="POST" action="{{ route('contrats.destroy', $contrat) }}">
                                @csrf @method('DELETE')
                                <div class="space-y-4 mb-8">
                                    <select name="motif_resiliation" required class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-semibold">
                                        <option value="">-- Choisir un motif --</option>
                                        <option value="depart_volontaire">Départ volontaire</option>
                                        <option value="non_paiement">Non paiement</option>
                                        <option value="fin_bail">Fin de bail</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                </div>
                                <div class="flex gap-4">
                                    <button type="button" @click="open = false" class="flex-1 px-6 py-4 rounded-2xl bg-slate-100 text-slate-600 font-bold">Annuler</button>
                                    <button type="submit" class="flex-1 px-6 py-4 rounded-2xl bg-rose-600 text-white font-bold shadow-lg shadow-rose-200">Confirmer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <a href="{{ route('contrats.edit', $contrat) }}" 
                   class="px-5 py-2.5 bg-amber-50 text-amber-600 font-bold rounded-xl hover:bg-amber-600 hover:text-white transition-all flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square"></i> Modifier
                </a>
            @endif
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Détails --}}
        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="bg-slate-900 p-8 text-white">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $contrat->statut === 'actif' ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }}">
                            {{ $contrat->statut }}
                        </span>
                    </div>
                    <h2 class="text-2xl font-black mb-1">{{ $contrat->numero_contrat }}</h2>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Établi le {{ $contrat->created_at->format('d/m/Y') }}</p>
                </div>
                
                <div class="p-8 space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-building text-slate-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-tighter">Bien Immobilier</p>
                            <a href="{{ route('biens.show', $contrat->bien) }}" class="text-slate-800 font-black hover:text-blue-600">{{ $contrat->bien->libelle }}</a>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-user text-slate-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-tighter">Locataire</p>
                            <a href="{{ route('locataires.show', $contrat->locataire) }}" class="text-slate-800 font-black hover:text-blue-600">
                                {{ $contrat->locataire->prenom }} {{ $contrat->locataire->nom }}
                            </a>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-50">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-slate-400 font-bold uppercase mb-1">Loyer Mensuel</p>
                                <p class="text-lg font-black text-emerald-600">{{ number_format($contrat->loyer, 0, ',', ' ') }} <span class="text-[10px]">GNF</span></p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-bold uppercase mb-1">Garantie</p>
                                <p class="text-lg font-black text-slate-800">{{ number_format($contrat->depot_garantie, 0, ',', ' ') }} <span class="text-[10px]">GNF</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-50 space-y-4">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Date de début</span>
                            <span class="font-bold text-slate-700">{{ $contrat->date_debut->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Date de fin</span>
                            <span class="font-bold text-slate-700">{{ $contrat->date_fin ? $contrat->date_fin->format('d/m/Y') : 'Indéterminée' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Jour d'échéance</span>
                            <span class="font-black text-blue-600">Le {{ $contrat->jour_echeance }} / mois</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Paiements --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-8 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-xl font-black text-slate-800">Historique des Paiements</h3>
                    @if(auth()->user()->isAdmin() || auth()->user()->isGestionnaire())
                    @if($contrat->statut === 'actif')
                    <a href="{{ route('paiements.create', ['contrat_id' => $contrat->id]) }}" class="px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl font-bold text-sm hover:bg-emerald-600 hover:text-white transition-all">
                        Encaisser un loyer
                    </a>
                    @endif
                    @endif
                </div>
                
                <div class="p-0">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-[10px] text-slate-400 font-black uppercase tracking-widest">
                            <tr>
                                <th class="px-8 py-4">Mois</th>
                                <th class="px-8 py-4">Date Paiement</th>
                                <th class="px-8 py-4 text-right">Montant</th>
                                <th class="px-8 py-4 text-center">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($contrat->paiements as $p)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-4">
                                    <p class="font-black text-slate-700">{{ \Carbon\Carbon::parse($p->mois_concerne)->locale('fr')->isoFormat('MMMM YYYY') }}</p>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-widest">{{ $p->mode_reglement }}</p>
                                </td>
                                <td class="px-8 py-4 text-sm text-slate-500">
                                    {{ $p->date_paiement->format('d/m/Y') }}
                                </td>
                                <td class="px-8 py-4 text-right font-black text-slate-800">
                                    {{ number_format($p->montant, 0, ',', ' ') }} GNF
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase 
                                        {{ $p->statut === 'paye' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $p->statut }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-8 py-12 text-center text-slate-400 italic">Aucun paiement encaissé pour ce contrat.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection