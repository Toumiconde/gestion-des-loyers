@extends('layouts.app')

@section('title', 'Gestion du Parc Immobilier')

@section('content')

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Parc Immobilier</h2>
        <p class="text-slate-500 font-medium">Gérez et suivez l'état de vos propriétés</p>
    </div>
    
    <div class="flex gap-3">
        @if(auth()->user()->isAdmin() || auth()->user()->isProprietaire() || auth()->user()->isGestionnaire())
        <a href="{{ route('export.biens') }}" 
           class="inline-flex items-center justify-center px-6 py-3.5 bg-emerald-50 text-emerald-600 font-black rounded-2xl hover:bg-emerald-600 hover:text-white transition-all active:scale-95 group border border-emerald-100 shadow-sm">
            <i class="fa-solid fa-file-excel mr-2 group-hover:bounce transition-transform"></i>
            Exporter Excel
        </a>
        @endif

        @if(auth()->user()->role !== 'proprietaire')
        <a href="{{ route('biens.create') }}" 
           class="inline-flex items-center justify-center px-6 py-3.5 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 shadow-xl shadow-blue-200 transition-all active:scale-95 group">
            <i class="fa-solid fa-house-medical mr-2 group-hover:scale-110 transition-transform"></i>
            Ajouter un bien
        </a>
        @endif
    </div>
</div>

@if(auth()->user()->isAdmin())
<div class="mb-8 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
    <form action="{{ route('biens.index') }}" method="GET" class="flex flex-wrap items-end gap-6">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Filtrer par mois d'ajout</label>
            <select name="month" class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-sm">
                <option value="">Tous les mois</option>
                @foreach($months as $num => $name)
                    <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-32">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Année</label>
            <select name="year" class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-sm">
                @for($y = 2024; $y <= 2030; $y++)
                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="bg-slate-900 text-white px-6 py-3 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-blue-600 transition-all">
            Appliquer le tri
        </button>
        @if($selectedMonth || $selectedYear != date('Y'))
            <a href="{{ route('biens.index') }}" class="text-xs font-black text-slate-400 uppercase hover:text-rose-500 transition-colors py-3">Réinitialiser</a>
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

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase tracking-widest font-black">
                    <th class="px-8 py-5">Bien & Type</th>
                    <th class="px-8 py-5">Propriétaire</th>
                    <th class="px-8 py-5">Loyer Base</th>
                    <th class="px-8 py-5">État</th>
                    <th class="px-8 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($biens as $bien)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl overflow-hidden shadow-sm">
                                <img src="{{ $bien->main_photo }}" class="w-full h-full object-cover" alt="{{ $bien->libelle }}">
                            </div>
                            <div>
                                <p class="font-black text-slate-800">{{ $bien->libelle }}</p>
                                <p class="text-xs text-slate-400 capitalize">{{ $bien->type }} • {{ $bien->surface }}m²</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-[10px] font-black">
                                {{ substr($bien->proprietaire->user->name, 0, 1) }}
                            </div>
                            <span class="text-sm text-slate-600 font-medium">{{ $bien->proprietaire->user->name }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <p class="font-black text-slate-800">{{ number_format($bien->loyer_base, 0, ',', ' ') }} <span class="text-[10px] text-slate-400 uppercase">GNF</span></p>
                    </td>
                    <td class="px-8 py-5">
                        @if($bien->trashed())
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-rose-100 text-rose-700">
                                Supprimé
                            </span>
                        @else
                            @php
                                $statusClasses = [
                                    'disponible' => 'bg-emerald-100 text-emerald-700',
                                    'occupe' => 'bg-blue-100 text-blue-700',
                                    'maintenance' => 'bg-amber-100 text-amber-700',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase {{ $statusClasses[$bien->statut] ?? 'bg-slate-100 text-slate-700' }}">
                                {{ $bien->statut }}
                            </span>
                        @endif
                    </td>
                    <td class="px-8 py-5 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('biens.show', $bien) }}" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                <i class="fa-solid fa-eye text-sm"></i>
                            </a>
                            @if(auth()->user()->role !== 'proprietaire' && !$bien->trashed())
                            <a href="{{ route('biens.edit', $bien) }}" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-amber-500 hover:text-white transition-all shadow-sm">
                                <i class="fa-solid fa-pen text-sm"></i>
                            </a>
                            <form action="{{ route('biens.destroy', $bien) }}" method="POST" onsubmit="return confirm('Archiver ce bien ?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-sm">
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
                        <i class="fa-solid fa-house-circle-xmark text-4xl mb-4 block opacity-20"></i>
                        Aucun bien trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($biens->hasPages())
    <div class="px-8 py-4 border-t border-slate-100 bg-slate-50/50">
        {{ $biens->links() }}
    </div>
    @endif
</div>

@endsection