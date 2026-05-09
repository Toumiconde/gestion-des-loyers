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

        <form method="POST" action="{{ route('messages.store') }}" enctype="multipart/form-data" class="p-8" x-data="{ isBroadcast: false }">
            @csrf

            <div class="space-y-8">
                @if(auth()->user()->isAdmin())
                <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 mb-8">
                    <label class="flex items-center gap-4 cursor-pointer group mb-4">
                        <input type="checkbox" name="is_broadcast" value="1" x-model="isBroadcast" 
                               class="w-6 h-6 rounded-lg border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="font-black text-slate-800 uppercase text-xs tracking-widest">Diffuser à un groupe (Broadcast)</span>
                    </label>

                    <div x-show="isBroadcast" class="space-y-4 pt-4 border-t border-slate-200">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="relative flex flex-col items-center gap-3 p-4 bg-white border border-slate-200 rounded-2xl cursor-pointer hover:border-blue-500 transition-all">
                                <input type="radio" name="broadcast_to" value="all_owners" class="absolute top-4 right-4 text-blue-600 focus:ring-blue-500">
                                <i class="fa-solid fa-user-tie text-2xl text-slate-400"></i>
                                <span class="text-[10px] font-black uppercase text-slate-600">Tous les Propriétaires</span>
                            </label>
                            <label class="relative flex flex-col items-center gap-3 p-4 bg-white border border-slate-200 rounded-2xl cursor-pointer hover:border-blue-500 transition-all">
                                <input type="radio" name="broadcast_to" value="all_tenants" class="absolute top-4 right-4 text-blue-600 focus:ring-blue-500">
                                <i class="fa-solid fa-users-line text-2xl text-slate-400"></i>
                                <span class="text-[10px] font-black uppercase text-slate-600">Tous les Locataires</span>
                            </label>
                            <label class="relative flex flex-col items-center gap-3 p-4 bg-white border border-slate-200 rounded-2xl cursor-pointer hover:border-blue-500 transition-all">
                                <input type="radio" name="broadcast_to" value="all_managers" class="absolute top-4 right-4 text-blue-600 focus:ring-blue-500">
                                <i class="fa-solid fa-user-gear text-2xl text-slate-400"></i>
                                <span class="text-[10px] font-black uppercase text-slate-600">Tous les Gestionnaires</span>
                            </label>
                            <label class="relative flex flex-col items-center gap-3 p-4 bg-white border border-rose-200 rounded-2xl cursor-pointer hover:border-rose-500 transition-all">
                                <input type="radio" name="broadcast_to" value="tenants_in_debt" class="absolute top-4 right-4 text-rose-600 focus:ring-rose-500">
                                <i class="fa-solid fa-hand-holding-dollar text-2xl text-rose-400"></i>
                                <span class="text-[10px] font-black uppercase text-rose-600">Locataires en Dette</span>
                            </label>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Option Broadcast pour Propriétaire/Gestionnaire --}}
                @if(auth()->user()->role === 'proprietaire' || auth()->user()->role === 'gestionnaire')
                <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 mb-8" x-show="isBroadcast">
                    <label class="flex items-center gap-4 cursor-pointer group mb-4">
                        <input type="checkbox" name="is_broadcast" value="1" x-model="isBroadcast" 
                               class="w-6 h-6 rounded-lg border-slate-300 text-rose-600 focus:ring-rose-500">
                        <span class="font-black text-slate-800 uppercase text-xs tracking-widest">Relancer mes Locataires en Dette</span>
                    </label>
                    <input type="hidden" name="broadcast_to" value="tenants_in_debt" x-if="isBroadcast">
                </div>
                @endif

                <div x-show="!isBroadcast">
                    <label class="block text-sm font-black text-slate-700 mb-2">Destinataire</label>
                    <div class="relative">
                        <select name="receiver_id" :required="!isBroadcast"
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
                                  placeholder="Décrivez votre situation..."
                                  class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium shadow-sm resize-none"></textarea>
                    </div>

                    <!-- ATTACHMENT SECTION (WHATSAPP STYLE) -->
                    <div class="mt-4 flex items-center gap-3" x-data="{ open: false, selectedFile: '' }">
                        <div class="relative">
                            <button type="button" @click="open = !open" 
                                    class="w-12 h-12 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                <i class="fa-solid fa-plus transition-transform" :class="open ? 'rotate-45' : ''"></i>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" 
                                 class="absolute bottom-16 left-0 w-48 bg-white rounded-2xl shadow-2xl border border-slate-100 py-2 z-50 overflow-hidden">
                                <label class="flex items-center gap-3 px-6 py-3 hover:bg-emerald-50 text-slate-700 font-bold text-xs uppercase cursor-pointer transition-colors">
                                    <i class="fa-solid fa-image text-emerald-600"></i>
                                    Photos
                                    <input type="file" name="attachment" class="hidden" accept="image/*" @change="selectedFile = $event.target.files[0].name; open = false">
                                </label>
                                <label class="flex items-center gap-3 px-6 py-3 hover:bg-blue-50 text-slate-700 font-bold text-xs uppercase cursor-pointer transition-colors">
                                    <i class="fa-solid fa-file-pdf text-blue-600"></i>
                                    Documents
                                    <input type="file" name="attachment" class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.csv" @change="selectedFile = $event.target.files[0].name; open = false">
                                </label>
                            </div>
                        </div>
                        
                        <template x-if="selectedFile">
                            <div class="flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-100">
                                <i class="fa-solid fa-paperclip"></i>
                                <span x-text="selectedFile"></span>
                                <button type="button" @click="selectedFile = ''; document.getElementsByName('attachment').forEach(i => i.value = '')" class="hover:text-rose-600">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </template>

                        <div class="flex-1 flex items-center gap-4 p-3 bg-slate-50 rounded-2xl border border-slate-200">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" name="is_urgent" value="1" class="w-5 h-5 rounded-lg border-slate-300 text-rose-600 focus:ring-rose-500">
                                <span class="font-black text-slate-700 uppercase text-[10px] tracking-widest">⚠️ Urgent</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4 mt-12 pt-8 border-t border-slate-100">
                <button type="submit"
                        class="px-10 py-4 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 shadow-xl shadow-blue-200 transition-all active:scale-95 flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Envoyer
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
