@extends('layouts.app')

@section('title', 'Gestion des Locataires')

@section('content')

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Locataires</h2>
        <p class="text-slate-500 font-medium">Répertoire complet des occupants et prospects</p>
    </div>
    
    <div class="flex gap-3">
        @if(auth()->user()->isAdmin() || auth()->user()->isProprietaire() || auth()->user()->isGestionnaire())
        <a href="{{ route('export.locataires') }}" 
           class="inline-flex items-center justify-center px-6 py-3.5 bg-emerald-50 text-emerald-600 font-black rounded-2xl hover:bg-emerald-600 hover:text-white transition-all active:scale-95 group border border-emerald-100 shadow-sm">
            <i class="fa-solid fa-file-excel mr-2 group-hover:bounce transition-transform"></i>
            Exporter Excel
        </a>
        @endif

        @if(auth()->user()->isAdmin() || auth()->user()->isGestionnaire())
        <a href="{{ route('locataires.create') }}" 
           class="inline-flex items-center justify-center px-6 py-3.5 bg-purple-600 text-white font-black rounded-2xl hover:bg-purple-700 shadow-xl shadow-purple-200 transition-all active:scale-95 group">
            <i class="fa-solid fa-user-plus mr-2 group-hover:rotate-12 transition-transform"></i>
            Nouveau locataire
        </a>
        @endif
    </div>
</div>

@if(auth()->user()->isAdmin())
<div class="mb-8 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
    <form action="{{ route('locataires.index') }}" method="GET" class="flex flex-wrap items-end gap-6">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Filtrer par mois d'inscription</label>
            <select name="month" class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none font-bold text-sm">
                <option value="">Tous les mois</option>
                @foreach($months as $num => $name)
                    <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-32">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Année</label>
            <select name="year" class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none font-bold text-sm">
                @for($y = 2024; $y <= 2030; $y++)
                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="bg-slate-900 text-white px-6 py-3 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-purple-600 transition-all">
            Appliquer le tri
        </button>
        @if($selectedMonth || $selectedYear != date('Y'))
            <a href="{{ route('locataires.index') }}" class="text-xs font-black text-slate-400 uppercase hover:text-rose-500 transition-colors py-3">Réinitialiser</a>
        @endif
    </form>
</div>
@else
    @if($selectedYear != date('Y'))
    <div class="mb-6 bg-amber-50 border-l-4 border-amber-500 p-4 rounded-xl flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-clock-rotate-left text-amber-600"></i>
            <p class="text-amber-800 text-sm font-medium">
                Affichage des archives de l'année <strong>{{ $selectedYear }}</strong>.
            </p>
        </div>
    </div>
    @endif
@endif

@include('partials.password-reset-alert')

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase tracking-widest font-black">
                    <th class="px-8 py-5">Locataire</th>
                    <th class="px-8 py-5">Contact & Adresse</th>
                    <th class="px-8 py-5">Occupation Actuelle</th>
                    <th class="px-8 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($locataires as $l)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center font-black text-lg">
                                {{ substr($l->prenom, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-black text-slate-800">{{ $l->prenom }} {{ $l->nom }}</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $l->piece_identite ?: 'Sans ID' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2 text-sm text-slate-600">
                                <i class="fa-solid fa-envelope text-[10px] text-slate-300"></i>
                                {{ $l->email ?: '—' }}
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-400">
                                <i class="fa-solid fa-phone text-[10px] text-slate-300"></i>
                                {{ $l->telephone ?: '—' }}
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        @if($l->trashed())
                            <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-700 text-[10px] font-black uppercase tracking-widest">Supprimé</span>
                        @else
                            @php $actif = $l->contrats->where('statut', 'actif')->first(); @endphp
                            @if($actif)
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-700">{{ $actif->bien->libelle }}</p>
                                        <p class="text-[10px] text-slate-400">Loyer: {{ number_format($actif->loyer, 0, ',', ' ') }} GNF</p>
                                    </div>
                                </div>
                            @else
                                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-400 text-[10px] font-black uppercase tracking-widest">Sans contrat</span>
                            @endif
                        @endif
                    </td>
                    <td class="px-8 py-5 text-right">
                        <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('locataires.show', $l) }}" class="w-9 h-9 rounded-xl bg-white text-slate-400 border border-slate-100 flex items-center justify-center hover:bg-purple-600 hover:text-white transition-all shadow-sm" title="Voir profil">
                                <i class="fa-solid fa-eye text-sm"></i>
                            </a>
                            @if(auth()->user()->isAdmin() || auth()->user()->isGestionnaire())
                                @if(!$l->trashed() && $l->user)
                                <form action="{{ route('admin.users.reset-password', $l->user) }}" method="POST" onsubmit="return confirm('Réinitialiser le mot de passe de {{ $l->nom_complet }} ?')" class="inline">
                                    @csrf
                                    <button type="submit" class="w-9 h-9 rounded-xl bg-white text-slate-400 border border-slate-100 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-sm" title="Réinitialiser mot de passe">
                                        <i class="fa-solid fa-key text-sm"></i>
                                    </button>
                                </form>
                                @endif
                                @if(!$l->trashed())
                                <a href="{{ route('locataires.edit', $l) }}" class="w-9 h-9 rounded-xl bg-white text-slate-400 border border-slate-100 flex items-center justify-center hover:bg-amber-500 hover:text-white transition-all shadow-sm" title="Modifier">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <form action="{{ route('locataires.destroy', $l) }}" method="POST" onsubmit="return confirm('Archiver ce locataire ?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-9 h-9 rounded-xl bg-rose-50 text-rose-500 border border-rose-100 flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-sm">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-20 text-center text-slate-400 italic">
                        <i class="fa-solid fa-user-slash text-4xl mb-4 block opacity-20"></i>
                        Aucun locataire enregistré.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($locataires->hasPages())
    <div class="px-8 py-4 border-t border-slate-100 bg-slate-50/50">
        {{ $locataires->links() }}
    </div>
    @endif
</div>

@endsection