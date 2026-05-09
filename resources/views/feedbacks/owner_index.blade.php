@extends('layouts.app')

@section('title', 'Votre avis sur GESTLOYER')

@section('content')

<div class="max-w-4xl mx-auto py-10">
    <div class="text-center mb-12">
        <div class="w-20 h-20 bg-amber-100 text-amber-600 rounded-[30px] flex items-center justify-center mx-auto mb-6 shadow-lg shadow-amber-50">
            <i class="fa-solid fa-flask-vial text-3xl"></i>
        </div>
        <h2 class="text-3xl font-black text-slate-800">Le Laboratoire d'Innovations</h2>
        <p class="text-slate-500 font-medium mt-2">Collaborez avec l'administration pour construire le futur de GESTLOYER.</p>
    </div>

    {{-- Mur des Innovations (Annonces Admin) --}}
    @if($announcements->count() > 0)
    <div class="mb-12 space-y-8">
        <h3 class="text-xl font-black text-slate-800 flex items-center gap-3 mb-6 px-2">
            <i class="fa-solid fa-bullhorn text-indigo-500"></i>
            Innovations proposées par l'Administrateur
        </h3>
        
        @foreach($announcements as $ann)
        <div class="bg-indigo-600 rounded-[40px] p-10 text-white shadow-xl shadow-indigo-100 relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-3 py-1 bg-white/20 rounded-full text-[9px] font-black uppercase tracking-widest">Nouveauté en cours d'étude</span>
                    <span class="text-[10px] text-indigo-200 font-bold italic">{{ $ann->created_at->diffForHumans() }}</span>
                </div>
                <h4 class="text-2xl font-black mb-6 leading-tight">{{ $ann->comment }}</h4>
                
                <div class="bg-white/10 rounded-3xl p-8 border border-white/10">
                    <h5 class="text-[10px] font-black uppercase tracking-widest text-indigo-200 mb-4">Votre avis nous intéresse :</h5>
                    
                    @php $myReaction = $ann->reactions->first(); @endphp
                    
                    @if($myReaction)
                        <div class="bg-white/20 p-5 rounded-2xl border border-white/20 italic">
                            <p class="text-[10px] font-black text-white uppercase mb-2">Votre réaction enregistrée :</p>
                            <p class="font-bold">"{{ $myReaction->comment }}"</p>
                        </div>
                    @else
                        <form action="{{ route('feedbacks.store') }}" method="POST" class="flex flex-col md:flex-row gap-4">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $ann->id }}">
                            <input type="text" name="comment" required placeholder="Que pensez-vous de cette idée ? (Validé, à modifier...)" 
                                   class="flex-1 bg-white/10 border border-white/20 rounded-2xl px-6 py-4 text-white placeholder-indigo-200 outline-none focus:ring-2 focus:ring-white/30 transition-all font-medium">
                            <button type="submit" class="bg-white text-indigo-600 px-8 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-indigo-50 transition-all">
                                Envoyer mon avis
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            <i class="fa-solid fa-rocket absolute right-0 bottom-0 text-[150px] opacity-10 translate-x-1/4 translate-y-1/4"></i>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Formulaire de Critique Classique --}}
    <div class="bg-white rounded-[40px] p-10 border border-slate-100 shadow-xl mb-12">
        <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-3">
            <i class="fa-solid fa-pen-nib text-amber-500"></i>
            Proposer une autre amélioration
        </h3>
        <form action="{{ route('feedbacks.store') }}" method="POST" x-data="{ stars: 5 }">
            @csrf
            <div class="mb-8 text-center">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Note globale de l'appli</label>
                <div class="flex items-center justify-center gap-4">
                    <template x-for="i in 5">
                        <button type="button" @click="stars = i" class="transition-all transform hover:scale-125">
                            <i class="fa-solid fa-star text-4xl" :class="i <= stars ? 'text-amber-400' : 'text-slate-100'"></i>
                        </button>
                    </template>
                    <input type="hidden" name="stars" :value="stars">
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Détaillez votre suggestion</label>
                <textarea name="comment" rows="5" required 
                          placeholder="Dites-nous ce que vous aimeriez changer ou améliorer dans GESTLOYER..."
                          class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-6 px-8 rounded-3xl focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all font-medium resize-none"></textarea>
            </div>

            <button type="submit" class="w-full py-5 bg-slate-900 text-white font-black rounded-3xl hover:bg-slate-800 shadow-xl shadow-slate-200 transition-all flex items-center justify-center gap-3">
                <i class="fa-solid fa-paper-plane"></i>
                Envoyer ma suggestion
            </button>
        </form>
    </div>

    {{-- Historique --}}
    @if($myFeedbacks->count() > 0)
    <div class="space-y-6">
        <h3 class="text-lg font-black text-slate-800 flex items-center gap-3 mb-6 px-2">
            <i class="fa-solid fa-clock-rotate-left text-slate-400"></i>
            Historique de vos propositions
        </h3>

        @foreach($myFeedbacks as $fb)
        <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-1">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fa-solid fa-star text-[10px] {{ $i <= $fb->stars ? 'text-amber-400' : 'text-slate-100' }}"></i>
                    @endfor
                </div>
                <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest
                    {{ $fb->status === 'implemented' ? 'bg-emerald-100 text-emerald-700' : ($fb->status === 'validated' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500') }}">
                    {{ $fb->status === 'implemented' ? '🚀 Implémenté' : ($fb->status === 'validated' ? '✅ Validé' : '⏳ En attente') }}
                </span>
            </div>
            <p class="text-slate-600 font-medium mb-6 italic">"{{ $fb->comment }}"</p>
            
            @if($fb->admin_note)
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Décision de l'Admin :</p>
                <p class="text-sm text-slate-700 font-bold">{{ $fb->admin_note }}</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>

@endsection
