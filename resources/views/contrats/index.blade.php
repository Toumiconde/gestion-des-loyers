@extends('layouts.app')

@section('title', 'Gestion des Engagements')

@section('content')

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Contrats de Bail</h2>
        <p class="text-slate-500 font-medium">Suivi des baux et engagements contractuels</p>
    </div>
    
    <div class="flex gap-3">
        @if(auth()->user()->isAdmin() || auth()->user()->isProprietaire() || auth()->user()->isGestionnaire())
        <a href="{{ route('export.contrats') }}" 
           class="inline-flex items-center justify-center px-6 py-3.5 bg-emerald-50 text-emerald-600 font-black rounded-2xl hover:bg-emerald-600 hover:text-white transition-all active:scale-95 group border border-emerald-100 shadow-sm">
            <i class="fa-solid fa-file-excel mr-2 group-hover:bounce transition-transform"></i>
            Exporter Excel
        </a>
        @endif

        @if(auth()->user()->isAdmin() || auth()->user()->isProprietaire())
        <a href="{{ route('contrats.create') }}" 
           class="inline-flex items-center justify-center px-6 py-3.5 bg-slate-900 text-white font-black rounded-2xl hover:bg-slate-800 shadow-xl shadow-slate-200 transition-all active:scale-95 group">
            <i class="fa-solid fa-file-signature mr-2 group-hover:scale-110 transition-transform"></i>
            Nouveau contrat
        </a>
        @endif
    </div>
</div>

@if(auth()->user()->isAdmin())
<div class="mb-8 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
    <form action="{{ route('contrats.index') }}" method="GET" class="flex flex-wrap items-end gap-6">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Filtrer par mois de signature (Début)</label>
            <select name="month" class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:ring-2 focus:ring-slate-500 outline-none font-bold text-sm">
                <option value="">Tous les mois</option>
                @foreach($months as $num => $name)
                    <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-32">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Année</label>
            <select name="year" class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:ring-2 focus:ring-slate-500 outline-none font-bold text-sm">
                @for($y = 2024; $y <= 2030; $y++)
                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="bg-slate-900 text-white px-6 py-3 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-blue-600 transition-all">
            Appliquer le tri
        </button>
        @if($selectedMonth || $selectedYear != date('Y'))
            <a href="{{ route('contrats.index') }}" class="text-xs font-black text-slate-400 uppercase hover:text-rose-500 transition-colors py-3">Réinitialiser</a>
        @endif
    </form>
</div>
@endif

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase tracking-widest font-black">
                    <th class="px-8 py-5">N° Contrat & Bien</th>
                    <th class="px-8 py-5">Locataire</th>
                    <th class="px-8 py-5">Conditions</th>
                    <th class="px-8 py-5">Statut</th>
                    <th class="px-8 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($contrats as $c)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center">
                                <i class="fa-solid fa-file-contract"></i>
                            </div>
                            <div>
                                <p class="font-black text-slate-800 group-hover:text-blue-600 transition-colors">{{ $c->numero_contrat }}</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $c->bien->libelle }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-[8px] font-black">
                                {{ substr($c->locataire->prenom, 0, 1) }}
                            </div>
                            <span class="text-sm text-slate-600 font-medium">{{ $c->locataire->prenom }} {{ $c->locataire->nom }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <p class="font-black text-slate-800">{{ number_format($c->loyer, 0, ',', ' ') }} <span class="text-[10px] text-slate-400">GNF/mois</span></p>
                        <p class="text-[10px] text-slate-400 italic">Début: {{ $c->date_debut->format('d/m/Y') }}</p>
                    </td>
                    <td class="px-8 py-5">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase {{ $c->statut === 'actif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $c->statut }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('contrats.show', $c) }}" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all shadow-sm" title="Détails">
                                <i class="fa-solid fa-eye text-sm"></i>
                            </a>
                            @if(auth()->user()->isAdmin() || auth()->user()->isProprietaire())
                            <a href="{{ route('contrats.edit', $c) }}" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-amber-500 hover:text-white transition-all shadow-sm" title="Modifier">
                                <i class="fa-solid fa-pen text-sm"></i>
                            </a>
                            <form action="{{ route('contrats.destroy', $c) }}" method="POST" onsubmit="return confirm('Archiver ce contrat ?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-sm" title="Supprimer">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-20 text-center text-slate-400 italic">
                        Aucun contrat enregistré.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection