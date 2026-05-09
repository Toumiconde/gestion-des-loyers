@extends('layouts.app')

@section('title', 'Modifier Locataire')

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
                    <a href="{{ route('locataires.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">Locataires</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-slate-300 mx-2 text-xs"></i>
                    <span class="text-slate-600">Modifier le locataire</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-700 p-8 text-white relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full"></div>
            <div class="relative z-10">
                <h2 class="text-3xl font-black mb-2">Modifier le locataire</h2>
                <p class="text-purple-100 italic">{{ $locataire->prenom }} {{ $locataire->nom }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('locataires.update', $locataire) }}" class="p-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                {{-- Identité --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-user-circle text-purple-600"></i>
                        <h3 class="font-black text-slate-800 uppercase tracking-widest text-sm">Identité</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2">Prénom</label>
                            <input type="text" name="prenom" value="{{ old('prenom', $locataire->prenom) }}"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all font-semibold"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2">Nom</label>
                            <input type="text" name="nom" value="{{ old('nom', $locataire->nom) }}"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all font-semibold"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">N° Pièce d'identité</label>
                        <div class="relative">
                            <input type="text" name="piece_identite" value="{{ old('piece_identite', $locataire->piece_identite) }}"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all font-semibold">
                            <i class="fa-solid fa-id-card absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>
                </div>

                {{-- Contact --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-address-book text-emerald-600"></i>
                        <h3 class="font-black text-slate-800 uppercase tracking-widest text-sm">Contact</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Adresse Email</label>
                        <div class="relative">
                            <input type="email" name="email" value="{{ old('email', $locataire->email) }}"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all font-semibold">
                            <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Téléphone</label>
                        <div class="relative">
                            <input type="text" name="telephone" value="{{ old('telephone', $locataire->telephone) }}"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all font-semibold">
                            <i class="fa-solid fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Adresse actuelle</label>
                        <div class="relative">
                            <textarea name="adresse" rows="1"
                                      class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all font-semibold">{{ old('adresse', $locataire->adresse) }}</textarea>
                            <i class="fa-solid fa-map-location-dot absolute left-4 top-4 text-slate-400"></i>
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex items-center gap-4 mt-12 pt-8 border-t border-slate-100">
                <button type="submit"
                        class="px-10 py-4 bg-purple-600 text-white font-black rounded-2xl hover:bg-purple-700 shadow-xl shadow-purple-200 transition-all active:scale-95">
                    Mettre à jour le locataire
                </button>
                <a href="{{ route('locataires.index') }}"
                   class="px-10 py-4 bg-slate-100 text-slate-600 font-bold rounded-2xl hover:bg-slate-200 transition-all">
                    Annuler
                </a>
            </div>

        </form>
    </div>
</div>

@endsection