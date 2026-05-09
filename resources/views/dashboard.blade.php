@extends('layouts.app')

@section('title', 'Pilotage Global')

@section('content')

<div class="max-w-7xl mx-auto py-8">
    
    @if(auth()->user()->role === 'locataire')
        @php $lData = $stats['locataire_data'] ?? null; @endphp
        
        @if($lData)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            {{-- Colonne Logement --}}
            <div class="lg:col-span-2 space-y-10">
                <div class="bg-white rounded-[50px] p-12 border border-slate-100 shadow-sm relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-64 h-64 bg-emerald-50 rounded-bl-full -mr-20 -mt-20 group-hover:bg-emerald-100 transition-colors"></div>
                    <div class="relative z-10 flex flex-col md:flex-row items-center gap-10">
                        <div class="w-48 h-48 rounded-[40px] overflow-hidden border-4 border-white shadow-2xl">
                            <img src="{{ $lData['bien']->photo ? Storage::url($lData['bien']->photo) : 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&q=80&w=300' }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <p class="text-xs font-black text-emerald-600 uppercase tracking-widest mb-2">Bienvenue chez vous</p>
                            <h2 class="text-4xl font-black text-slate-800 mb-4">{{ $lData['bien']->libelle }}</h2>
                            <p class="text-slate-500 font-medium mb-6"><i class="fa-solid fa-location-dot mr-2"></i> {{ $lData['bien']->ville }}, {{ $lData['bien']->quartier }}</p>
                            <div class="inline-flex items-center gap-4 px-6 py-3 bg-slate-900 text-white rounded-2xl font-black text-sm">
                                <i class="fa-solid fa-receipt text-emerald-400"></i>
                                Loyer Mensuel : {{ number_format($lData['contrat']->loyer, 0, ',', ' ') }} GNF
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Suivi des incidents --}}
                <div class="bg-white rounded-[50px] p-12 border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-center mb-10">
                        <h3 class="text-2xl font-black text-slate-800">Mes Demandes d'Intervention</h3>
                        <a href="{{ route('incidents.create') }}" class="px-6 py-2 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all">Nouveau Signalement</a>
                    </div>
                    
                    <div class="space-y-6">
                        @forelse($lData['incidents'] as $incident)
                        <div class="flex items-center justify-between p-6 bg-slate-50 rounded-3xl border border-slate-100 group hover:border-blue-200 transition-all">
                            <div class="flex items-center gap-6">
                                <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-slate-400 group-hover:text-blue-600 shadow-sm">
                                    <i class="fa-solid {{ $incident->statut === 'resolu' ? 'fa-circle-check text-emerald-500' : 'fa-screwdriver-wrench' }}"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">{{ $incident->objet }}</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase">{{ $incident->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest
                                    {{ $incident->statut === 'resolu' ? 'bg-emerald-100 text-emerald-700' : ($incident->statut === 'en_cours' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ $incident->statut }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-10 opacity-40">
                            <i class="fa-solid fa-mug-hot text-4xl mb-4"></i>
                            <p class="font-bold italic">Aucun incident signalé. Tout va bien !</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Actions Rapides --}}
            <div class="space-y-8">
                {{-- Boutons d'Action --}}
                <div class="bg-slate-900 rounded-[50px] p-10 text-white shadow-2xl shadow-slate-200">
                    <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest mb-10 text-center">Services Locataire</h3>
                    
                    <div class="space-y-4">
                        <a href="{{ route('paiements.create') }}" class="flex items-center gap-4 w-full p-6 bg-white/5 rounded-3xl border border-white/10 hover:bg-emerald-600 hover:border-emerald-500 transition-all group">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-xl group-hover:bg-white group-hover:text-emerald-600">
                                <i class="fa-solid fa-money-bill-transfer"></i>
                            </div>
                            <div class="text-left">
                                <p class="font-black text-sm">Déclarer un paiement</p>
                                <p class="text-[10px] text-slate-400 group-hover:text-emerald-100 font-bold">Envoyer votre reçu</p>
                            </div>
                        </a>

                        @if($lData['bail_doc'])
                        <a href="{{ Storage::url($lData['bail_doc']->chemin) }}" download class="flex items-center gap-4 w-full p-6 bg-white/5 rounded-3xl border border-white/10 hover:bg-blue-600 hover:border-blue-500 transition-all group">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-xl group-hover:bg-white group-hover:text-blue-600">
                                <i class="fa-solid fa-file-pdf"></i>
                            </div>
                            <div class="text-left">
                                <p class="font-black text-sm">Mon Contrat de Bail</p>
                                <p class="text-[10px] text-slate-400 group-hover:text-blue-100 font-bold">Télécharger en PDF</p>
                            </div>
                        </a>
                        @endif

                        <a href="{{ route('incidents.create') }}" class="flex items-center gap-4 w-full p-6 bg-white/5 rounded-3xl border border-white/10 hover:bg-rose-600 hover:border-rose-500 transition-all group">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-xl group-hover:bg-white group-hover:text-rose-600">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div class="text-left">
                                <p class="font-black text-sm">Signaler une panne</p>
                                <p class="text-[10px] text-slate-400 group-hover:text-rose-100 font-bold">Maintenance & Réparations</p>
                            </div>
                        </a>

                        <a href="{{ route('help.locataireGuide') }}" class="flex items-center gap-4 w-full p-6 bg-indigo-600 rounded-3xl border border-indigo-500 hover:bg-white hover:text-indigo-600 transition-all group">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-xl group-hover:bg-indigo-600 group-hover:text-white">
                                <i class="fa-solid fa-circle-question"></i>
                            </div>
                            <div class="text-left text-white group-hover:text-indigo-600">
                                <p class="font-black text-sm">Besoin d'aide ?</p>
                                <p class="text-[10px] opacity-70 font-bold uppercase">Guide d'utilisation</p>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- Prochain Loyer --}}
                <div class="bg-emerald-50 rounded-[50px] p-10 border border-emerald-100">
                    <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-4">Prochain Loyer dû</p>
                    <h4 class="text-2xl font-black text-slate-800">
                        {{ \Carbon\Carbon::parse($lData['contrat']->date_debut)->day }} {{ $stats['months'][date('n')] }}
                    </h4>
                    <p class="text-xs text-slate-500 font-medium mt-2">Votre échéance mensuelle est le {{ \Carbon\Carbon::parse($lData['contrat']->date_debut)->day }} de chaque mois.</p>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white rounded-[50px] p-20 text-center border border-slate-100 shadow-sm">
            <i class="fa-solid fa-file-contract text-6xl text-slate-100 mb-8"></i>
            <h2 class="text-3xl font-black text-slate-800 mb-4">Aucun contrat actif</h2>
            <p class="text-slate-400 font-medium max-w-md mx-auto">Vous n'avez pas encore de contrat de bail enregistré dans notre système. Contactez votre agence pour plus d'informations.</p>
        </div>
        @endif

    @else
        {{-- FILTRES (ADMIN & PROPRIO) --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
            <div class="flex items-center gap-4 bg-white p-2 rounded-[30px] shadow-sm border border-slate-100">
                <form action="{{ route('dashboard') }}" method="GET" class="flex items-center">
                    <select name="year" onchange="this.form.submit()" class="bg-transparent border-0 font-black text-slate-800 focus:ring-0 cursor-pointer px-6">
                        @foreach($stats['years'] as $y)
                            <option value="{{ $y }}" {{ $stats['selected_year'] == $y ? 'selected' : '' }}>Année {{ $y }}</option>
                        @endforeach
                    </select>
                    <div class="w-px h-6 bg-slate-200 mx-2"></div>
                    <select name="month" onchange="this.form.submit()" class="bg-transparent border-0 font-black text-slate-800 focus:ring-0 cursor-pointer px-6">
                        <option value="">Tous les mois</option>
                        @foreach($stats['months'] as $num => $name)
                            <option value="{{ $num }}" {{ $stats['selected_month'] == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'proprietaire')
            <a href="{{ route('reports.monthly', ['year' => $stats['selected_year'], 'month' => $stats['selected_month'] ?: date('n')]) }}" 
               class="px-8 py-4 bg-slate-900 text-white rounded-[25px] font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-200 flex items-center gap-3">
                <i class="fa-solid fa-file-pdf"></i>
                Exporter le bilan
            </a>
            @endif
        </div>

        {{-- KIPS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6 mb-12">
            {{-- Revenus --}}
            <div class="bg-white rounded-[40px] p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Revenus</p>
                <h3 class="text-xl font-black text-blue-600">{{ number_format($stats['period']['revenus'], 0, ',', ' ') }}</h3>
                <p class="text-[8px] text-slate-400 font-bold uppercase">GNF Encaissés</p>
            </div>

            {{-- Charges --}}
            <div class="bg-white rounded-[40px] p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Charges</p>
                <h3 class="text-xl font-black text-rose-600">{{ number_format($stats['period']['total_depenses'], 0, ',', ' ') }}</h3>
                <p class="text-[8px] text-slate-400 font-bold uppercase">GNF Dépensés</p>
            </div>

            {{-- Bénéfice Net --}}
            <div class="bg-slate-900 rounded-[40px] p-6 border border-slate-800 shadow-xl relative overflow-hidden group">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Net Réel</p>
                <h3 class="text-xl font-black text-white">{{ number_format($stats['period']['benefice_net'], 0, ',', ' ') }}</h3>
                <p class="text-[8px] text-slate-500 font-bold uppercase">GNF de Gain</p>
            </div>

            {{-- Biens --}}
            <div class="bg-white rounded-[40px] p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Actifs</p>
                <h3 class="text-xl font-black text-emerald-600">{{ $stats['global']['total_biens'] }}</h3>
                <p class="text-[8px] text-slate-400 font-bold uppercase">Biens Gérés</p>
            </div>

            {{-- Locataires --}}
            <div class="bg-white rounded-[40px] p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Locataires</p>
                <h3 class="text-xl font-black text-purple-600">{{ $stats['global']['total_locataires'] }}</h3>
                <p class="text-[8px] text-slate-400 font-bold uppercase">Clients Actifs</p>
            </div>

            {{-- Contrats --}}
            <div class="bg-white rounded-[40px] p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Contrats</p>
                <h3 class="text-xl font-black text-amber-600">{{ $stats['global']['total_contrats'] }}</h3>
                <p class="text-[8px] text-slate-400 font-bold uppercase">Baux Signés</p>
            </div>

            {{-- Support --}}
            <a href="{{ route('messages.index', ['filter' => 'support']) }}" class="bg-indigo-50 rounded-[40px] p-6 border border-indigo-100 shadow-sm relative overflow-hidden group hover:bg-indigo-600 transition-all">
                @if($stats['support_tickets_count'] > 0)
                    <div class="absolute top-4 right-4 w-4 h-4 bg-rose-500 rounded-full border-2 border-white animate-bounce z-20 shadow-lg shadow-rose-300"></div>
                @endif
                <p class="text-[10px] font-black text-indigo-400 group-hover:text-indigo-200 uppercase tracking-widest mb-2">Support</p>
                <h3 class="text-xl font-black text-indigo-700 group-hover:text-white">{{ $stats['support_tickets_count'] }}</h3>
                <p class="text-[8px] text-indigo-400 group-hover:text-indigo-200 font-bold uppercase">Requêtes non lues</p>
                <div class="absolute right-4 bottom-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-headset text-4xl text-indigo-900 group-hover:text-white"></i>
                </div>
            </a>
        </div>

        {{-- REQUÊTES SUPPORT RÉCENTES --}}
        @if(count($stats['recent_support_requests']) > 0)
        <div class="mb-12">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-black text-slate-800">
                    {{ auth()->user()->role === 'admin' ? 'Dernières requêtes support' : 'Réponses du Support' }}
                </h3>
                <a href="{{ route('messages.index', ['filter' => 'support']) }}" class="text-xs font-black text-blue-600 uppercase tracking-widest hover:underline">Voir tout</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($stats['recent_support_requests'] as $req)
                <a href="{{ route('messages.show', $req->id) }}" class="bg-white p-6 rounded-[35px] border border-slate-100 shadow-sm hover:shadow-md transition-all group flex items-start gap-4">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center flex-shrink-0 text-lg font-black group-hover:bg-blue-600 group-hover:text-white transition-all">
                        {{ substr($req->sender->name ?? '?', 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <h4 class="font-black text-slate-800 text-sm mb-1 truncate">
                            {{ $req->sender->id == 1 ? 'Le Système' : ($req->sender->name ?? 'Anonyme') }}
                        </h4>
                        <p class="text-[10px] text-slate-400 mb-2 font-bold uppercase tracking-tighter">{{ $req->created_at->diffForHumans() }}</p>
                        <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed italic">"{{ $req->content }}"</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- GRAPHIQUES ET ACTIONS --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            <div class="lg:col-span-2 bg-white rounded-[50px] p-12 border border-slate-100 shadow-sm h-[500px]">
                <div class="flex justify-between items-center mb-10">
                    <h3 class="text-xl font-black text-slate-800">Évolution des Revenus</h3>
                    <span class="px-4 py-2 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest">Performance Mensuelle</span>
                </div>
                <div class="h-80">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-[50px] p-12 border border-slate-100 shadow-sm flex flex-col justify-center items-center text-center">
                <h3 class="text-xl font-black text-slate-800 mb-8">Statut du Parc</h3>
                <div class="w-64 h-64 relative">
                    <canvas id="statutChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-3xl font-black text-slate-800">{{ $stats['global']['total_biens'] }}</span>
                        <span class="text-[10px] font-black text-slate-400 uppercase">Total Biens</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- LISTES ET LOGS --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            {{-- Propriétaires Récents --}}
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'gestionnaire')
            <div class="bg-white rounded-[50px] p-12 border border-slate-100 shadow-sm">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xl font-black text-slate-800">Propriétaires Récents</h3>
                    <a href="{{ route('proprietaires.index') }}" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline">Voir tout</a>
                </div>
                <div class="space-y-6">
                    @forelse($stats['recent_proprietaires'] as $p)
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-[30px] hover:bg-blue-50 transition-all group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center shadow-sm group-hover:scale-110 transition-all">
                                <i class="fa-solid fa-user-shield text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-black text-slate-800 text-sm">{{ $p->user->name ?? 'N/A' }}</p>
                                <p class="text-[9px] text-slate-400 font-bold uppercase">{{ $p->telephone ?: 'Aucun tel' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-black text-slate-800 text-sm">{{ $p->biens_count ?? $p->biens->count() }} Biens</p>
                            <a href="{{ route('proprietaires.show', $p) }}" class="text-[8px] text-blue-600 font-black uppercase hover:underline">Détails</a>
                        </div>
                    </div>
                    @empty
                    <p class="text-slate-400 italic text-center py-8">Aucun propriétaire.</p>
                    @endforelse
                </div>
            </div>
            @endif

            {{-- Mes Locataires Récents --}}
            <div class="bg-white rounded-[50px] p-12 border border-slate-100 shadow-sm">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xl font-black text-slate-800">Locataires Récents</h3>
                    <a href="{{ route('locataires.index') }}" class="text-[10px] font-black text-purple-600 uppercase tracking-widest hover:underline">Voir tout</a>
                </div>
                <div class="space-y-6">
                    @forelse($stats['locataires_liste'] as $l)
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-[30px] hover:bg-purple-50 transition-all group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center shadow-sm group-hover:scale-110 transition-all">
                                <i class="fa-solid fa-user-tie text-purple-600"></i>
                            </div>
                            <div>
                                <p class="font-black text-slate-800 text-sm">{{ $l->nom_complet }}</p>
                                <p class="text-[9px] text-slate-400 font-bold uppercase">
                                    {{ $l->contratActif->bien->libelle ?? 'Sans contrat' }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-black text-slate-800 text-sm">{{ $l->telephone }}</p>
                            <a href="tel:{{ $l->telephone }}" class="text-[8px] text-purple-600 font-black uppercase hover:underline">Contacter</a>
                        </div>
                    </div>
                    @empty
                    <p class="text-slate-400 italic text-center py-8">Aucun locataire trouvé.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Vos Relevés de Gestion (PROPRIÉTAIRE SEULEMENT) --}}
        @if(auth()->user()->role === 'proprietaire')
        <div class="bg-slate-900 rounded-[50px] p-12 text-white shadow-2xl shadow-slate-200 relative overflow-hidden group mb-12">
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-white/5 rounded-full group-hover:scale-110 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-2xl font-black">Relevés de Gestion Officiels</h3>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-2">Archives financières certifiées par l'agence</p>
                    </div>
                    <i class="fa-solid fa-file-shield text-4xl text-blue-500"></i>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @php
                        $currentMonthNum = date('n');
                        $currentYearNum = date('Y');
                        $selectedYearNum = $stats['selected_year'];
                    @endphp
                    @for($m = 1; $m <= 12; $m++)
                        @php
                            $bilan = $stats['bilans_officiels'][$m] ?? null;
                            $isFuture = ($selectedYearNum > $currentYearNum) || ($selectedYearNum == $currentYearNum && $m > $currentMonthNum);
                            $isCurrent = ($selectedYearNum == $currentYearNum && $m == $currentMonthNum);
                        @endphp

                        @if($bilan)
                            <a href="{{ route('reports.monthly', ['year' => $selectedYearNum, 'month' => $m]) }}" 
                               class="flex flex-col items-center p-4 bg-white/10 rounded-2xl hover:bg-blue-600 transition-all border border-white/10 group/item shadow-lg">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-300">{{ $stats['months'][$m] }}</span>
                                <i class="fa-solid fa-cloud-arrow-down mt-2 text-white text-lg group-hover/item:translate-y-1 transition-transform"></i>
                                <span class="text-[8px] font-black mt-2 text-emerald-400 uppercase tracking-widest">Disponible</span>
                            </a>
                        @elseif($isCurrent)
                            <div class="flex flex-col items-center p-4 bg-white/5 rounded-2xl border border-dashed border-white/20 opacity-80">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $stats['months'][$m] }}</span>
                                <i class="fa-solid fa-spinner fa-spin mt-2 text-blue-400"></i>
                                <span class="text-[8px] font-black mt-2 text-blue-500 uppercase tracking-widest">En cours...</span>
                            </div>
                        @elseif($isFuture)
                            <div class="flex flex-col items-center p-4 bg-slate-950/30 rounded-2xl border border-white/5 opacity-30">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-600">{{ $stats['months'][$m] }}</span>
                                <i class="fa-solid fa-lock mt-2 text-slate-700"></i>
                                <span class="text-[8px] font-black mt-2 text-slate-700 uppercase tracking-widest">À venir</span>
                            </div>
                        @else
                            <div class="flex flex-col items-center p-4 bg-rose-950/20 rounded-2xl border border-rose-900/20">
                                <span class="text-[10px] font-black uppercase tracking-widest text-rose-300/50">{{ $stats['months'][$m] }}</span>
                                <i class="fa-solid fa-clock-rotate-left mt-2 text-rose-900"></i>
                                <span class="text-[8px] font-black mt-2 text-rose-900 uppercase tracking-widest">Non clôturé</span>
                            </div>
                        @endif
                    @endfor
                </div>
            </div>
        </div>
        @endif

        {{-- Logs d'activité (ADMIN SEULEMENT) --}}
        @if(auth()->user()->role === 'admin')
            <div class="bg-white rounded-[50px] p-12 border border-slate-100 shadow-sm lg:col-span-2 mt-8">
                <h3 class="text-xl font-black text-slate-800 mb-8">Journal d'Activité Système</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($stats['activity_logs']->take(6) as $log)
                    <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl">
                        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-slate-400 border border-slate-100 font-black text-xs uppercase shadow-sm">
                            {{ substr($log->user->name ?? '?', 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[11px] font-bold text-slate-700 truncate">
                                {{ $log->details['message'] ?? $log->action }}
                            </p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase">{{ $log->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Graphique d'Évolution des Revenus (Comparatif)
        const ctxRevenue = document.getElementById('revenueChart');
        if (ctxRevenue) {
            new Chart(ctxRevenue, {
                type: 'line',
                data: {
                    labels: @json($stats['labels_mois']),
                    datasets: [
                        {
                            label: '{{ $stats['selected_year'] }}',
                            data: @json($stats['data_paiements']),
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 4,
                            pointRadius: 2,
                            pointHoverRadius: 6,
                        },
                        {
                            label: '{{ $stats['selected_year'] - 1 }}',
                            data: @json($stats['data_past_year']),
                            borderColor: '#cbd5e1',
                            borderDash: [5, 5],
                            fill: false,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 0,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: '#1e293b',
                            titleFont: { weight: 'black' },
                            padding: 12,
                            cornerRadius: 12
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.03)' },
                            ticks: { 
                                font: { weight: 'bold', size: 10 }, 
                                color: '#94a3b8',
                                callback: function(value) { return value.toLocaleString() + ' GNF'; }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { weight: 'bold', size: 10 }, color: '#94a3b8' }
                        }
                    }
                }
            });
        }

        // Graphique de Répartition (Donut)
        const ctxStatut = document.getElementById('statutChart');
        if (ctxStatut) {
            new Chart(ctxStatut, {
                type: 'doughnut',
                data: {
                    labels: @json($stats['statut_labels']),
                    datasets: [{
                        data: @json($stats['statut_counts']),
                        backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444', '#8b5cf6'],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 25,
                                font: { weight: 'bold', size: 11, family: 'Inter' },
                                color: '#64748b'
                            }
                        }
                    },
                    cutout: '75%'
                }
            });
        }
    });
</script>
@endpush

@endsection