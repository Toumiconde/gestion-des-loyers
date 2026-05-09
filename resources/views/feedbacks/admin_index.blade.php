@extends('layouts.app')

@section('title', 'Critiques & Validation des Projets')

@section('content')

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Laboratoire d'Évolution</h2>
        <p class="text-slate-500 font-medium">Analysez les critiques et proposez de nouvelles fonctionnalités</p>
    </div>
    <div class="flex gap-4">
        <div class="bg-white px-8 py-4 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-6">
            <div class="text-right">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Note Moyenne</p>
                <p class="text-2xl font-black text-slate-800">{{ number_format($averageStars, 1) }} <span class="text-xs text-slate-400">/ 5</span></p>
            </div>
            <div class="flex items-center gap-1">
                @for($i = 1; $i <= 5; $i++)
                    <i class="fa-solid fa-star text-base {{ $i <= round($averageStars) ? 'text-amber-400' : 'text-slate-100' }}"></i>
                @endfor
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
    
    {{-- COLONNE DE GAUCHE : ANNONCES & PROPOSITIONS --}}
    <div class="lg:col-span-2 space-y-10">
        
        {{-- Formulaire d'annonce de nouvelle fonctionnalité --}}
        <div class="bg-indigo-600 rounded-[40px] p-10 text-white shadow-xl shadow-indigo-100 relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-2xl font-black mb-2 flex items-center gap-3">
                    <i class="fa-solid fa-rocket"></i>
                    Annoncer une Innovation
                </h3>
                <p class="text-indigo-100 text-sm mb-8">Informez les propriétaires des nouvelles fonctionnalités en cours de développement.</p>
                
                <form action="{{ route('feedbacks.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="is_announcement" value="1">
                    <textarea name="comment" required rows="3" 
                              placeholder="Décrivez la nouvelle fonctionnalité (ex: Nous prévoyons d'ajouter la signature électronique des baux...)"
                              class="w-full bg-white/10 border border-white/20 rounded-2xl p-6 text-white placeholder-indigo-200 outline-none focus:ring-2 focus:ring-white/30 transition-all font-medium mb-4"></textarea>
                    <button type="submit" class="bg-white text-indigo-600 px-8 py-4 rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-indigo-50 transition-all shadow-lg">
                        Publier sur le mur du Lab
                    </button>
                </form>
            </div>
            <i class="fa-solid fa-flask absolute right-0 bottom-0 text-[180px] opacity-10 translate-x-1/4 translate-y-1/4"></i>
        </div>

        {{-- Liste des Annonces avec Réactions --}}
        @foreach($announcements as $ann)
        <div class="bg-white rounded-[40px] p-10 border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
                <div>
                    <h4 class="font-black text-slate-800">Innovation proposée</h4>
                    <p class="text-[10px] text-slate-400 font-bold uppercase">{{ $ann->created_at->diffForHumans() }}</p>
                </div>
            </div>
            
            <div class="text-lg font-bold text-slate-700 leading-relaxed mb-8">
                {{ $ann->comment }}
            </div>

            <div class="pt-8 border-t border-slate-50">
                <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-comments"></i>
                    Réactions des Propriétaires ({{ $ann->reactions->count() }})
                </h5>
                <div class="space-y-4">
                    @forelse($ann->reactions as $react)
                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                                <span class="text-xs font-black text-slate-800">{{ $react->user->name }} :</span>
                            </div>
                            <p class="text-sm text-slate-600 font-medium italic">"{{ $react->comment }}"</p>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">Aucune réaction pour le moment.</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- COLONNE DE DROITE : SUGGESTIONS REÇUES --}}
    <div class="space-y-8">
        <h3 class="text-xl font-black text-slate-800 flex items-center gap-3 px-2">
            <i class="fa-solid fa-inbox text-slate-400"></i>
            Suggestions reçues
        </h3>

        @forelse($feedbacks as $fb)
        <div class="bg-white rounded-[35px] p-8 border border-slate-100 shadow-sm hover:shadow-lg transition-all">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center font-black text-slate-600 text-xs">
                        {{ substr($fb->user->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-800">{{ $fb->user->name }}</p>
                        <p class="text-[9px] text-slate-400 font-bold uppercase">{{ $fb->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex gap-0.5">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fa-solid fa-star text-[8px] {{ $i <= $fb->stars ? 'text-amber-400' : 'text-slate-100' }}"></i>
                    @endfor
                </div>
            </div>

            <p class="text-sm text-slate-600 leading-relaxed mb-6 font-medium italic">"{{ $fb->comment }}"</p>

            <form action="{{ route('feedbacks.updateStatus', $fb) }}" method="POST" class="space-y-4 pt-4 border-t border-slate-50">
                @csrf @method('PATCH')
                <select name="status" class="w-full bg-slate-50 border border-slate-100 text-slate-700 py-2 px-3 rounded-xl text-[10px] font-black uppercase outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="pending" {{ $fb->status === 'pending' ? 'selected' : '' }}>⏳ En attente</option>
                    <option value="validated" {{ $fb->status === 'validated' ? 'selected' : '' }}>✅ Valider</option>
                    <option value="implemented" {{ $fb->status === 'implemented' ? 'selected' : '' }}>🚀 Implémenté</option>
                </select>
                <button type="submit" class="w-full py-2 bg-slate-900 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all">Mettre à jour</button>
            </form>
        </div>
        @empty
        <div class="p-10 text-center text-slate-400 bg-white rounded-[35px] border border-dashed border-slate-200">
            Aucune suggestion.
        </div>
        @endforelse
    </div>
</div>

@endsection
