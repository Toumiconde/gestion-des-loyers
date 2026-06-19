@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="animate-fade-in space-y-10 pb-12">

    {{-- ================================================================= --}}
    {{-- LOGIQUE DE DÉTECTION DU RÔLE                                    --}}
    {{-- ================================================================= --}}
    @php
        $user = auth()->user();
        $role = $user->role;
    @endphp

    {{-- ================================================================= --}}
    {{-- INTERFACE LOCATAIRE                                             --}}
    {{-- ================================================================= --}}
    @if($role === 'locataire')
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

                {{-- Sidebar Locataire --}}
                <div class="space-y-8">
                    <div class="bg-slate-900 rounded-[50px] p-10 text-white shadow-2xl">
                        <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest mb-10 text-center">Services</h3>
                        <div class="space-y-4">
                            <a href="{{ route('paiements.create') }}" class="flex items-center gap-4 w-full p-6 bg-white/5 rounded-3xl border border-white/10 hover:bg-emerald-600 transition-all group">
                                <i class="fa-solid fa-money-bill-transfer text-2xl opacity-50 group-hover:opacity-100"></i>
                                <p class="font-black text-sm">Déclarer un paiement</p>
                            </a>
                            <a href="{{ route('incidents.create') }}" class="flex items-center gap-4 w-full p-6 bg-white/5 rounded-3xl border border-white/10 hover:bg-rose-600 transition-all group">
                                <i class="fa-solid fa-triangle-exclamation text-2xl opacity-50 group-hover:opacity-100"></i>
                                <p class="font-black text-sm">Signaler une panne</p>
                            </a>
                        </div>
                    </div>

                    <div class="bg-emerald-50 rounded-[50px] p-10 border border-emerald-100">
                        <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-4">Prochain Loyer</p>
                        <h4 class="text-2xl font-black text-slate-800">
                            {{ \Carbon\Carbon::parse($lData['contrat']->date_debut)->day }} {{ $stats['months'][date('n')] }}
                        </h4>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-[50px] p-20 text-center border border-slate-100 shadow-sm">
                <i class="fa-solid fa-file-contract text-6xl text-slate-100 mb-8"></i>
                <h2 class="text-3xl font-black text-slate-800 mb-4">Aucun contrat actif</h2>
                <p class="text-slate-400 font-medium">Contactez l'agence pour activer votre espace locataire.</p>
            </div>
        @endif

    {{-- ================================================================= --}}
    {{-- INTERFACE ADMIN / GESTIONNAIRE / COMPTABLE                      --}}
    {{-- ================================================================= --}}
    @else
        {{-- En-tête / Salutations --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Bonjour, {{ explode(' ', $user->name)[0] }}</h2>
                <p class="text-slate-500 font-medium mt-1">Voici le résumé de votre activité de gestion.</p>
            </div>
            {{-- Barre de Filtres Premium --}}
            <div class="bg-white p-2 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-2">
                <form action="{{ route('dashboard') }}" method="GET" class="flex items-center m-0">
                    <select name="month" class="bg-transparent border-none text-sm font-bold text-slate-600 focus:ring-0 cursor-pointer py-2 pl-4 pr-8">
                        <option value="">Tous les mois</option>
                        @foreach($stats['months'] as $num => $name)
                            <option value="{{ $num }}" {{ $stats['selected_month'] == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <div class="w-px h-6 bg-slate-200 mx-2"></div>
                    <select name="year" class="bg-transparent border-none text-sm font-bold text-slate-600 focus:ring-0 cursor-pointer py-2 pl-2 pr-8">
                        @foreach($stats['years'] as $y)
                            <option value="{{ $y }}" {{ $stats['selected_year'] == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-slate-900 text-white p-2 rounded-xl hover:bg-blue-600 transition-colors ml-2 flex items-center justify-center w-10 h-10 shadow-md">
                        <i class="fa-solid fa-filter text-xs"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- KPI Grid Premium : FINANCES & OPÉRATIONS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Revenus -->
            <div class="bg-white p-6 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/60 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="flex justify-between items-start relative z-10 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                        <i class="fa-solid fa-arrow-down-to-line text-lg"></i>
                    </div>
                    <span class="bg-blue-50 text-blue-600 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Entrées</span>
                </div>
                <div class="relative z-10">
                    <h3 class="text-slate-400 text-xs font-bold mb-1">Encaissements</h3>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black text-slate-800 tracking-tight">{{ number_format($stats['period']['revenus'], 0, ',', ' ') }}</span>
                        <span class="text-xs font-bold text-slate-400">GNF</span>
                    </div>
                </div>
            </div>

            <!-- Dépenses -->
            <div class="bg-white p-6 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/60 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="flex justify-between items-start relative z-10 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-rose-400 to-rose-600 flex items-center justify-center text-white shadow-lg shadow-rose-500/30">
                        <i class="fa-solid fa-arrow-up-from-bracket text-lg"></i>
                    </div>
                    <span class="bg-rose-50 text-rose-600 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Sorties</span>
                </div>
                <div class="relative z-10">
                    <h3 class="text-slate-400 text-xs font-bold mb-1">Dépenses & Travaux</h3>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black text-slate-800 tracking-tight">{{ number_format($stats['period']['total_depenses'], 0, ',', ' ') }}</span>
                        <span class="text-xs font-bold text-slate-400">GNF</span>
                    </div>
                </div>
            </div>

            <!-- Bénéfice Net -->
            <div class="bg-white p-6 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/60 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="flex justify-between items-start relative z-10 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
                        <i class="fa-solid fa-piggy-bank text-lg"></i>
                    </div>
                    <span class="bg-emerald-50 text-emerald-600 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Trésorerie</span>
                </div>
                <div class="relative z-10">
                    <h3 class="text-slate-400 text-xs font-bold mb-1">Bénéfice Net</h3>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black text-slate-800 tracking-tight">{{ number_format($stats['period']['benefice_net'], 0, ',', ' ') }}</span>
                        <span class="text-xs font-bold text-slate-400">GNF</span>
                    </div>
                </div>
            </div>

            <!-- Loyers en retard -->
            <div class="bg-gradient-to-br from-rose-50 to-white p-6 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-rose-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="flex justify-between items-start relative z-10 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-white border border-rose-100 flex items-center justify-center text-rose-500 shadow-sm">
                        <i class="fa-solid fa-triangle-exclamation text-lg animate-pulse"></i>
                    </div>
                    <span class="bg-rose-100 text-rose-700 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Impayés</span>
                </div>
                <div class="relative z-10">
                    <h3 class="text-rose-400 text-xs font-bold mb-1">Loyers en retard</h3>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black text-rose-600 tracking-tight">{{ $stats['period']['loyers_en_retard'] }}</span>
                        <span class="text-xs font-bold text-rose-400">Factures</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Patrimoine -->
            <div class="bg-white p-6 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/60 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="flex justify-between items-start relative z-10 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600">
                        <i class="fa-solid fa-building"></i>
                    </div>
                </div>
                <div class="relative z-10">
                    <h3 class="text-slate-400 text-xs font-bold mb-1">Patrimoine Actif</h3>
                    <div class="flex items-baseline gap-2">
                        <span class="text-xl font-black text-slate-800 tracking-tight">{{ $stats['biens_count'] ?? $stats['global']['total_biens'] }}</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Unités</span>
                    </div>
                </div>
            </div>

            <!-- Taux Occupation -->
            <div class="bg-white p-6 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/60 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="flex justify-between items-start relative z-10 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <div class="w-12 h-1 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-500" style="width: {{ min(100, $stats['taux_occupation'] ?? 0) }}%"></div>
                    </div>
                </div>
                <div class="relative z-10">
                    <h3 class="text-slate-400 text-xs font-bold mb-1">Taux d'Occupation</h3>
                    <div class="flex items-baseline gap-2">
                        <span class="text-xl font-black text-slate-800 tracking-tight">{{ number_format($stats['taux_occupation'] ?? 0, 1) }}</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">%</span>
                    </div>
                </div>
            </div>

            <!-- Locataires -->
            <div class="bg-white p-6 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/60 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="flex justify-between items-start relative z-10 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <div class="relative z-10">
                    <h3 class="text-slate-400 text-xs font-bold mb-1">Locataires Actifs</h3>
                    <div class="flex items-baseline gap-2">
                        <span class="text-xl font-black text-slate-800 tracking-tight">{{ $stats['locataires_count'] ?? $stats['global']['total_locataires'] }}</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Personnes</span>
                    </div>
                </div>
            </div>

            <!-- Support -->
            <div class="bg-white p-6 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/60 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="flex justify-between items-start relative z-10 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                </div>
                <div class="relative z-10">
                    <h3 class="text-slate-400 text-xs font-bold mb-1">Tickets Support</h3>
                    <div class="flex items-baseline gap-2">
                        <span class="text-xl font-black text-slate-800 tracking-tight">{{ $stats['support_tickets_count'] ?? 0 }}</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Non lus</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section Centrale: Graphiques et Actions --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            
            {{-- === GRAPHIQUE PERFORMANCE FINANCIÈRE PREMIUM === --}}
            <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/60 overflow-hidden flex flex-col">
                {{-- Header du graphique --}}
                <div class="px-8 pt-8 pb-4 flex items-start justify-between flex-wrap gap-4 border-b border-slate-50">
                    <div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                            <div class="w-2 h-6 bg-blue-500 rounded-full"></div>
                            Évolution Financière
                        </h3>
                        <p class="text-xs text-slate-400 mt-1 font-medium ml-4">Comparatif mensuel des encaissements sur {{ $stats['selected_year'] }}</p>
                    </div>
                    {{-- KPI Inline --}}
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Annuel</p>
                            <p class="text-xl font-black text-slate-800">{{ number_format(array_sum($stats['data_paiements']), 0, ',', ' ') }} GNF</p>
                        </div>
                        <div class="w-px h-10 bg-slate-100 mx-2"></div>
                        <div class="px-3 py-2 bg-emerald-50 rounded-xl flex items-center gap-2 border border-emerald-100">
                            <i class="fa-solid fa-arrow-trend-up text-emerald-500"></i>
                            <span class="text-xs font-bold text-emerald-700">+ Actif</span>
                        </div>
                    </div>
                </div>
                {{-- Zone du graphique --}}
                <div class="h-80 px-4 pb-6 pt-4 flex-1">
                    <canvas id="accountingTrendChart"></canvas>
                </div>
            </div>

            {{-- Colonne Droite: Statut du Parc & Actions Rapides --}}
            <div class="space-y-8 flex flex-col">
                <!-- Actions Rapides Dark Mode -->
                <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-[2.5rem] p-8 shadow-xl shadow-slate-900/20 relative overflow-hidden flex-1">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>
                    <div class="relative z-10">
                        <h3 class="text-lg font-black text-white mb-6 tracking-tight flex items-center gap-2">
                            <i class="fa-solid fa-bolt text-amber-400"></i> Actions Rapides
                        </h3>
                        <div class="space-y-3">
                            <a href="{{ route('paiements.create') }}" class="group flex items-center justify-between p-4 bg-white/10 hover:bg-white/20 border border-white/5 rounded-2xl transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-money-bill-wave"></i>
                                    </div>
                                    <span class="text-sm font-bold text-white">Saisir un paiement</span>
                                </div>
                                <i class="fa-solid fa-chevron-right text-white/30 group-hover:text-white transition-colors text-xs"></i>
                            </a>
                            <a href="{{ route('biens.create') }}" class="group flex items-center justify-between p-4 bg-white/10 hover:bg-white/20 border border-white/5 rounded-2xl transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center text-blue-400 group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-house-medical"></i>
                                    </div>
                                    <span class="text-sm font-bold text-white">Ajouter un bien</span>
                                </div>
                                <i class="fa-solid fa-chevron-right text-white/30 group-hover:text-white transition-colors text-xs"></i>
                            </a>
                            <a href="{{ route('locataires.create') }}" class="group flex items-center justify-between p-4 bg-white/10 hover:bg-white/20 border border-white/5 rounded-2xl transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-purple-500/20 flex items-center justify-center text-purple-400 group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-user-plus"></i>
                                    </div>
                                    <span class="text-sm font-bold text-white">Nouveau locataire</span>
                                </div>
                                <i class="fa-solid fa-chevron-right text-white/30 group-hover:text-white transition-colors text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- État du parc -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/60 flex-1 flex flex-col">
                    <h3 class="text-lg font-black text-slate-800 mb-6 tracking-tight flex items-center gap-2">
                        <div class="w-2 h-5 bg-emerald-500 rounded-full"></div>
                        Occupation du Parc
                    </h3>
                    <div class="w-full relative flex-1 min-h-[200px]">
                        <canvas id="statutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section du Bas: Journal & Flash Info --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Journal des Activités --}}
            <div class="lg:col-span-2 bg-white rounded-[2.5rem] p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/60">
                <div class="flex items-center justify-between mb-8 border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-black text-slate-800 tracking-tight flex items-center gap-2">
                        <div class="w-2 h-5 bg-slate-800 rounded-full"></div>
                        Activités Récentes
                    </h3>
                    <a href="{{ route('activity-logs.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 px-3 py-1.5 rounded-lg transition-colors">Tout afficher</a>
                </div>
                <div class="space-y-4">
                    @forelse($stats['activity_logs'] as $log)
                    <div class="flex items-start gap-4 group">
                        <div class="mt-1">
                            @php
                                $icon = 'fa-circle-info'; $color = 'text-blue-500'; $bg = 'bg-blue-50';
                                if(str_contains(strtolower($log->action), 'suppression') || str_contains(strtolower($log->action), 'erreur')) { $icon = 'fa-trash'; $color = 'text-rose-500'; $bg = 'bg-rose-50'; }
                                elseif(str_contains(strtolower($log->action), 'création') || str_contains(strtolower($log->action), 'paiement')) { $icon = 'fa-check'; $color = 'text-emerald-500'; $bg = 'bg-emerald-50'; }
                                elseif(str_contains(strtolower($log->action), 'mise à jour') || str_contains(strtolower($log->action), 'modification')) { $icon = 'fa-pen'; $color = 'text-amber-500'; $bg = 'bg-amber-50'; }
                            @endphp
                            <div class="w-10 h-10 rounded-full {{ $bg }} {{ $color }} flex items-center justify-center text-sm shadow-sm group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid {{ $icon }}"></i>
                            </div>
                        </div>
                        <div class="flex-1 bg-slate-50 rounded-2xl p-4 border border-slate-100/60 group-hover:border-slate-200 transition-colors">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-sm font-bold text-slate-800">{{ $log->user->name ?? 'Système' }}</p>
                                <span class="text-[10px] font-bold text-slate-400 flex items-center gap-1">
                                    <i class="fa-regular fa-clock"></i> {{ $log->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed">{{ $log->description }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-400">
                        <i class="fa-solid fa-inbox text-3xl mb-3 opacity-50"></i>
                        <p class="text-sm font-medium">Aucune activité récente.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Dernières Transactions (Flux Financier) --}}
            <div class="bg-white rounded-[2.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/60 flex flex-col overflow-hidden">
                <div class="px-8 pt-8 pb-4 flex items-center justify-between border-b border-slate-50">
                    <h3 class="text-lg font-black text-slate-800 tracking-tight flex items-center gap-2">
                        <div class="w-2 h-5 bg-blue-500 rounded-full"></div>
                        Dernières Transactions
                    </h3>
                    <a href="{{ route('paiements.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 px-3 py-1.5 rounded-lg transition-colors">Tout voir</a>
                </div>
                
                <div class="flex-1 overflow-x-auto p-4">
                    <table class="w-full text-left">
                        <tbody class="divide-y divide-slate-50">
                            @forelse($stats['derniers_paiements'] as $paiement)
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 border border-slate-100">
                                                <i class="fa-solid fa-file-invoice"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-800">{{ $paiement->contrat->locataire->nom_complet ?? 'Locataire Inconnu' }}</p>
                                                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">{{ Carbon\Carbon::parse($paiement->mois_concerne)->translatedFormat('F Y') }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="text-sm font-bold text-slate-800">{{ number_format($paiement->montant, 0, ',', ' ') }} <span class="text-xs text-slate-400">GNF</span></p>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        @if($paiement->statut === 'paye')
                                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest rounded-full border border-emerald-100">Payé</span>
                                        @elseif($paiement->statut === 'en_retard')
                                            <span class="px-3 py-1 bg-rose-50 text-rose-600 text-[10px] font-black uppercase tracking-widest rounded-full border border-rose-100">En retard</span>
                                        @else
                                            <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-black uppercase tracking-widest rounded-full border border-amber-100">Attente</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-10 text-center text-slate-400">
                                        <i class="fa-solid fa-receipt text-3xl mb-3 opacity-30"></i>
                                        <p class="text-sm font-medium">Aucune transaction récente.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#94a3b8';

        // ================================================================
        // GRAPHIQUE PERFORMANCE FINANCIÈRE — VERSION ULTRA PRO
        // ================================================================
        const ctxFinance = document.getElementById('accountingTrendChart');
        if (ctxFinance) {
            const ctx = ctxFinance.getContext('2d');

            // Dégradé principal (Bleu profond → transparent)
            const fillGradient = ctx.createLinearGradient(0, 0, 0, 350);
            fillGradient.addColorStop(0,   'rgba(99, 102, 241, 0.30)');
            fillGradient.addColorStop(0.5, 'rgba(59, 130, 246, 0.10)');
            fillGradient.addColorStop(1,   'rgba(59, 130, 246, 0.00)');

            // Ligne dégradée (Indigo → Cyan)
            const lineGradient = ctx.createLinearGradient(0, 0, 350, 0);
            lineGradient.addColorStop(0,   '#6366f1'); // Indigo
            lineGradient.addColorStop(0.5, '#3b82f6'); // Blue
            lineGradient.addColorStop(1,   '#06b6d4'); // Cyan

            const data = @json($stats['data_paiements']);
            const labels = @json($stats['labels_mois']);

            new Chart(ctxFinance, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Encaissements',
                        data: data,
                        borderColor: lineGradient,
                        borderWidth: 3.5,
                        pointRadius: 4,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#6366f1',
                        pointBorderWidth: 2.5,
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: '#6366f1',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 3,
                        fill: true,
                        backgroundColor: fillGradient,
                        tension: 0.45,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            enabled: true,
                            backgroundColor: '#0f172a',
                            titleColor: '#e2e8f0',
                            bodyColor: '#ffffff',
                            titleFont: { size: 11, weight: '600', family: 'Inter' },
                            bodyFont:  { size: 16, weight: '900', family: 'Inter' },
                            padding: { top: 12, right: 18, bottom: 12, left: 18 },
                            cornerRadius: 14,
                            displayColors: false,
                            caretPadding: 10,
                            callbacks: {
                                title: (items) => items[0].label,
                                label: (ctx) => {
                                    const val = ctx.parsed.y;
                                    if (val >= 1000000) return (val / 1000000).toFixed(2) + ' M GNF';
                                    return val.toLocaleString('fr-FR') + ' GNF';
                                },
                                afterLabel: (ctx) => {
                                    const total = data.reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? ((ctx.parsed.y / total) * 100).toFixed(1) : 0;
                                    return '⟶ ' + pct + '% du total annuel';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            position: 'left',
                            grid: {
                                color: 'rgba(226, 232, 240, 0.6)',
                                drawBorder: false,
                                lineWidth: 1,
                            },
                            border: { dash: [4, 4], display: false },
                            ticks: {
                                padding: 12,
                                font: { size: 11, weight: '700' },
                                callback: function(value) {
                                    if (value >= 1000000) return (value / 1000000).toFixed(1) + ' M';
                                    if (value >= 1000)    return (value / 1000).toFixed(0) + ' k';
                                    return value;
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: {
                                padding: 12,
                                font: { size: 11, weight: '700' }
                            }
                        }
                    }
                }
            });
        }

        // --- GRAPHIQUE DE RÉPARTITION (DOUGHNUT PREMIUM) ---
        const ctxStatut = document.getElementById('statutChart');
        if (ctxStatut) {
            new Chart(ctxStatut, {
                type: 'doughnut',
                data: {
                    labels: @json($stats['statut_labels'] ?? []),
                    datasets: [{
                        data: @json($stats['statut_counts'] ?? []),
                        backgroundColor: [
                            '#10b981', // Emerald
                            '#f59e0b', // Amber
                            '#3b82f6', // Blue
                            '#ef4444', // Rose
                            '#8b5cf6'  // Violet
                        ],
                        hoverOffset: 15,
                        borderWidth: 8,
                        borderColor: '#fff',
                        borderRadius: 10, // Coins arrondis
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: { size: 11, weight: 'bold' }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            padding: 15,
                            cornerRadius: 15,
                            titleFont: { weight: 'bold' }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush

@endsection