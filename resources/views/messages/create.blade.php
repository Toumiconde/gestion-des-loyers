@extends('layouts.app')

@section('title', 'Nouveau Message')

@section('content')

<div class="max-w-4xl mx-auto py-8">
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm font-medium">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-blue-600 transition-colors">
                    <i class="fa-solid fa-house mr-2"></i> Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-slate-300 mx-2 text-xs"></i>
                    <a href="{{ route('messages.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">Messages</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-slate-300 mx-2 text-xs"></i>
                    <span class="text-slate-600">Nouveau</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-blue-600 p-8 text-white relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full"></div>
            <div class="relative z-10 flex items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center text-3xl">
                    <i class="fa-solid fa-comment-dots"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-black mb-1">Rédiger un message</h2>
                    <p class="text-blue-50 italic">Exprimez votre demande, notre système s'occupe du reste.</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('messages.store') }}" class="p-8">
            @csrf

            <div class="space-y-8">
                <div>
                    <label class="block text-sm font-black text-slate-700 mb-2">Destinataire</label>
                    <div class="relative">
                        <select name="receiver_id" required
                                class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-4 pl-12 pr-4 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-semibold cursor-pointer shadow-sm">
                            <option value="">-- Sélectionner le destinataire --</option>
                            @foreach($receivers as $r)
                                <option value="{{ $r->id }}">{{ $r->name }} ({{ ucfirst($r->role) }})</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-user-tie absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-black text-slate-700 mb-2">Votre Message</label>
                    <div class="relative">
                        <textarea name="content" rows="6" required
                                  placeholder="Décrivez votre situation (ex: demande de départ, problème financier, panne technique...)"
                                  class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium shadow-sm resize-none"></textarea>
                    </div>
                    <div class="mt-4 flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-200">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="is_urgent" value="1" class="w-6 h-6 rounded-lg border-slate-300 text-rose-600 focus:ring-rose-500">
                            <span class="font-black text-slate-700 uppercase text-xs tracking-widest group-hover:text-rose-600 transition-colors">⚠️ Marquer ce message comme URGENT</span>
                        </label>
                    </div>
                    
                    <div class="mt-4 p-4 bg-blue-50 rounded-xl border border-blue-100 flex items-start gap-3">
                        <i class="fa-solid fa-circle-info text-blue-600 mt-1"></i>
                        <p class="text-[10px] text-blue-800 leading-relaxed font-bold uppercase tracking-widest">
                            Note : Notre IA analyse également votre texte. Les sujets graves (résiliation, fuite, impayé) sont priorisés automatiquement.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4 mt-12 pt-8 border-t border-slate-100">
                <button type="submit"
                        class="px-10 py-4 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 shadow-xl shadow-blue-200 transition-all active:scale-95 flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Envoyer le message
                </button>
                <a href="{{ route('messages.index') }}"
                   class="px-10 py-4 bg-slate-100 text-slate-600 font-bold rounded-2xl hover:bg-slate-200 transition-all">
                    Annuler
                </a>
            </div>

        </form>
    </div>
</div>

@endsection
