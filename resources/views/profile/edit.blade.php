@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')

<div class="max-w-4xl mx-auto py-8">
    
    {{-- En-tête du profil --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 mb-8 flex flex-col md:flex-row items-center gap-8">
        <div class="relative group">
            @if(auth()->user()->photo)
                <img src="{{ asset('storage/' . auth()->user()->photo) }}" 
                     class="w-32 h-32 rounded-3xl object-cover shadow-lg border-4 border-white">
            @else
                <div class="w-32 h-32 rounded-3xl bg-blue-100 flex items-center justify-center text-blue-600 text-4xl font-black shadow-lg">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            @endif
        </div>
        
        <div class="text-center md:text-left">
            <h2 class="text-3xl font-black text-slate-800">{{ auth()->user()->name }}</h2>
            <p class="text-slate-500 font-medium">{{ auth()->user()->email }}</p>
            <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-2">
                <span class="px-4 py-1.5 rounded-full bg-blue-50 text-blue-600 text-xs font-black uppercase tracking-widest">
                    {{ auth()->user()->role }}
                </span>
                <span class="px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-600 text-xs font-black uppercase tracking-widest">
                    Compte Actif
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8">
        {{-- Formulaire d'informations --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i class="fa-solid fa-user-gear text-blue-600"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800">Informations Personnelles</h3>
            </div>
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- CABINET PROPRIÉTAIRE (Signature & RIB) --}}
        @if(auth()->user()->role === 'proprietaire' && auth()->user()->proprietaire)
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center">
                    <i class="fa-solid fa-briefcase text-indigo-600"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800">Cabinet de Gestion (Signature & RIB)</h3>
            </div>
            @include('profile.partials.update-owner-cabinet-form')
        </div>
        @endif

        {{-- Formulaire de mot de passe --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                    <i class="fa-solid fa-lock text-amber-600"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800">Sécurité du compte</h3>
            </div>
            @include('profile.partials.update-password-form')
        </div>

        {{-- Suppression du compte --}}
        @if(auth()->user()->role !== 'admin')
        <div class="bg-rose-50 rounded-3xl p-8 border border-rose-100">
            <div class="flex items-center gap-3 mb-6 text-rose-800">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <h3 class="text-lg font-black uppercase tracking-widest">Zone de danger</h3>
            </div>
            @include('profile.partials.delete-user-form')
        </div>
        @endif
    </div>
</div>

@endsection
