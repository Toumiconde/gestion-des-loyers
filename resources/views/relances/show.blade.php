@extends('layouts.app')

@section('title', 'Détails de la Relance')

@section('content')

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
                        <a href="{{ route('relances.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">Relances</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-slate-300 mx-2 text-xs"></i>
                        <span class="text-slate-600">Détails relance</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Statut Card --}}
        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 text-center relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-indigo-50 rounded-full"></div>
                <div class="w-20 h-20 rounded-3xl bg-indigo-600 text-white flex items-center justify-center text-3xl mx-auto mb-6 relative z-10 shadow-lg shadow-indigo-100">
                    <i class="fa-solid fa-paper-plane"></i>
                </div>
                <h2 class="text-2xl font-black text-slate-800 capitalize">{{ $relance->statut }}</h2>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-2">Envoyée le {{ $relance->created_at->format('d/m/Y à H:i') }}</p>
                
                <div class="mt-8 pt-8 border-t border-slate-50 space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-medium">Niveau</span>
                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-black uppercase">{{ $relance->niveau ?? 'Normal' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-medium">Canal</span>
                        <span class="font-black text-slate-700 capitalize">{{ $relance->type ?? 'Email' }}</span>
                    </div>
                </div>
            </div>

            @if($relance->statut === 'envoye' || $relance->statut === 'envoyee')
            <div class="bg-indigo-900 rounded-3xl p-8 text-white shadow-xl shadow-indigo-100 relative overflow-hidden">
                <div class="absolute right-0 bottom-0 opacity-10">
                    <i class="fa-solid fa-circle-check text-6xl -mb-4 -mr-4"></i>
                </div>
                <h3 class="text-lg font-black mb-4">Action requise</h3>
                <p class="text-indigo-300 text-sm leading-relaxed mb-6">Le locataire a-t-il régularisé sa situation ou répondu à ce rappel ?</p>
                <form method="POST" action="{{ route('relances.update', $relance) }}">
                    @csrf @method('PUT')
                    <button type="submit" class="w-full py-3 bg-white text-indigo-900 font-black rounded-xl hover:bg-indigo-50 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check"></i> Marquer comme réglé
                    </button>
                </form>
            </div>
            @endif
        </div>

        {{-- Détails --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-3">
                    <i class="fa-solid fa-circle-info text-indigo-600"></i> Informations Relance
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center shrink-0"><i class="fa-solid fa-user text-slate-400"></i></div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Locataire concerné</p>
                                <p class="font-bold text-slate-800">{{ $relance->contrat->locataire->prenom }} {{ $relance->contrat->locataire->nom }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center shrink-0"><i class="fa-solid fa-building text-slate-400"></i></div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Bien immobilier</p>
                                <p class="font-bold text-slate-800">{{ $relance->contrat->bien->libelle }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center shrink-0"><i class="fa-solid fa-file-contract text-slate-400"></i></div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">N° de Contrat</p>
                                <p class="font-bold text-slate-800">{{ $relance->contrat->numero_contrat }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-4">Contenu / Notes de la relance</p>
                    <p class="text-slate-600 italic leading-relaxed">
                        " {{ $relance->message ?: 'Aucun message personnalisé n\'a été ajouté à cette relance.' }} "
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection