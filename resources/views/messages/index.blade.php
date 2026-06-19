@extends('layouts.app')

@section('title', 'Centre de Communication')

@section('content')

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Messages & Échanges</h2>
        <p class="text-slate-500 font-medium">Communiquez avec vos locataires ou propriétaires (Canal Admin réservé à l'agence)</p>
    </div>
    
    <a href="{{ route('messages.create') }}" 
       class="inline-flex items-center justify-center px-6 py-3.5 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 shadow-xl shadow-blue-200 transition-all active:scale-95 group">
        <i class="fa-solid fa-paper-plane mr-2 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
        Nouveau message
    </a>
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-emerald-100 text-emerald-700 rounded-2xl font-bold flex items-center gap-3">
    <i class="fa-solid fa-circle-check"></i>
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    
    {{-- Filtres / Stats --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Filtres</h3>
            <div class="space-y-2">
                <a href="{{ route('messages.index') }}" 
                   class="w-full flex items-center justify-between p-3 {{ !request('filter') ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50' }} rounded-xl font-black text-sm transition-all">
                    <span>Tous les messages</span>
                    <span class="bg-blue-600 text-white text-[10px] px-2 py-0.5 rounded-full">
                        {{ \App\Models\Message::where(function($q) {
                            $q->where('sender_id', auth()->id())->orWhere('receiver_id', auth()->id());
                        })->count() }}
                    </span>
                </a>
                <button class="w-full flex items-center justify-between p-3 text-slate-500 hover:bg-slate-50 rounded-xl font-bold text-sm transition-all">
                    <span>Messages urgents</span>
                    <span class="bg-rose-500 text-white text-[10px] px-2 py-0.5 rounded-full">{{ $messages->where('is_urgent', true)->where('is_read', false)->where('receiver_id', auth()->id())->count() }}</span>
                </button>
                <a href="{{ route('messages.index', ['filter' => 'unread']) }}" class="w-full flex items-center justify-between p-3 {{ request('filter') === 'unread' ? 'bg-amber-50 text-amber-600' : 'text-slate-500 hover:bg-slate-50' }} rounded-xl font-bold text-sm transition-all">
                    <span>Non lus</span>
                    <span class="bg-amber-500 text-white text-[10px] px-2 py-0.5 rounded-full">{{ $messages->where('is_read', false)->where('receiver_id', auth()->id())->count() }}</span>
                </a>
                @if(auth()->user()->role !== 'comptable')
                <a href="{{ route('messages.index', ['filter' => 'support']) }}" 
                   class="w-full flex items-center justify-between p-3 {{ request('filter') === 'support' ? 'bg-indigo-50 text-indigo-600' : 'text-slate-500 hover:bg-slate-50' }} rounded-xl font-bold text-sm transition-all">
                    <span>Support Technique</span>
                    <span class="bg-indigo-600 text-white text-[10px] px-2 py-0.5 rounded-full">
                        {{ \App\Models\Message::where('is_support', true)
                            ->where(function($q) {
                                if(auth()->user()->role === 'admin') return; 
                                $q->where('sender_id', auth()->id())->orWhere('receiver_id', auth()->id());
                            })->count() }}
                    </span>
                </a>
                @endif
                <a href="{{ route('messages.archived') }}" class="w-full flex items-center justify-between p-3 text-slate-500 hover:bg-slate-50 rounded-xl font-bold text-sm transition-all">
                    <span>Historique</span>
                    <i class="fa-solid fa-clock-rotate-left text-[10px] opacity-30"></i>
                </a>
            </div>
        </div>

        <div class="bg-slate-900 rounded-3xl p-8 text-white relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 opacity-10">
                <i class="fa-solid fa-robot text-6xl"></i>
            </div>
            <h3 class="font-black text-sm mb-2">Analyse Intelligente</h3>
            <p class="text-[10px] text-slate-400 leading-relaxed">Vos messages sont analysés pour détecter les urgences (départ, problèmes techniques) et alerter immédiatement les responsables.</p>
        </div>
    </div>

    {{-- Liste des messages --}}
    <div class="lg:col-span-3">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-100 bg-slate-50/30 flex items-center justify-between">
                <h3 class="text-xl font-black text-slate-800">Boîte de réception</h3>
                <div class="flex items-center gap-4">
                    @if($messages->where('is_read', false)->where('receiver_id', auth()->id())->count() > 0)
                        <form action="{{ route('messages.markAllAsRead') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs font-bold text-blue-600 hover:underline">
                                Tout marquer comme lu
                            </button>
                        </form>
                    @endif
                    <div class="relative">
                        <input type="text" placeholder="Rechercher..." class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-xs focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($messages as $m)
                @php $isIncoming = ($m->receiver_id == auth()->id()); @endphp
                <a href="{{ route('messages.show', $m) }}" class="flex items-center gap-6 p-6 hover:bg-slate-50 transition-all group {{ $isIncoming && !$m->is_read ? 'bg-blue-50/30' : '' }}">
                    <div class="shrink-0 relative">
                        <div class="w-12 h-12 rounded-2xl {{ $isIncoming ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-lg font-black">
                            @if($m->is_support && (($isIncoming && ($m->sender->role ?? '') === 'admin') || (!$isIncoming && ($m->receiver->role ?? '') === 'admin')))
                                <i class="fa-solid fa-robot"></i>
                            @else
                                {{ substr($isIncoming ? ($m->sender->name ?? '?') : ($m->receiver->name ?? '?'), 0, 1) }}
                            @endif
                        </div>
                        @if($isIncoming && !$m->is_read)
                            <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-rose-500 border-2 border-white rounded-full"></span>
                        @endif
                    </div>

                    <div class="grow min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="font-black text-slate-800 truncate">
                                @if($m->is_system)
                                    Le Système
                                @else
                                    @if($m->is_support)
                                        @php
                                            $role = $isIncoming ? ($m->sender->role ?? '') : ($m->receiver->role ?? '');
                                            $label = $role === 'proprietaire' ? 'Propriétaire' : ($role === 'locataire' ? 'Locataire' : 'Utilisateur');
                                        @endphp
                                        {{ $isIncoming ? $label : 'À: ' . $label }}
                                        <span class="ml-2 px-2 py-0.5 bg-indigo-50 text-indigo-600 text-[8px] uppercase tracking-widest rounded-full border border-indigo-100">Support</span>
                                    @else
                                        {{ $isIncoming ? ($m->sender->name ?? 'Utilisateur supprimé') : 'À: ' . ($m->receiver->name ?? 'Utilisateur supprimé') }}
                                    @endif
                                @endif

                                @if($m->is_urgent)
                                    <span class="ml-2 px-2 py-0.5 bg-rose-100 text-rose-600 text-[8px] uppercase tracking-widest rounded-full">Urgent</span>
                                @endif
                            </h4>
                            <span class="text-[10px] text-slate-400 font-bold whitespace-nowrap">{{ $m->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-slate-500 truncate group-hover:text-slate-700 transition-colors">
                            {{ $m->content }}
                        </p>
                    </div>

                    <div class="shrink-0 flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <form action="{{ route('messages.destroy', $m) }}" method="POST" onsubmit="return confirm('Archiver ce message ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-400 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all">
                                <i class="fa-solid fa-trash-can text-[10px]"></i>
                            </button>
                        </form>
                        <i class="fa-solid fa-chevron-right text-slate-300"></i>
                    </div>
                </a>
                @empty
                <div class="p-20 text-center">
                    <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-inbox text-slate-200 text-3xl"></i>
                    </div>
                    <h4 class="text-lg font-black text-slate-800 mb-2">Aucun message</h4>
                    <p class="text-slate-400 text-sm">Commencez une conversation avec votre bailleur ou vos locataires.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection
