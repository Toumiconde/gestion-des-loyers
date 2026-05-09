@extends('layouts.app')

@section('title', 'Assistance Technique & Maintenance')

@section('content')

<div class="max-w-4xl mx-auto py-10">
    <div class="text-center mb-12">
        <div class="w-20 h-20 bg-indigo-100 text-indigo-600 rounded-[30px] flex items-center justify-center mx-auto mb-6 shadow-lg shadow-indigo-50">
            <i class="fa-solid fa-robot text-3xl"></i>
        </div>
        <h2 class="text-3xl font-black text-slate-800">Assistance Intelligente</h2>
        <p class="text-slate-500 font-medium mt-2">Posez votre question, notre assistant IA analyse vos besoins 24h/24.</p>
    </div>

    @if(session('success'))
    <div class="mb-8 bg-indigo-50 border-l-4 border-indigo-500 p-6 rounded-2xl flex items-center gap-4 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
            <i class="fa-solid fa-check text-indigo-600"></i>
        </div>
        <p class="text-indigo-800 font-bold">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Formulaire de Maintenance --}}
    <div class="bg-white rounded-[40px] p-10 border border-slate-100 shadow-xl mb-12">
        <form action="{{ route('maintenance.store') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Sujet de votre inquiétude</label>
                <input type="text" name="subject" required placeholder="Ex: Problème de signature, Erreur de calcul..."
                       class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
            </div>

            <div class="mb-8">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Détaillez votre besoin</label>
                <textarea name="message" rows="5" required 
                          placeholder="Décrivez précisément ce qui vous bloque ou l'opération que vous souhaitez faire..."
                          class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-6 px-8 rounded-3xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all font-medium resize-none"></textarea>
            </div>

            <button type="submit" class="w-full py-5 bg-indigo-600 text-white font-black rounded-3xl hover:bg-indigo-700 shadow-xl shadow-indigo-200 transition-all active:scale-95 flex items-center justify-center gap-3">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                Analyser et Envoyer à l'Assistance
            </button>
        </form>
    </div>

    @if($requests->count() > 0)
    <div class="space-y-8">
        <h3 class="text-2xl font-black text-slate-800 flex items-center gap-4 mb-8">
            <i class="fa-solid fa-list-check text-indigo-500 bg-indigo-50 p-3 rounded-2xl"></i>
            Suivi & Réponses IA de l'Expert
        </h3>

        @foreach($requests as $req)
        <div class="bg-white rounded-[40px] p-10 border border-slate-100 shadow-sm hover:shadow-xl transition-all">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full {{ $req->status === 'resolved' ? 'bg-emerald-500' : 'bg-indigo-500 animate-pulse' }}"></span>
                    <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">{{ $req->subject }}</h4>
                </div>
                <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest
                    {{ $req->status === 'resolved' ? 'bg-emerald-100 text-emerald-700' : ($req->status === 'auto_replied' ? 'bg-indigo-100 text-indigo-700' : 'bg-amber-100 text-amber-700') }}">
                    {{ $req->status === 'auto_replied' ? 'Expertise IA terminée' : ($req->status === 'resolved' ? 'Dossier Clos' : 'Analyse Admin') }}
                </span>
            </div>
            
            <div class="mb-8 p-6 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                <p class="text-slate-500 text-xs font-bold mb-2 italic">Votre message :</p>
                <p class="text-slate-700 font-medium leading-relaxed italic">"{{ $req->message }}"</p>
            </div>

            @if($req->auto_response)
            <div class="bg-indigo-600 p-8 rounded-[30px] border border-indigo-700 relative overflow-hidden shadow-xl shadow-indigo-200">
                <div class="absolute right-0 top-0 opacity-10 translate-x-1/4 -translate-y-1/4">
                    <i class="fa-solid fa-robot text-[120px] text-white"></i>
                </div>
                <div class="flex items-center gap-3 mb-4 relative z-10">
                    <div class="w-10 h-10 rounded-xl bg-white/20 text-white flex items-center justify-center backdrop-blur-sm">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <p class="text-[10px] font-black text-indigo-100 uppercase tracking-widest">Conseil de l'Expert GESTLOYER</p>
                </div>
                <p class="text-lg text-white font-black leading-snug relative z-10">{{ $req->auto_response }}</p>
                
                <div class="mt-6 pt-6 border-t border-white/10 flex items-center gap-2 relative z-10">
                    <i class="fa-solid fa-circle-info text-indigo-200 text-xs"></i>
                    <p class="text-[10px] text-indigo-200 font-bold italic">Cette réponse a été générée instantanément pour vous débloquer.</p>
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>

@endsection
