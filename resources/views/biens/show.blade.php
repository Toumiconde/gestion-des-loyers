@extends('layouts.app')

@section('title', 'Détails du Bien')

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
                        <a href="{{ route('biens.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">Biens</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-slate-300 mx-2 text-xs"></i>
                        <span class="text-slate-600">{{ $bien->libelle }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        @if(auth()->user()->role !== 'proprietaire')
        <div class="flex gap-3">
            <a href="{{ route('biens.edit', $bien) }}" 
               class="px-5 py-2.5 bg-amber-50 text-amber-600 font-bold rounded-xl hover:bg-amber-600 hover:text-white transition-all flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> Modifier
            </a>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Colonne Gauche : Spécifications --}}
        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="h-64 relative">
                    <img src="{{ $bien->main_photo }}" class="w-full h-full object-cover" alt="{{ $bien->libelle }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
                    <div class="absolute right-4 top-4">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-blue-500/20 text-blue-200 border border-blue-500/30 backdrop-blur-md">
                            {{ $bien->statut }}
                        </span>
                    </div>
                    <div class="absolute bottom-6 left-8 text-white">
                        <h2 class="text-2xl font-black">{{ $bien->libelle }}</h2>
                        <p class="text-blue-200/80 text-sm capitalize">{{ $bien->type }} • {{ $bien->surface }}m²</p>
                    </div>
                </div>
                
                <div class="p-8 space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-location-dot text-slate-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-tighter">Localisation</p>
                            <p class="text-slate-700 font-medium leading-relaxed">{{ $bien->adresse }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-user-tie text-slate-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-tighter">Propriétaire</p>
                            <a href="{{ route('proprietaires.show', $bien->proprietaire) }}" class="text-blue-600 font-black hover:underline">
                                {{ $bien->proprietaire->user->name }}
                            </a>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-50">
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-4">Conditions Financières</p>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500">Loyer de base</span>
                                <span class="font-black text-slate-800">{{ number_format($bien->loyer_base, 0, ',', ' ') }} GNF</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500">Charges</span>
                                <span class="font-black text-slate-800">{{ number_format($bien->charges, 0, ',', ' ') }} GNF</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                                <span class="text-xs font-bold text-emerald-700">Loyer Total</span>
                                <span class="font-black text-emerald-700">{{ number_format($bien->loyer_base + $bien->charges, 0, ',', ' ') }} GNF</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Colonne Droite : Contrats & Gestion --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-8">
                <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div>
                        <h3 class="text-xl font-black text-slate-800">Unités Locatives (Dalles/Étages)</h3>
                        <p class="text-xs text-slate-400 mt-1">Gérez les différents niveaux de ce bâtiment</p>
                    </div>
                    @if(auth()->user()->role !== 'locataire' && auth()->user()->role !== 'proprietaire')
                    <button type="button" onclick="document.getElementById('modalAddUnite').classList.remove('hidden')" class="px-4 py-2 bg-blue-600 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-blue-700 transition-all flex items-center gap-2 shadow-lg shadow-blue-500/20">
                        <i class="fa-solid fa-plus"></i> Ajouter une Unité
                    </button>
                    @endif
                </div>
                
                <div class="p-0">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-[10px] text-slate-400 font-black uppercase tracking-widest">
                            <tr>
                                <th class="px-8 py-4">Désignation</th>
                                <th class="px-8 py-4">Niveau</th>
                                <th class="px-8 py-4">Chambres</th>
                                <th class="px-8 py-4">Loyer</th>
                                <th class="px-8 py-4 text-center">Statut</th>
                                @if(auth()->user()->role !== 'locataire' && auth()->user()->role !== 'proprietaire')
                                <th class="px-8 py-4 text-right">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($bien->unitesLocatives as $unite)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-4 font-bold text-slate-700">{{ $unite->libelle }}</td>
                                <td class="px-8 py-4 text-slate-500 text-sm">Étage {{ $unite->niveau }}</td>
                                <td class="px-8 py-4 text-slate-500 text-sm">{{ $unite->nombre_chambres }} Ch.</td>
                                <td class="px-8 py-4 font-black text-slate-800 text-sm">{{ number_format($unite->prix_loyer, 0, ',', ' ') }} GNF</td>
                                <td class="px-8 py-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase 
                                        {{ $unite->statut === 'libre' ? 'bg-emerald-100 text-emerald-700' : ($unite->statut === 'reserve' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500') }}">
                                        {{ $unite->statut }}
                                    </span>
                                </td>
                                @if(auth()->user()->role !== 'locataire' && auth()->user()->role !== 'proprietaire')
                                <td class="px-8 py-4 text-right flex justify-end gap-2">
                                    <form action="{{ route('unites-locatives.destroy', $unite) }}" method="POST" onsubmit="return confirm('Supprimer cette unité ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-8 py-12 text-center text-slate-400 italic">
                                    Aucune unité définie. Ce bien est considéré comme une seule entité.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Ajout Unité -->
            <div id="modalAddUnite" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-200">
                    <div class="p-8 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-xl font-black text-slate-800">Ajouter une Unité</h3>
                        <button onclick="document.getElementById('modalAddUnite').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                            <i class="fa-solid fa-times text-xl"></i>
                        </button>
                    </div>
                    <form action="{{ route('unites-locatives.store') }}" method="POST" class="p-8 space-y-5">
                        @csrf
                        <input type="hidden" name="bien_id" value="{{ $bien->id }}">
                        
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Libellé (ex: Dalle 1, Appt A...)</label>
                            <input type="text" name="libelle" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-blue-500 transition-all font-semibold">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Niveau (Étage)</label>
                                <input type="number" name="niveau" value="0" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-blue-500 transition-all font-semibold">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Nombre de chambres</label>
                                <input type="number" name="nombre_chambres" value="1" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-blue-500 transition-all font-semibold">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Loyer de l'unité (GNF)</label>
                            <input type="number" name="prix_loyer" value="{{ $bien->loyer_base }}" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-blue-500 transition-all font-semibold">
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full py-5 bg-blue-600 text-white font-black uppercase tracking-widest rounded-2xl hover:bg-blue-700 shadow-xl shadow-blue-500/20 transition-all">
                                Enregistrer l'unité
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-8 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-xl font-black text-slate-800">Historique des Contrats</h3>
                    @if($bien->statut === 'disponible')
                    <a href="{{ route('contrats.create', ['bien_id' => $bien->id]) }}" class="text-blue-600 font-black text-sm hover:underline flex items-center gap-2">
                        <i class="fa-solid fa-plus-circle"></i> Nouveau Contrat
                    </a>
                    @endif
                </div>
                
                <div class="p-0">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-[10px] text-slate-400 font-black uppercase tracking-widest">
                            <tr>
                                <th class="px-8 py-4">N° Contrat</th>
                                <th class="px-8 py-4">Locataire</th>
                                <th class="px-8 py-4">Période</th>
                                <th class="px-8 py-4 text-center">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($bien->contrats as $contrat)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-4">
                                    <a href="{{ route('contrats.show', $contrat) }}" class="font-black text-blue-600 hover:underline">
                                        {{ $contrat->numero_contrat }}
                                    </a>
                                </td>
                                <td class="px-8 py-4">
                                    <p class="font-bold text-slate-700">{{ $contrat->locataire->prenom }} {{ $contrat->locataire->nom }}</p>
                                </td>
                                <td class="px-8 py-4 text-xs text-slate-500">
                                    Du {{ $contrat->date_debut->format('d/m/Y') }}
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase 
                                        {{ $contrat->statut === 'actif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $contrat->statut }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-8 py-12 text-center text-slate-400 italic">
                                    Aucun contrat enregistré pour ce bien.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Statistiques Rapides --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm flex items-center gap-6">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl">
                        <i class="fa-solid fa-vault"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Garantie Détenue</p>
                        <h4 class="text-xl font-black text-slate-800">{{ number_format($bien->depot_garantie, 0, ',', ' ') }} GNF</h4>
                    </div>
                </div>
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm flex items-center gap-6">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Total Revenus</p>
                        <h4 class="text-xl font-black text-slate-800">{{ number_format($bien->contrats->flatMap->paiements->sum('montant'), 0, ',', ' ') }} GNF</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection