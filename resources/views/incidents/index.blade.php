@extends('layouts.app')

@section('title', 'Gestion des Incidents & Maintenance')

@section('content')

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Assistance & Suivi</h2>
        <p class="text-slate-500 font-medium">Gestion unifiée des incidents locatifs et de la maintenance applicative</p>
    </div>
    
    <div class="flex items-center gap-3">
        @if(auth()->user()->role === 'proprietaire')
        <a href="{{ route('maintenance.index') }}" 
           class="inline-flex items-center justify-center px-6 py-3.5 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 shadow-xl shadow-indigo-200 transition-all active:scale-95 group">
            <i class="fa-solid fa-robot mr-2 group-hover:animate-bounce transition-transform"></i>
            Besoin d'aide maintenance ?
        </a>
        @endif

        @if(auth()->user()->role !== 'proprietaire')
        <a href="{{ route('incidents.create') }}" 
           class="inline-flex items-center justify-center px-6 py-3.5 bg-amber-500 text-white font-black rounded-2xl hover:bg-amber-600 shadow-xl shadow-amber-200 transition-all active:scale-95 group">
            <i class="fa-solid fa-triangle-exclamation mr-2 group-hover:rotate-12 transition-transform"></i>
            Signaler un incident
        </a>
        @endif
    </div>
</div>

<div x-data="{ tab: 'incidents' }" class="space-y-6">
    {{-- Système d'onglets pour Admin, Proprio et Staff --}}
    @if(in_array(auth()->user()->role, ['admin', 'proprietaire', 'gestionnaire', 'comptable']))
    <div class="flex items-center gap-2 p-1.5 bg-slate-200/50 rounded-2xl w-fit border border-slate-200">
        <button @click="tab = 'incidents'" 
                :class="tab === 'incidents' ? 'bg-white shadow-md text-slate-900' : 'text-slate-500 hover:text-slate-700'"
                class="px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest transition-all flex items-center gap-2">
            <i class="fa-solid fa-house-crack"></i>
            Incidents Locatifs
        </button>
        <button @click="tab = 'maintenance'" 
                :class="tab === 'maintenance' ? 'bg-indigo-600 shadow-md text-white' : 'text-slate-500 hover:text-slate-700'"
                class="px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest transition-all flex items-center gap-2">
            <i class="fa-solid fa-robot"></i>
            Maintenance IA ({{ $maintenanceRequests->count() }})
        </button>
    </div>
    @endif

    {{-- LISTE DES INCIDENTS --}}
    <div x-show="tab === 'incidents'" class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase tracking-widest font-black">
                        <th class="px-8 py-5">Incident</th>
                        <th class="px-8 py-5">Localisation</th>
                        <th class="px-8 py-5">Priorité</th>
                        <th class="px-8 py-5">Statut</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($incidents as $i)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="font-black text-slate-800">{{ $i->titre }}</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Signalé le {{ $i->created_at->format('d/m/Y') }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <p class="text-sm font-bold text-slate-700">{{ $i->contrat->bien->libelle }}</p>
                            <p class="text-[10px] text-slate-400">Locataire: {{ $i->contrat->locataire->nom }}</p>
                        </td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase 
                                {{ $i->priorite === 'urgent' ? 'bg-rose-100 text-rose-700' : ($i->priorite === 'moyen' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">
                                {{ $i->priorite }}
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full {{ $i->statut === 'resolu' ? 'bg-emerald-500' : ($i->statut === 'en_cours' ? 'bg-blue-500' : 'bg-rose-500') }}"></div>
                                <span class="text-xs font-black text-slate-700 capitalize">{{ str_replace('_', ' ', $i->statut) }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('incidents.show', $i) }}" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                @if(auth()->user()->role === 'admin')
                                <a href="{{ route('incidents.edit', $i) }}" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-8 py-20 text-center text-slate-400 italic font-bold">Aucun incident locatif signalé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-8 py-4 border-t border-slate-100">{{ $incidents->links() }}</div>
    </div>

    {{-- LISTE DE LA MAINTENANCE (IA) --}}
    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'proprietaire')
    <div x-show="tab === 'maintenance'" class="space-y-8">
        @forelse($maintenanceRequests as $req)
        <div class="bg-white rounded-[40px] p-10 border border-slate-100 shadow-sm hover:shadow-xl transition-all">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-xl">
                        {{ substr($req->user->name, 0, 1) }}
                    </div>
                    <div>
                        <h4 class="text-lg font-black text-slate-800">{{ $req->user->name }}</h4>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $req->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <span class="px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest
                    {{ $req->status === 'resolved' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-200' : 'bg-indigo-500 text-white shadow-lg shadow-indigo-200' }}">
                    {{ $req->status === 'auto_replied' ? 'IA a répondu' : ($req->status === 'resolved' ? 'Résolu par l\'Admin' : 'Analyse en cours') }}
                </span>
            </div>

            <div class="mb-8 p-6 bg-slate-50/50 rounded-3xl border border-dashed border-slate-200">
                <p class="text-xs font-black text-slate-400 uppercase mb-3 italic">Question du Propriétaire : {{ $req->subject }}</p>
                <p class="text-slate-700 font-bold italic leading-relaxed text-lg">"{{ $req->message }}"</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Réponse de l'IA --}}
                @if($req->auto_response)
                <div class="p-8 bg-indigo-600 rounded-[35px] text-white relative overflow-hidden shadow-xl shadow-indigo-100">
                    <div class="absolute right-0 top-0 opacity-10 translate-x-1/4 -translate-y-1/4">
                        <i class="fa-solid fa-robot text-8xl"></i>
                    </div>
                    <div class="flex items-center gap-3 mb-4 relative z-10">
                        <i class="fa-solid fa-wand-magic-sparkles text-indigo-200"></i>
                        <p class="text-[10px] font-black uppercase tracking-widest text-indigo-100">Réponse de l'Assistant Expert IA</p>
                    </div>
                    <p class="text-sm font-bold leading-relaxed relative z-10">{{ $req->auto_response }}</p>
                </div>
                @endif

                {{-- Réponse de l'Admin --}}
                @if($req->admin_response)
                <div class="p-8 bg-emerald-600 rounded-[35px] text-white relative overflow-hidden shadow-xl shadow-emerald-100">
                    <div class="absolute right-0 top-0 opacity-10 translate-x-1/4 -translate-y-1/4">
                        <i class="fa-solid fa-user-tie text-8xl"></i>
                    </div>
                    <div class="flex items-center gap-3 mb-4 relative z-10">
                        <i class="fa-solid fa-check-double text-emerald-200"></i>
                        <p class="text-[10px] font-black uppercase tracking-widest text-emerald-100">Réponse Personnalisée de l'Admin</p>
                    </div>
                    <p class="text-sm font-black leading-relaxed relative z-10">{{ $req->admin_response }}</p>
                </div>
                @elseif(auth()->user()->role === 'admin' && $req->status !== 'resolved')
                <div class="p-8 bg-white rounded-[35px] border-2 border-dashed border-slate-200 flex flex-col justify-center">
                    <form action="{{ route('maintenance.manualResponse', $req) }}" method="POST">
                        @csrf
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">Intervenir Manuellement :</label>
                        <textarea name="admin_response" required rows="3" 
                                  placeholder="Votre réponse précise qui s'affichera directement pour le proprio..."
                                  class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-emerald-500 outline-none mb-4 font-bold"></textarea>
                        <button type="submit" class="w-full py-3 bg-emerald-600 text-white font-black rounded-xl text-xs uppercase hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-100">
                            Envoyer la réponse humaine
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-[40px] p-20 text-center border border-dashed border-slate-200 text-slate-400 font-bold">
            Aucune demande de maintenance applicative.
        </div>
        @endforelse
    </div>
    @endif
</div>

@endsection