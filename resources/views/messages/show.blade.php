@extends('layouts.app')

@section('title', 'Lecture Message')

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
                    <span class="text-slate-600">Conversation</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl {{ $message->is_support ? 'bg-blue-600' : 'bg-slate-900' }} text-white flex items-center justify-center text-xl font-black">
                    @if($message->is_support)
                        <i class="fa-solid fa-robot"></i>
                    @else
                        {{ substr($message->sender->name ?? '?', 0, 1) }}
                    @endif
                </div>
                <div>
                    @if($message->is_support && ($message->sender->role ?? '') === 'admin')
                        <h3 class="text-xl font-black text-blue-600">Le Système (Support Technique)</h3>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Assistance Automatisée • {{ $message->created_at->format('d M Y à H:i') }}</p>
                    @else
                        @php
                            $displayName = $message->sender->name ?? 'Utilisateur supprimé';
                            if ($message->is_support) {
                                $role = $message->sender->role ?? '';
                                $displayName = $role === 'proprietaire' ? 'Propriétaire' : ($role === 'locataire' ? 'Locataire' : $displayName);
                            }
                        @endphp
                        <h3 class="text-xl font-black text-slate-800">{{ $displayName }}</h3>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">{{ $message->sender->role ?? 'N/A' }} • {{ $message->created_at->format('d M Y à H:i') }}</p>
                    @endif
                </div>
            </div>
            @if($message->is_urgent)
                <span class="px-4 py-1.5 bg-rose-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-rose-200 animate-pulse">
                    <i class="fa-solid fa-triangle-exclamation mr-2"></i> Urgent
                </span>
            @endif
        </div>

        <div class="p-10 bg-slate-50/50">
            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm text-slate-700 leading-relaxed text-lg whitespace-pre-line mb-6">
                {{ $message->content }}
            </div>

            @if($message->attachment_path)
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center justify-between gap-6 group hover:border-blue-500 transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center text-3xl text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-600 transition-all overflow-hidden">
                        @php
                            $extension = pathinfo($message->attachment_path, PATHINFO_EXTENSION);
                            $isPhoto = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        @endphp
                        @if($isPhoto)
                            <img src="{{ asset('storage/' . $message->attachment_path) }}" class="w-full h-full object-cover">
                        @else
                            <i class="fa-solid fa-file-pdf"></i>
                        @endif
                    </div>
                    <div>
                        <p class="font-black text-slate-800 text-sm mb-1 line-clamp-1">{{ $message->attachment_name }}</p>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            {{ $isPhoto ? 'Média / Photo' : 'Document Officiel' }}
                        </p>
                    </div>
                </div>
                
                <div class="flex flex-col items-end gap-2">
                    <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Action requise</p>
                    <a href="{{ route('documents.index') }}" 
                       class="px-6 py-3 bg-slate-900 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-blue-600 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-arrow-right-long"></i> Consulter dans mes Documents
                    </a>
                </div>
            </div>
            @endif
             @php
            $isStaffSender = in_array($message->sender->role ?? '', ['admin', 'gestionnaire']);
            $canReply = $message->can_reply && !$isStaffSender;
        @endphp

        @if($message->receiver_id === auth()->id())
            @if($canReply)
            <div class="p-8 border-t border-slate-100 bg-white">
                <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-6">Répondre</h4>
                <form method="POST" action="{{ route('messages.store') }}">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $message->sender_id }}">
                    <input type="hidden" name="is_support" value="{{ $message->is_support ? 1 : 0 }}">
                    <textarea name="content" rows="4" required
                               placeholder="Écrivez votre réponse ici..."
                               class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium mb-6 resize-none"></textarea>
                    <button type="submit" class="px-8 py-4 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 shadow-xl shadow-blue-200 transition-all active:scale-95 flex items-center gap-2">
                        <i class="fa-solid fa-reply"></i> Envoyer la réponse
                    </button>
                </form>
            </div>
            @else
            <div class="p-10 border-t border-slate-100 bg-slate-50 flex flex-col items-center text-center">
                <div class="w-16 h-16 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center mb-6 shadow-sm">
                    <i class="fa-solid fa-bell-slash text-xl"></i>
                </div>
                <h4 class="text-slate-800 font-black uppercase tracking-widest text-sm mb-2">Message à Sens Unique</h4>
                <p class="text-slate-500 font-medium max-w-sm">
                    @if(($message->sender->role ?? '') === 'admin')
                        Cette communication provient de l'administration. 
                        <span class="block mt-2 font-black text-rose-600 uppercase text-[10px]">Impossible de répondre à l'administration.</span>
                    @else
                        Cette communication provient de la gestion. 
                        <span class="block mt-2 font-black text-rose-600 uppercase text-[10px]">Impossible de répondre à la gestion.</span>
                    @endif
                </p>
            </div>
            @endif
        @endif
        
        <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-center">
            <a href="{{ route('messages.index') }}" class="text-slate-400 text-xs font-black uppercase hover:text-slate-600 transition-colors">Retour à la boîte de réception</a>
        </div>
    </div>
</div>

@endsection
