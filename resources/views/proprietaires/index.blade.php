@extends('layouts.app')

@section('title', 'Liste des Propriétaires')

@section('content')

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Propriétaires</h2>
        <p class="text-slate-500 font-medium">Gérez les bailleurs du parc immobilier</p>
    </div>
    
    <div class="flex gap-3">
        @if(auth()->user()->isAdmin() || auth()->user()->isGestionnaire())
        <a href="{{ route('export.proprietaires') }}" 
           class="inline-flex items-center justify-center px-6 py-3.5 bg-emerald-50 text-emerald-600 font-black rounded-2xl hover:bg-emerald-600 hover:text-white transition-all active:scale-95 group border border-emerald-100 shadow-sm">
            <i class="fa-solid fa-file-excel mr-2 group-hover:bounce transition-transform"></i>
            Exporter Excel
        </a>
        @endif

        @if(auth()->user()->isAdmin() || auth()->user()->isGestionnaire())
        <a href="{{ route('proprietaires.create') }}" 
           class="inline-flex items-center justify-center px-6 py-3.5 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 shadow-xl shadow-blue-200 transition-all active:scale-95 group">
            <i class="fa-solid fa-plus mr-2 group-hover:rotate-90 transition-transform"></i>
            Nouveau propriétaire
        </a>
        @endif
    </div>
</div>

@if($selectedYear != date('Y'))
<div class="mb-6 bg-amber-50 border-l-4 border-amber-500 p-4 rounded-xl flex items-center justify-between">
    <div class="flex items-center gap-3">
        <i class="fa-solid fa-clock-rotate-left text-amber-600"></i>
        <p class="text-amber-800 text-sm font-medium">
            Affichage des archives de l'année <strong>{{ $selectedYear }}</strong>. Les propriétaires supprimés après cette période sont visibles.
        </p>
    </div>
    <a href="{{ route('dashboard') }}" class="text-xs font-black text-amber-600 uppercase hover:underline">Changer</a>
</div>
@endif

@include('partials.password-reset-alert')

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 text-slate-400 text-xs uppercase tracking-widest font-black">
                    <th class="px-8 py-5">Propriétaire</th>
                    <th class="px-8 py-5">Contact</th>
                    <th class="px-8 py-5">Adresse</th>
                    <th class="px-8 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($proprietaires as $p)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center font-black text-lg">
                                {{ substr($p->user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-black text-slate-800 group-hover:text-blue-600 transition-colors">{{ $p->user->name }}</p>
                                @if($p->trashed())
                                    <span class="px-2 py-0.5 rounded-md bg-rose-100 text-[10px] font-black text-rose-600 uppercase tracking-tighter">Supprimé</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-[10px] font-black text-slate-500 uppercase tracking-tighter">Bailleur</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <p class="text-sm text-slate-700 font-medium">{{ $p->user->email }}</p>
                        <p class="text-xs text-slate-400">{{ $p->telephone ?? '—' }}</p>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-sm text-slate-500 italic">{{ Str::limit($p->adresse, 30) ?: 'Non renseigné' }}</span>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <div class="flex justify-end items-center gap-2">
                            <a href="{{ route('proprietaires.show', $p) }}" 
                               class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Voir détails">
                                <i class="fa-solid fa-eye text-sm"></i>
                            </a>
                            @if(!$p->trashed() && $p->user && (auth()->user()->isAdmin() || auth()->user()->isGestionnaire()))
                            <form action="{{ route('admin.users.reset-password', $p->user) }}" method="POST" onsubmit="return confirm('Réinitialiser le mot de passe de {{ $p->user->name }} ?')" class="inline">
                                @csrf
                                <button type="submit" class="w-10 h-10 rounded-xl bg-white text-slate-400 border border-slate-100 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-sm" title="Reset Pass">
                                    <i class="fa-solid fa-key text-sm"></i>
                                </button>
                            </form>
                            @endif
                            @if(!$p->trashed() && (auth()->user()->isAdmin() || auth()->user()->isGestionnaire()))
                            <a href="{{ route('proprietaires.edit', $p) }}" 
                               class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center hover:bg-amber-600 hover:text-white transition-all shadow-sm" title="Modifier">
                                <i class="fa-solid fa-pen-to-square text-sm"></i>
                            </a>
                            <form method="POST" action="{{ route('proprietaires.destroy', $p) }}" 
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce propriétaire ?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" 
                                        class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-sm" title="Supprimer">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-20 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                <i class="fa-solid fa-user-slash text-slate-200 text-4xl"></i>
                            </div>
                            <p class="text-slate-400 italic">Aucun propriétaire enregistré pour le moment.</p>
                            <a href="{{ route('proprietaires.create') }}" class="mt-4 text-blue-600 font-bold hover:underline">Ajouter le premier</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($proprietaires->hasPages())
    <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/50">
        {{ $proprietaires->links() }}
    </div>
    @endif
</div>

@endsection