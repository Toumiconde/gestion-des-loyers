@extends('layouts.app')

@section('title', 'Détails Propriétaire')

@section('content')

@include('partials.password-reset-alert')

<div class="max-w-5xl mx-auto py-8">
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
                        <a href="{{ route('proprietaires.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">Propriétaires</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-slate-300 mx-2 text-xs"></i>
                        <span class="text-slate-600">Détails</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="flex gap-3">
            @if(auth()->user()->isAdmin() && $proprietaire->user)
            <form action="{{ route('admin.users.reset-password', $proprietaire->user) }}" method="POST"
                  onsubmit="return confirm('Réinitialiser le mot de passe de {{ $proprietaire->user->name }} ?')">
                @csrf
                <button type="submit"
                        class="px-5 py-2.5 bg-indigo-50 text-indigo-600 font-bold rounded-xl hover:bg-indigo-600 hover:text-white transition-all flex items-center gap-2">
                    <i class="fa-solid fa-key"></i> Reset Pass
                </button>
            </form>
            @endif
            <a href="{{ route('proprietaires.edit', $proprietaire) }}" 
               class="px-5 py-2.5 bg-amber-50 text-amber-600 font-bold rounded-xl hover:bg-amber-600 hover:text-white transition-all flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> Modifier
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Colonne Gauche : Profil --}}
        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 text-center">
                <div class="w-24 h-24 rounded-3xl bg-blue-600 text-white flex items-center justify-center text-4xl font-black mx-auto mb-6 shadow-xl shadow-blue-200">
                    {{ substr($proprietaire->user->name, 0, 1) }}
                </div>
                <h2 class="text-2xl font-black text-slate-800">{{ $proprietaire->user->name }}</h2>
                <p class="text-slate-400 font-medium mb-6">{{ $proprietaire->user->email }}</p>
                
                <div class="flex justify-center gap-2">
                    <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest">Propriétaire</span>
                    <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest">Vérifié</span>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-6 border-b border-slate-50 pb-4">Coordonnées</h3>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-phone text-slate-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase">Téléphone</p>
                            <p class="text-slate-700 font-medium">{{ $proprietaire->telephone ?: 'Non renseigné' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-location-dot text-slate-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase">Adresse</p>
                            <p class="text-slate-700 font-medium leading-relaxed">{{ $proprietaire->adresse ?: 'Non renseigné' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-building-columns text-slate-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase">RIB Bancaire</p>
                            <p class="text-slate-700 font-mono text-sm">{{ $proprietaire->rib_bancaire ?: 'Non renseigné' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Colonne Droite : Biens & Activité --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Ses Biens --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
                    <h3 class="text-xl font-black text-slate-800">Parc Immobilier</h3>
                    <span class="px-3 py-1 rounded-full bg-blue-600 text-white text-[10px] font-black uppercase">{{ $proprietaire->biens->count() }} Biens</span>
                </div>
                <div class="p-0">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50/50 text-[10px] text-slate-400 font-black uppercase tracking-widest">
                            <tr>
                                <th class="px-8 py-4">Bien</th>
                                <th class="px-8 py-4">Type</th>
                                <th class="px-8 py-4 text-right">Loyer Base</th>
                                <th class="px-8 py-4 text-center">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($proprietaire->biens as $bien)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-8 py-4 font-bold text-slate-700">{{ $bien->libelle }}</td>
                                <td class="px-8 py-4 text-slate-500 text-sm">{{ $bien->type }}</td>
                                <td class="px-8 py-4 text-right font-black text-slate-800">{{ number_format($bien->loyer_base, 0, ',', ' ') }} GNF</td>
                                <td class="px-8 py-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase 
                                        {{ $bien->statut === 'disponible' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $bien->statut }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-8 py-12 text-center text-slate-400 italic">Aucun bien associé à ce propriétaire.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Résumé Financier Rapide --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-[#02132D] rounded-3xl p-8 text-white">
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-2">Total Revenus Générés</p>
                    <h4 class="text-2xl font-black">{{ number_format($proprietaire->biens->flatMap->contrats->flatMap->paiements->sum('montant'), 0, ',', ' ') }} GNF</h4>
                </div>
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-2">Contrats Actifs</p>
                    <h4 class="text-2xl font-black text-slate-800">{{ $proprietaire->biens->flatMap->contrats->where('statut', 'actif')->count() }}</h4>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
