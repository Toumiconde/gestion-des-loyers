@extends('layouts.app')

@section('title', 'Suivi des Relances')

@section('content')

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Relances de Paiement</h2>
        <p class="text-slate-500 font-medium">Historique des rappels envoyés aux retardataires</p>
    </div>
    
    <a href="{{ route('relances.create') }}" 
       class="inline-flex items-center justify-center px-6 py-3.5 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 shadow-xl shadow-indigo-200 transition-all active:scale-95 group">
        <i class="fa-solid fa-paper-plane mr-2 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
        Nouvelle relance
    </a>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase tracking-widest font-black">
                    <th class="px-8 py-5">Date & Type</th>
                    <th class="px-8 py-5">Locataire</th>
                    <th class="px-8 py-5">Bien</th>
                    <th class="px-8 py-5">Statut</th>
                    <th class="px-8 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($relances as $r)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                @if($r->type == 'email') <i class="fa-solid fa-envelope"></i>
                                @else <i class="fa-solid fa-file-pdf"></i> @endif
                            </div>
                            <div>
                                <p class="font-black text-slate-800">{{ $r->created_at->format('d/m/Y') }}</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $r->type }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <span class="font-bold text-slate-700">{{ $r->contrat->locataire->prenom }} {{ $r->contrat->locataire->nom }}</span>
                    </td>
                    <td class="px-8 py-5 text-sm text-slate-500">
                        {{ $r->contrat->bien->libelle }}
                    </td>
                    <td class="px-8 py-5">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase {{ $r->statut === 'envoye' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $r->statut }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('relances.show', $r) }}" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                <i class="fa-solid fa-eye text-sm"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-20 text-center text-slate-400 italic">
                        Aucune relance envoyée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection