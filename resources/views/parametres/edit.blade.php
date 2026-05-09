@extends('layouts.app')

@section('title', 'Modifier les Paramètres')

@section('content')

<div class="max-w-5xl mx-auto py-8">
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
                    <a href="{{ route('parametres.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">Paramètres</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-slate-300 mx-2 text-xs"></i>
                    <span class="text-slate-600">Modification</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-blue-600 p-8 text-white relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full"></div>
            <div class="relative z-10">
                <h2 class="text-3xl font-black mb-1">Configuration de l'Agence</h2>
                <p class="text-blue-100 italic">Mettez à jour votre identité visuelle et vos coordonnées.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('parametres.update') }}" enctype="multipart/form-data" x-data="{ tab: 'agence' }">
            @csrf

            <div class="flex flex-col lg:flex-row gap-10 p-8">
                
                {{-- Menu Interne --}}
                <div class="w-full lg:w-64 space-y-2 border-r border-slate-100 pr-6">
                    <button type="button" @click="tab = 'agence'" :class="tab === 'agence' ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50'" class="w-full text-left px-5 py-3 rounded-xl font-bold transition-all text-sm">
                        <i class="fa-solid fa-building mr-2"></i> Agence
                    </button>
                    <button type="button" @click="tab = 'docs'" :class="tab === 'docs' ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50'" class="w-full text-left px-5 py-3 rounded-xl font-bold transition-all text-sm">
                        <i class="fa-solid fa-file-pdf mr-2"></i> Documentation
                    </button>
                    <button type="button" @click="tab = 'notifications'" :class="tab === 'notifications' ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50'" class="w-full text-left px-5 py-3 rounded-xl font-bold transition-all text-sm">
                        <i class="fa-solid fa-bell mr-2"></i> Alertes
                    </button>
                    <button type="button" @click="tab = 'fiscalite'" :class="tab === 'fiscalite' ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50'" class="w-full text-left px-5 py-3 rounded-xl font-bold transition-all text-sm">
                        <i class="fa-solid fa-file-invoice-dollar mr-2"></i> Fiscalité
                    </button>
                    <button type="button" @click="tab = 'securite'" :class="tab === 'securite' ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50'" class="w-full text-left px-5 py-3 rounded-xl font-bold transition-all text-sm">
                        <i class="fa-solid fa-shield-halved mr-2"></i> Sécurité
                    </button>
                </div>

                {{-- Champs --}}
                <div class="flex-1">
                    
                    {{-- SECTION AGENCE --}}
                    <div x-show="tab === 'agence'" class="space-y-6 animate-fade-in">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Nom de l'Agence</label>
                                <input type="text" name="nom_agence" value="{{ old('nom_agence', $settings['nom_agence'] ?? '') }}" class="w-full bg-slate-50 border border-slate-200 py-3 px-4 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Email Contact</label>
                                <input type="email" name="email_contact" value="{{ old('email_contact', $settings['email_contact'] ?? '') }}" class="w-full bg-slate-50 border border-slate-200 py-3 px-4 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Téléphone</label>
                                <input type="text" name="telephone" value="{{ old('telephone', $settings['telephone'] ?? '') }}" class="w-full bg-slate-50 border border-slate-200 py-3 px-4 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Devise (ex: GNF, EUR)</label>
                                <input type="text" name="devise" value="{{ old('devise', $settings['devise'] ?? 'GNF') }}" class="w-full bg-slate-50 border border-slate-200 py-3 px-4 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Adresse</label>
                            <textarea name="adresse" rows="2" class="w-full bg-slate-50 border border-slate-200 py-3 px-4 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold">{{ old('adresse', $settings['adresse'] ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Logo de l'agence</label>
                            <input type="file" name="logo" class="w-full p-2 border border-dashed border-slate-300 rounded-xl mb-4">
                            
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Cachet Officiel / Signature de l'agence</label>
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-xl bg-slate-50 flex items-center justify-center border border-slate-200 overflow-hidden">
                                    @if(!empty($settings['signature']))
                                        <img src="{{ Storage::url($settings['signature']) }}" class="w-full h-full object-contain p-1">
                                    @else
                                        <i class="fa-solid fa-stamp text-slate-300"></i>
                                    @endif
                                </div>
                                <input type="file" name="signature" class="flex-1 p-2 border border-dashed border-slate-300 rounded-xl">
                            </div>
                        </div>
                    </div>

                    {{-- SECTION DOCUMENTATION --}}
                    <div x-show="tab === 'docs'" class="space-y-8 animate-fade-in" x-cloak>
                        <div class="bg-blue-50 p-6 rounded-3xl border border-blue-100 flex gap-4 mb-6">
                            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-blue-600 shadow-sm shrink-0">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>
                            <p class="text-xs text-blue-700 leading-relaxed">Téléchargez ici vos propres manuels d'utilisation au format PDF. Ces fichiers remplaceront les guides par défaut sur le dashboard des utilisateurs.</p>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            {{-- Guide Admin --}}
                            <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-black text-slate-800">Guide Complet Administrateur</h4>
                                    @if(!empty($settings['guide_admin']))
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-black uppercase">Fichier Présent</span>
                                    @else
                                        <span class="px-3 py-1 bg-slate-100 text-slate-400 rounded-full text-[10px] font-black uppercase">Aucun fichier</span>
                                    @endif
                                </div>
                                <input type="file" name="guide_admin" accept=".pdf" class="w-full p-2 border border-dashed border-slate-300 rounded-xl text-xs">
                            </div>

                            {{-- Guide Gestionnaire --}}
                            <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-black text-slate-800">Guide pour les Gestionnaires</h4>
                                    @if(!empty($settings['guide_gestionnaire']))
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-black uppercase">Fichier Présent</span>
                                    @else
                                        <span class="px-3 py-1 bg-slate-100 text-slate-400 rounded-full text-[10px] font-black uppercase">Aucun fichier</span>
                                    @endif
                                </div>
                                <input type="file" name="guide_gestionnaire" accept=".pdf" class="w-full p-2 border border-dashed border-slate-300 rounded-xl text-xs">
                            </div>

                            {{-- Guide Locataire --}}
                            <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-black text-slate-800">Guide pour les Locataires</h4>
                                    @if(!empty($settings['guide_locataire']))
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-black uppercase">Fichier Présent</span>
                                    @else
                                        <span class="px-3 py-1 bg-slate-100 text-slate-400 rounded-full text-[10px] font-black uppercase">Aucun fichier</span>
                                    @endif
                                </div>
                                <input type="file" name="guide_locataire" accept=".pdf" class="w-full p-2 border border-dashed border-slate-300 rounded-xl text-xs">
                            </div>
                        </div>
                    </div>

                    {{-- SECTION NOTIFICATIONS --}}
                    <div x-show="tab === 'notifications'" class="space-y-8 animate-fade-in" x-cloak>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Seuil de retard (jours)</label>
                            <input type="number" name="seuil_retard" value="{{ old('seuil_retard', $settings['seuil_retard'] ?? '5') }}" class="w-full bg-slate-50 border border-slate-200 py-3 px-4 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                                        <i class="fa-solid fa-envelope"></i>
                                    </div>
                                    <h4 class="font-black text-slate-800">Alertes par Email</h4>
                                </div>
                                <div class="flex bg-slate-100 p-1 rounded-xl">
                                    <label class="flex-1">
                                        <input type="radio" name="alerte_email" value="on" {{ ($settings['alerte_email'] ?? 'on') === 'on' ? 'checked' : '' }} class="hidden peer">
                                        <div class="text-center py-2 rounded-lg cursor-pointer transition-all font-black text-xs uppercase peer-checked:bg-blue-600 peer-checked:text-white text-slate-500">Activer</div>
                                    </label>
                                    <label class="flex-1">
                                        <input type="radio" name="alerte_email" value="off" {{ ($settings['alerte_email'] ?? 'on') === 'off' ? 'checked' : '' }} class="hidden peer">
                                        <div class="text-center py-2 rounded-lg cursor-pointer transition-all font-black text-xs uppercase peer-checked:bg-slate-400 peer-checked:text-white text-slate-500">Désactiver</div>
                                    </label>
                                </div>
                            </div>

                            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                                        <i class="fa-solid fa-comment-sms"></i>
                                    </div>
                                    <h4 class="font-black text-slate-800">Alertes par SMS</h4>
                                </div>
                                <div class="flex bg-slate-100 p-1 rounded-xl">
                                    <label class="flex-1">
                                        <input type="radio" name="alerte_sms" value="on" {{ ($settings['alerte_sms'] ?? 'off') === 'on' ? 'checked' : '' }} class="hidden peer">
                                        <div class="text-center py-2 rounded-lg cursor-pointer transition-all font-black text-xs uppercase peer-checked:bg-emerald-500 peer-checked:text-white text-slate-500">Activer</div>
                                    </label>
                                    <label class="flex-1">
                                        <input type="radio" name="alerte_sms" value="off" {{ ($settings['alerte_sms'] ?? 'off') === 'off' ? 'checked' : '' }} class="hidden peer">
                                        <div class="text-center py-2 rounded-lg cursor-pointer transition-all font-black text-xs uppercase peer-checked:bg-slate-400 peer-checked:text-white text-slate-500">Désactiver</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION FISCALITÉ --}}
                    <div x-show="tab === 'fiscalite'" class="space-y-6 animate-fade-in" x-cloak>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Taux TVA par défaut (%)</label>
                            <input type="number" step="0.01" name="tva_taux" value="{{ old('tva_taux', $settings['tva_taux'] ?? '0') }}" class="w-full bg-slate-50 border border-slate-200 py-3 px-4 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Préfixe Quittance</label>
                            <input type="text" name="format_quittance" value="{{ old('format_quittance', $settings['format_quittance'] ?? 'QUI-') }}" class="w-full bg-slate-50 border border-slate-200 py-3 px-4 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                        </div>
                    </div>

                    {{-- SECTION SÉCURITÉ --}}
                    <div x-show="tab === 'securite'" class="space-y-6 animate-fade-in" x-cloak>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Durée de session (minutes)</label>
                            <input type="number" name="expiration_session" value="{{ old('expiration_session', $settings['expiration_session'] ?? '120') }}" class="w-full bg-slate-50 border border-slate-200 py-3 px-4 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                        </div>
                    </div>

                </div>
            </div>

            <div class="flex items-center gap-4 mt-12 pt-8 border-t border-slate-100">
                <button type="submit"
                        class="px-10 py-4 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 shadow-xl shadow-blue-200 transition-all active:scale-95">
                    Enregistrer la configuration
                </button>
                <a href="{{ route('parametres.index') }}"
                   class="px-10 py-4 bg-slate-100 text-slate-600 font-bold rounded-2xl hover:bg-slate-200 transition-all">
                    Annuler
                </a>
            </div>

        </form>
    </div>
</div>

@endsection