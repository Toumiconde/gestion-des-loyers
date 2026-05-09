@extends('layouts.app')

@section('title', 'Nouveau Contrat de Bail')

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
                    <a href="{{ route('contrats.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">Contrats</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-slate-300 mx-2 text-xs"></i>
                    <span class="text-slate-600">Nouveau contrat</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-[#0F172A] p-8 text-white relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/5 rounded-full"></div>
            <div class="relative z-10 flex items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center text-3xl">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-black mb-1">Établir un contrat</h2>
                    <p class="text-slate-400">Définissez les termes du bail entre le propriétaire et le locataire.</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('contrats.store') }}" class="p-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                
                {{-- Parties Prenantes --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-handshake text-blue-600"></i>
                        <h3 class="font-black text-slate-800 uppercase tracking-widest text-sm">Parties & Bien</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Bien immobilier concerné</label>
                        <div class="relative">
                            <select name="bien_id" required
                                    class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-semibold">
                                <option value="">-- Choisir un bien --</option>
                                @foreach($biens as $bien)
                                <option value="{{ $bien->id }}" {{ old('bien_id') == $bien->id ? 'selected' : '' }}>
                                    {{ $bien->libelle }} — {{ number_format($bien->loyer_base, 0, ',', ' ') }} GNF
                                </option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-building absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Locataire</label>
                        <div class="relative">
                            <select name="locataire_id" required
                                    class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-semibold">
                                <option value="">-- Choisir un locataire --</option>
                                @foreach($locataires as $l)
                                <option value="{{ $l->id }}" {{ old('locataire_id') == $l->id ? 'selected' : '' }}>
                                    {{ $l->prenom }} {{ $l->nom }}
                                </option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2">Date de début</label>
                            <input type="date" name="date_debut" value="{{ old('date_debut', date('Y-m-d')) }}" required
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-semibold">
                        </div>
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2">Durée (mois)</label>
                            <input type="number" name="duree_mois" value="{{ old('duree_mois', 12) }}"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-semibold">
                        </div>
                    </div>
                </div>

                {{-- Conditions Financières --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-file-invoice-dollar text-emerald-600"></i>
                        <h3 class="font-black text-slate-800 uppercase tracking-widest text-sm">Conditions</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2">Loyer Mensuel (GNF)</label>
                            <div class="relative">
                                <input type="number" name="loyer" value="{{ old('loyer') }}" required
                                       class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all font-bold text-emerald-600">
                                <i class="fa-solid fa-money-bill-1 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2">Garantie (GNF)</label>
                            <div class="relative">
                                <input type="number" name="depot_garantie" value="{{ old('depot_garantie') }}" required
                                       class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all font-bold text-emerald-600">
                                <i class="fa-solid fa-shield-halved absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2">Jour d'échéance</label>
                            <input type="number" name="jour_echeance" value="{{ old('jour_echeance', 5) }}" min="1" max="28" required
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-semibold">
                        </div>
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2">Révision annuelle (%)</label>
                            <input type="number" name="taux_revision" value="{{ old('taux_revision', 0) }}" step="0.01"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-semibold">
                        </div>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100 flex gap-4">
                        <i class="fa-solid fa-circle-check text-blue-600 mt-1"></i>
                        <p class="text-xs text-blue-800 leading-relaxed">
                            En créant ce contrat, le statut du bien passera automatiquement à <strong>"Occupé"</strong>.
                        </p>
                    </div>
                </div>

            </div>

            <div class="flex items-center gap-4 mt-12 pt-8 border-t border-slate-100">
                <button type="submit"
                        class="px-10 py-4 bg-[#0F172A] text-white font-black rounded-2xl hover:bg-slate-800 shadow-xl shadow-slate-200 transition-all active:scale-95">
                    Générer le contrat définitif
                </button>
                <a href="{{ route('contrats.index') }}"
                   class="px-10 py-4 bg-slate-100 text-slate-600 font-bold rounded-2xl hover:bg-slate-200 transition-all">
                    Annuler
                </a>
            </div>

        </form>
    </div>
</div>

@endsection