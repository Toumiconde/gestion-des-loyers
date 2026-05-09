@extends('layouts.app')

@section('title', 'Envoyer une relance')

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
                    <a href="{{ route('relances.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">Relances</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-slate-300 mx-2 text-xs"></i>
                    <span class="text-slate-600">Nouvelle relance</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-indigo-600 p-8 text-white relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full"></div>
            <div class="relative z-10 flex items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center text-3xl">
                    <i class="fa-solid fa-paper-plane"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-black mb-1">Envoyer une relance</h2>
                    <p class="text-indigo-100 italic">Rappelez au locataire ses obligations de paiement.</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('relances.store') }}" class="p-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                
                {{-- Destinataire --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-user-clock text-indigo-600"></i>
                        <h3 class="font-black text-slate-800 uppercase tracking-widest text-sm">Destinataire</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Contrat / Locataire en retard</label>
                        <div class="relative">
                            <select name="contrat_id" required
                                    class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all font-semibold cursor-pointer">
                                <option value="">-- Choisir un contrat --</option>
                                @foreach($contrats as $c)
                                <option value="{{ $c->id }}" {{ old('contrat_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->locataire->nom }} — {{ $c->bien->libelle }}
                                </option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-file-contract absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Type de relance</label>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach(['email' => 'Email', 'courrier' => 'Courrier Papier'] as $val => $label)
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="{{ $val }}" class="peer hidden" {{ old('type', 'email') == $val ? 'checked' : '' }}>
                                <div class="text-center py-3 rounded-xl border border-slate-200 bg-slate-50 text-xs font-black text-slate-500 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-600 transition-all">
                                    {{ $label }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Message --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-comment-dots text-purple-600"></i>
                        <h3 class="font-black text-slate-800 uppercase tracking-widest text-sm">Communication</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Niveau de rappel</label>
                        <select name="statut"
                                class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all font-semibold cursor-pointer">
                            <option value="envoye">Premier Rappel (Amiable)</option>
                            <option value="en_attente">Mise en demeure (Ferme)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Notes ou Message personnalisé</label>
                        <textarea name="message" rows="3"
                                  placeholder="Détails supplémentaires ou message spécifique..."
                                  class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all">{{ old('message') }}</textarea>
                    </div>
                </div>

            </div>

            <div class="flex items-center gap-4 mt-12 pt-8 border-t border-slate-100">
                <button type="submit"
                        class="px-10 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 shadow-xl shadow-indigo-200 transition-all active:scale-95">
                    Générer & Envoyer la relance
                </button>
                <a href="{{ route('relances.index') }}"
                   class="px-10 py-4 bg-slate-100 text-slate-600 font-bold rounded-2xl hover:bg-slate-200 transition-all">
                    Annuler
                </a>
            </div>

        </form>
    </div>
</div>

@endsection
