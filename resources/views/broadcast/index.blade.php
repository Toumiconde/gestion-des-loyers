@extends('layouts.app')

@section('title', 'Diffusion de Masse (Broadcast)')

@section('content')

<div class="max-w-4xl mx-auto">
    <div class="mb-10">
        <h2 class="text-3xl font-black text-slate-800">Centre de Diffusion</h2>
        <p class="text-slate-500 font-medium">Envoyez des messages groupés par SMS, Email ou Messagerie Interne</p>
    </div>

    <form action="{{ route('broadcast.send') }}" method="POST" class="space-y-8">
        @csrf
        
        <div class="bg-white rounded-[40px] p-10 shadow-sm border border-slate-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                
                {{-- Destinataires --}}
                <div class="space-y-6">
                    <label class="block">
                        <span class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3 block italic">Groupe Cible :</span>
                        <select name="target" class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="all_tenants">Tous les Locataires {{ auth()->user()->isProprietaire() ? 'de mes biens' : '' }}</option>
                            @if(auth()->user()->isAdmin() || auth()->user()->role === 'gestionnaire')
                                <option value="all_owners">Tous les Propriétaires</option>
                            @endif
                            <option value="unpaid_tenants" class="text-rose-600">🚨 Locataires en retard (Paiement du mois)</option>
                        </select>
                    </label>

                    <div>
                        <span class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3 block italic">Canaux d'envoi :</span>
                        <div class="grid grid-cols-1 gap-3">
                            <label class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 cursor-pointer hover:bg-blue-50 transition-all group">
                                <input type="checkbox" name="channel[]" value="internal" checked class="w-5 h-5 rounded-lg border-slate-300 text-blue-600 focus:ring-blue-500">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-comment-dots text-blue-500"></i>
                                    <span class="font-bold text-slate-700">Messagerie Interne</span>
                                </div>
                            </label>
                            <label class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 cursor-pointer hover:bg-blue-50 transition-all group">
                                <input type="checkbox" name="channel[]" value="email" class="w-5 h-5 rounded-lg border-slate-300 text-blue-600 focus:ring-blue-500">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-envelope text-amber-500"></i>
                                    <span class="font-bold text-slate-700">E-mail Professionnel</span>
                                </div>
                            </label>
                            <label class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 cursor-pointer hover:bg-blue-50 transition-all group">
                                <input type="checkbox" name="channel[]" value="sms" class="w-5 h-5 rounded-lg border-slate-300 text-blue-600 focus:ring-blue-500">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-mobile-screen text-emerald-500"></i>
                                    <span class="font-bold text-slate-700">Alerte SMS Directe</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Contenu --}}
                <div class="space-y-6">
                    <label class="block">
                        <span class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3 block italic">Objet du message :</span>
                        <input type="text" name="subject" required placeholder="Ex: Rappel de paiement, Annonce importante..." 
                               class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </label>

                    <label class="block">
                        <span class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3 block italic">Corps du message :</span>
                        <textarea name="content" required rows="8" placeholder="Rédigez votre message ici..."
                                  class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none transition-all"></textarea>
                    </label>
                </div>

            </div>

            <div class="mt-10 pt-10 border-t border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3 text-slate-400 italic">
                    <i class="fa-solid fa-circle-info"></i>
                    <p class="text-xs">Chaque destinataire recevra un message personnalisé.</p>
                </div>
                <button type="submit" class="inline-flex items-center justify-center px-10 py-5 bg-blue-600 text-white font-black rounded-3xl hover:bg-blue-700 shadow-xl shadow-blue-200 transition-all active:scale-95 group">
                    <i class="fa-solid fa-paper-plane mr-3 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                    Lancer la Diffusion
                </button>
            </div>
        </div>
    </form>

    {{-- Conseils --}}
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-6 bg-amber-50 rounded-3xl border border-amber-100">
            <i class="fa-solid fa-clock-rotate-left text-amber-600 mb-3 block text-xl"></i>
            <h4 class="font-black text-slate-800 text-sm mb-2">Relances Automatiques</h4>
            <p class="text-xs text-slate-600 leading-relaxed">Le groupe "Locataires en retard" cible uniquement ceux qui n'ont pas validé leur paiement du mois actuel.</p>
        </div>
        <div class="p-6 bg-blue-50 rounded-3xl border border-blue-100">
            <i class="fa-solid fa-shield-halved text-blue-600 mb-3 block text-xl"></i>
            <h4 class="font-black text-slate-800 text-sm mb-2">Sécurité des envois</h4>
            <p class="text-xs text-slate-600 leading-relaxed">Les messages groupés sont envoyés de manière individuelle. Personne ne verra la liste des autres destinataires.</p>
        </div>
        <div class="p-6 bg-emerald-50 rounded-3xl border border-emerald-100">
            <i class="fa-solid fa-bolt-lightning text-emerald-600 mb-3 block text-xl"></i>
            <h4 class="font-black text-slate-800 text-sm mb-2">Diffusion instantanée</h4>
            <p class="text-xs text-slate-600 leading-relaxed">Les SMS et Emails sont envoyés immédiatement après votre validation.</p>
        </div>
    </div>
</div>

@endsection
