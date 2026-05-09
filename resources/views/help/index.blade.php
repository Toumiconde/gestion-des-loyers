@extends('layouts.app')

@section('title', 'Centre d\'Assistance & Documentation')

@section('content')

<div class="max-w-4xl mx-auto py-10">

    {{-- Header --}}
    <div class="text-center mb-16">
        <div class="w-24 h-24 bg-blue-600 rounded-[35px] flex items-center justify-center mx-auto mb-8 shadow-2xl shadow-blue-200 animate-bounce">
            <i class="fa-solid fa-headset text-white text-3xl"></i>
        </div>
        <h2 class="text-4xl font-black text-slate-800 mb-4 tracking-tight">Besoin d'un coup de main ?</h2>
        <p class="text-slate-500 text-lg font-medium max-w-xl mx-auto">Explorez nos ressources pour maîtriser votre plateforme GESTLOYER comme un expert.</p>
    </div>

    {{-- SECTION GUIDES DE TÉLÉCHARGEMENT --}}
    <div class="mb-20">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-1.5 h-6 bg-slate-900 rounded-full"></div>
            <h3 class="text-xl font-black text-slate-800 uppercase tracking-widest text-sm">Votre Bibliothèque de Documentation</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Guide Principal du Profil --}}
            <div class="bg-slate-900 rounded-[40px] p-10 text-white relative overflow-hidden group shadow-2xl shadow-slate-200">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-blue-600/10 rounded-full group-hover:scale-110 transition-transform duration-700"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center text-blue-400 mb-6 text-2xl">
                        <i class="fa-solid fa-file-pdf"></i>
                    </div>
                    <h4 class="text-2xl font-black mb-4">Manuel Complet (PDF)</h4>
                    <p class="text-slate-400 text-sm mb-10 leading-relaxed">
                        @if(auth()->user()->isAdmin())
                            Téléchargez le guide intégral pour configurer l'agence, gérer vos gestionnaires et superviser vos finances.
                        @elseif(auth()->user()->isGestionnaire())
                            Le manuel complet pour la gestion quotidienne des loyers, des contrats et du patrimoine.
                        @elseif(auth()->user()->isProprietaire())
                            Manuel du Propriétaire : suivez vos revenus, consultez vos bilans et signez vos quittances numériquement.
                        @else
                            Guide utilisateur locataire : comment gérer vos paiements et signaler vos incidents en un clic.
                        @endif
                    </p>
                    <a href="{{ route('help.downloadGuide', ['role' => auth()->user()->role]) }}" target="_blank"
                       class="inline-flex items-center gap-3 px-8 py-4 bg-white text-slate-900 font-black rounded-2xl hover:bg-blue-600 hover:text-white transition-all shadow-xl">
                        Télécharger le Guide
                        <i class="fa-solid fa-download"></i>
                    </a>
                </div>
            </div>

            {{-- Support & Aide Directe --}}
            <div class="bg-blue-600 rounded-[40px] p-10 text-white relative overflow-hidden group shadow-2xl shadow-blue-200">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-white/10 rounded-full group-hover:scale-110 transition-transform duration-700"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center text-white mb-6 text-2xl">
                        <i class="fa-solid fa-circle-question"></i>
                    </div>
                    <h4 class="text-2xl font-black mb-4">Foire aux Questions</h4>
                    <p class="text-blue-100 text-sm mb-10 leading-relaxed">Parcourez les questions les plus fréquentes pour obtenir des réponses immédiates sur le fonctionnement de la plateforme.</p>
                    <a href="#faq" class="inline-flex items-center gap-3 px-8 py-4 bg-slate-900 text-white font-black rounded-2xl hover:bg-white hover:text-slate-900 transition-all shadow-xl">
                        Voir les questions
                        <i class="fa-solid fa-arrow-down"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- FAQ Section --}}
    <div id="faq" class="space-y-6 mb-20 scroll-mt-24">
        <h3 class="text-xl font-black text-slate-800 flex items-center gap-3 mb-8">
            <i class="fa-solid fa-circle-question text-blue-600"></i>
            Questions Fréquentes
        </h3>

        @foreach($faqs as $index => $faq)
        <div x-data="{ open: false }" class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden transition-all hover:shadow-md">
            <button @click="open = !open" class="w-full px-8 py-6 flex items-center justify-between text-left focus:outline-none">
                <span class="font-black text-slate-700">{{ $faq['question'] }}</span>
                <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-blue-50 transition-all">
                    <i class="fa-solid" :class="open ? 'fa-chevron-up text-blue-600' : 'fa-chevron-down'"></i>
                </div>
            </button>
            <div x-show="open" x-collapse x-cloak class="px-8 pb-8">
                <div class="h-px bg-slate-50 mb-6"></div>
                <p class="text-slate-500 leading-relaxed font-medium">
                    {{ $faq['answer'] }}
                </p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Footer Aide --}}
    @if(auth()->user()->role === 'admin')
    <div class="text-center bg-white rounded-[40px] p-12 border border-slate-100 shadow-sm">
        <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 mx-auto mb-6 text-2xl">
            <i class="fa-solid fa-paper-plane"></i>
        </div>
        <h4 class="text-xl font-black text-slate-800 mb-2">Vous n'avez pas trouvé votre réponse ?</h4>
        <p class="text-slate-400 text-sm mb-8">Notre équipe d'assistance est disponible du Lundi au Samedi pour vous aider.</p>
        <a href="{{ route('messages.index') }}" class="inline-flex items-center justify-center px-10 py-5 bg-slate-900 text-white font-black rounded-2xl hover:bg-blue-600 shadow-xl shadow-slate-200 transition-all">
            Voir les requêtes support
        </a>
    </div>
    @elseif(auth()->user()->role === 'proprietaire')
    <div class="bg-slate-900 rounded-[40px] p-12 text-white shadow-2xl shadow-slate-300">
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center gap-6 mb-10">
                <div class="w-20 h-20 bg-blue-600 rounded-3xl flex items-center justify-center text-3xl shadow-xl shadow-blue-900/20">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <div>
                    <h4 class="text-2xl font-black mb-1">Contacter le Support Technique</h4>
                    <p class="text-slate-400 font-medium">Votre message sera traité par le système et un administrateur vous répondra.</p>
                </div>
            </div>

            <form action="{{ route('messages.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $admin->id ?? '' }}">
                <input type="hidden" name="is_support" value="1">
                
                <div>
                    <label class="block text-sm font-bold text-slate-400 mb-3 uppercase tracking-widest">Description de votre problème</label>
                    <textarea name="content" rows="4" required
                        class="w-full bg-slate-800 border-none rounded-3xl p-6 text-white placeholder-slate-500 focus:ring-2 focus:ring-blue-500 transition-all"
                        placeholder="Expliquez-nous en détail ce qui ne fonctionne pas..."></textarea>
                </div>

                <div class="flex items-center justify-between pt-4">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_urgent" id="urgent" class="w-5 h-5 rounded border-slate-700 bg-slate-800 text-blue-600 focus:ring-blue-500">
                        <label for="urgent" class="text-slate-300 font-bold">Marquer comme urgent</label>
                    </div>
                    <button type="submit" class="px-10 py-5 bg-blue-600 text-white font-black rounded-2xl hover:bg-white hover:text-slate-900 transition-all shadow-xl shadow-blue-900/20">
                        Envoyer au Système
                        <i class="fa-solid fa-paper-plane ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="text-center bg-white rounded-[40px] p-12 border border-slate-100 shadow-sm">
        <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 mx-auto mb-6 text-2xl">
            <i class="fa-solid fa-comments"></i>
        </div>
        <h4 class="text-xl font-black text-slate-800 mb-2">Besoin d'échanger ?</h4>
        <p class="text-slate-400 text-sm mb-8">Pour toute question relative à votre location ou vos paiements, veuillez utiliser la messagerie directe.</p>
        <a href="{{ route('messages.index') }}" class="inline-flex items-center justify-center px-10 py-5 bg-slate-900 text-white font-black rounded-2xl hover:bg-amber-600 shadow-xl shadow-slate-200 transition-all">
            Accéder à mes messages
        </a>
    </div>
    @endif
</div>

@endsection
