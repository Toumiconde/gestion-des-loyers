@extends('layouts.app')

@section('title', 'Recruter un Gestionnaire')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('staff.index') }}" class="text-xs font-black text-slate-400 uppercase tracking-widest hover:text-indigo-600 transition-colors flex items-center gap-2 mb-4">
            <i class="fa-solid fa-arrow-left"></i> Retour à la liste
        </a>
        <h2 class="text-3xl font-black text-slate-800">Nouveau Recrutement</h2>
        <p class="text-slate-500 font-medium">Créez un compte pour votre futur collaborateur</p>
    </div>

    <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 p-10">
        <form action="{{ route('staff.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom complet</label>
                <input type="text" name="name" required value="{{ old('name') }}"
                       placeholder="Ex: Jean Martin"
                       class="w-full bg-slate-50 border-none h-14 px-6 rounded-2xl text-slate-700 font-bold focus:ring-4 focus:ring-indigo-100 transition-all outline-none">
                @error('name') <p class="text-rose-500 text-xs font-bold">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Email professionnel</label>
                <input type="email" name="email" required value="{{ old('email') }}"
                       placeholder="jean.martin@agence.com"
                       class="w-full bg-slate-50 border-none h-14 px-6 rounded-2xl text-slate-700 font-bold focus:ring-4 focus:ring-indigo-100 transition-all outline-none">
                @error('email') <p class="text-rose-500 text-xs font-bold">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mot de passe provisoire</label>
                    <input type="password" name="password" required
                           class="w-full bg-slate-50 border-none h-14 px-6 rounded-2xl text-slate-700 font-bold focus:ring-4 focus:ring-indigo-100 transition-all outline-none">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Confirmer</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full bg-slate-50 border-none h-14 px-6 rounded-2xl text-slate-700 font-bold focus:ring-4 focus:ring-indigo-100 transition-all outline-none">
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full h-16 bg-slate-900 text-white rounded-2xl font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200 group">
                    Valider le recrutement
                    <i class="fa-solid fa-paper-plane ml-2 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="mt-10 p-8 bg-indigo-50 rounded-[30px] border border-indigo-100 flex gap-6">
        <div class="w-12 h-12 shrink-0 rounded-2xl bg-white flex items-center justify-center text-indigo-600 shadow-sm">
            <i class="fa-solid fa-shield-halved text-xl"></i>
        </div>
        <div>
            <h4 class="font-black text-indigo-900 mb-1">Sécurité des accès</h4>
            <p class="text-xs text-indigo-700/70 leading-relaxed">Le gestionnaire pourra gérer les locataires, les paiements et les incidents, mais il n'aura pas accès aux paramètres critiques de l'agence ni à la suppression du compte administrateur.</p>
        </div>
    </div>
</div>

@endsection
