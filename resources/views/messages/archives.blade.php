@extends('layouts.app')

@section('title', 'Historique des Messages')

@section('content')

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Historique / Archives</h2>
        <p class="text-slate-500 font-medium">Retrouvez tous vos échanges supprimés</p>
    </div>
    
    <a href="{{ route('messages.index') }}" 
       class="inline-flex items-center justify-center px-6 py-3.5 bg-slate-800 text-white font-black rounded-2xl hover:bg-slate-900 shadow-xl shadow-slate-200 transition-all active:scale-95">
        <i class="fa-solid fa-arrow-left mr-2"></i>
        Retour à la boîte
    </a>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-8 border-b border-slate-100 bg-slate-50/30 flex items-center justify-between">
        <h3 class="text-xl font-black text-slate-800">Messages supprimés</h3>
    </div>

    <div class="divide-y divide-slate-100">
        @forelse($messages as $m)
        @php $isIncoming = ($m->receiver_id == auth()->id()); @endphp
        <div class="flex items-center gap-6 p-6 opacity-60 hover:opacity-100 transition-all group bg-slate-50/20">
            <div class="shrink-0">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-lg font-black">
                    @if($m->is_support && (($isIncoming && ($m->sender->role ?? '') === 'admin') || (!$isIncoming && ($m->receiver->role ?? '') === 'admin')))
                        <i class="fa-solid fa-robot"></i>
                    @else
                        {{ substr($isIncoming ? ($m->sender->name ?? '?') : ($m->receiver->name ?? '?'), 0, 1) }}
                    @endif
                </div>
            </div>

            <div class="grow min-w-0">
                <div class="flex items-center justify-between mb-1">
                    <h4 class="font-black text-slate-800 truncate">
                        @if($m->is_support && (($isIncoming && ($m->sender->role ?? '') === 'admin') || (!$isIncoming && ($m->receiver->role ?? '') === 'admin')))
                            <span class="text-blue-600"><i class="fa-solid fa-robot mr-1"></i> Le Système</span>
                        @else
                            {{ $isIncoming ? ($m->sender->name ?? 'Utilisateur supprimé') : 'À: ' . ($m->receiver->name ?? 'Utilisateur supprimé') }}
                        @endif
                        
                        <span class="ml-2 px-2 py-0.5 bg-slate-100 text-slate-500 text-[8px] uppercase tracking-widest rounded-full">Archivé</span>
                        @if($m->is_support)
                            <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-600 text-[8px] uppercase tracking-widest rounded-full">Support</span>
                        @endif
                    </h4>
                    <span class="text-[10px] text-slate-400 font-bold whitespace-nowrap">Supprimé {{ $m->deleted_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-slate-500 truncate italic">
                    {{ $m->content }}
                </p>
            </div>

            <div class="shrink-0 flex items-center gap-2">
                <form action="{{ route('messages.restore', $m->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Restaurer">
                        <i class="fa-solid fa-rotate-left"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="p-20 text-center text-slate-400 italic">
            <i class="fa-solid fa-box-open text-4xl mb-4 block opacity-20"></i>
            Aucun message dans l'historique.
        </div>
        @endforelse
    </div>
</div>

@endsection
