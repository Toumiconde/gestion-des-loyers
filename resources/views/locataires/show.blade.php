@extends('layouts.app')

@section('title', 'Détails du Locataire')

@section('content')

@include('partials.password-reset-alert')

<div class="max-w-6xl mx-auto py-8">
    {{-- Fil d'ariane --}}
    <div class="flex items-center justify-between mb-8">
        <nav class="flex" aria-label="Breadcrumb">
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
                        <span class="text-slate-600">{{ $locataire->prenom }} {{ $locataire->nom }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="flex gap-3">
            @if((auth()->user()->isAdmin() || auth()->user()->isGestionnaire()) && $locataire->user)
            <form action="{{ route('admin.users.reset-password', $locataire->user) }}" method="POST"
                  onsubmit="return confirm('Réinitialiser le mot de passe de {{ $locataire->prenom }} {{ $locataire->nom }} ?')">
                @csrf
                <button type="submit"
                        class="px-5 py-2.5 bg-indigo-50 text-indigo-600 font-bold rounded-xl hover:bg-indigo-600 hover:text-white transition-all flex items-center gap-2">
                    <i class="fa-solid fa-key"></i> Reset Pass
                </button>
            </form>
            @endif
            @if(auth()->user()->isAdmin() || auth()->user()->isGestionnaire())
            <a href="{{ route('locataires.edit', $locataire) }}" 
               class="px-5 py-2.5 bg-amber-50 text-amber-600 font-bold rounded-xl hover:bg-amber-600 hover:text-white transition-all flex items-center gap-2">
                <i class="fa-solid fa-user-pen"></i> Modifier le profil
            </a>
            @endif
        </div>
    </div>

    {{-- ALERTE DE SOLVABILITÉ --}}
    @if($isDropping || $latestScore < 50)
    <div class="mb-8 p-6 bg-rose-600 text-white rounded-[30px] shadow-xl shadow-rose-200 flex flex-col md:flex-row items-center justify-between gap-6 animate-pulse">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center text-3xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h3 class="text-xl font-black italic">Alerte de Vigilance Financière</h3>
                <p class="text-rose-100 font-medium">Le niveau de fiabilité de {{ $locataire->prenom }} est en baisse. Risque d'impayé détecté.</p>
            </div>
        </div>
        <div class="text-center md:text-right">
            <p class="text-[10px] font-black uppercase tracking-widest text-rose-200">Score de Confiance Actuel</p>
            <p class="text-4xl font-black">{{ $latestScore }}%</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Profil --}}
        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 p-10 text-center relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-purple-50 rounded-full"></div>
                <div class="w-24 h-24 rounded-3xl bg-purple-600 text-white flex items-center justify-center text-4xl font-black mx-auto mb-6 shadow-xl shadow-purple-200 relative z-10">
                    {{ substr($locataire->prenom, 0, 1) }}
                </div>
                <h2 class="text-2xl font-black text-slate-800">{{ $locataire->prenom }} {{ $locataire->nom }}</h2>
                <p class="text-slate-400 font-medium mb-6">Inscrit le {{ $locataire->created_at->format('d/m/Y') }}</p>
                
                <div class="flex justify-center flex-wrap gap-2">
                    @if($locataire->contrats->where('statut', 'actif')->count() > 0)
                        <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                            <i class="fa-solid fa-user-check"></i> Ancien Locataire
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                            <i class="fa-solid fa-user-plus"></i> Nouveau Locataire
                        </span>
                    @endif
                    
                    @if($locataire->user)
                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-widest flex items-center gap-1">
                            <i class="fa-solid fa-circle-check text-[8px]"></i> Compte actif
                        </span>
                    @endif
                </div>

                {{-- LIEN DE PAIEMENT RAPIDE --}}
                <div class="mt-8 pt-8 border-t border-slate-50">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Lien de paiement rapide</p>
                    <div class="flex items-center gap-2 bg-slate-50 p-2 rounded-2xl border border-slate-100">
                        <input type="text" id="paymentLink" readonly value="{{ url('/paiements/create') }}" 
                               class="flex-1 bg-transparent border-none text-[10px] font-mono text-slate-500 focus:ring-0">
                        <button onclick="copyPaymentLink()" class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-100">
                            <i class="fa-solid fa-copy"></i>
                        </button>
                    </div>
                    <p id="copyStatus" class="text-[9px] text-emerald-600 font-bold mt-2 opacity-0 transition-opacity">Lien copié !</p>
                </div>

                <script>
                    function copyPaymentLink() {
                        const linkInput = document.getElementById('paymentLink');
                        linkInput.select();
                        linkInput.setSelectionRange(0, 99999);
                        navigator.clipboard.writeText(linkInput.value);
                        
                        const status = document.getElementById('copyStatus');
                        status.classList.remove('opacity-0');
                        setTimeout(() => status.classList.add('opacity-0'), 2000);
                    }
                </script>
            </div>

            {{-- JAUGE DE FIABILITÉ --}}
            <div class="bg-slate-900 rounded-[40px] p-8 text-white relative overflow-hidden">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 italic">Score de Fiabilité</h3>
                <div class="flex items-end justify-between mb-4">
                    <span class="text-4xl font-black {{ $latestScore > 70 ? 'text-emerald-400' : ($latestScore > 40 ? 'text-amber-400' : 'text-rose-400') }}">
                        {{ $latestScore }}%
                    </span>
                    <i class="fa-solid {{ $isDropping ? 'fa-arrow-trend-down text-rose-400' : 'fa-arrow-trend-up text-emerald-400' }} text-2xl"></i>
                </div>
                <div class="w-full h-2 bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full {{ $latestScore > 70 ? 'bg-emerald-400' : ($latestScore > 40 ? 'bg-amber-400' : 'bg-rose-400') }} transition-all duration-1000" 
                         style="width: {{ $latestScore }}%"></div>
                </div>
                <p class="mt-4 text-[10px] text-slate-400 font-medium">Analyse basée sur l'historique complet des règlements de loyers.</p>
            </div>

            <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 p-10">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-6 border-b border-slate-50 pb-4 italic">Coordonnées</h3>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-phone text-slate-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-tighter">Téléphone</p>
                            <p class="text-slate-700 font-medium">{{ $locataire->telephone ?: 'Non renseigné' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-envelope text-slate-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-tighter">Email</p>
                            <p class="text-slate-700 font-medium">{{ $locataire->email ?: 'Non renseigné' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- DIAGRAMME D'ÉVOLUTION --}}
        <div class="lg:col-span-2 space-y-8">
            
            <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 p-10">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-slate-800">Évolution du Comportement de Paiement</h3>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Niveau de confiance</span>
                    </div>
                </div>
                <div class="h-[300px]">
                    <canvas id="reliabilityChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-8 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-xl font-black text-slate-800">Historique des Contrats</h3>
                </div>
                
                <div class="p-0">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-[10px] text-slate-400 font-black uppercase tracking-widest">
                            <tr>
                                <th class="px-8 py-4">N° Contrat</th>
                                <th class="px-8 py-4">Bien Immobilier</th>
                                <th class="px-8 py-4 text-right">Loyer</th>
                                <th class="px-8 py-4 text-center">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($locataire->contrats as $contrat)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-4">
                                    <a href="{{ route('contrats.show', $contrat) }}" class="font-black text-blue-600 hover:underline">
                                        {{ $contrat->numero_contrat }}
                                    </a>
                                </td>
                                <td class="px-8 py-4">
                                    <p class="font-bold text-slate-700">{{ $contrat->bien->libelle }}</p>
                                    <p class="text-[10px] text-slate-400">Depuis le {{ $contrat->date_debut->format('d/m/Y') }}</p>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <span class="font-black text-slate-800">{{ number_format($contrat->loyer, 0, ',', ' ') }} GNF</span>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase 
                                        {{ $contrat->statut === 'actif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $contrat->statut }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-8 py-12 text-center text-slate-400 italic">Aucun contrat.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('reliabilityChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Score de Fiabilité (%)',
                    data: {!! json_encode($scoreHistory) !!},
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2563eb',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { borderDash: [5, 5], color: '#e2e8f0' },
                        ticks: { font: { weight: 'bold' }, color: '#64748b' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: 'bold' }, color: '#64748b' }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { weight: 'black' },
                        padding: 12,
                        cornerRadius: 12
                    }
                }
            }
        });
    });
</script>

@endsection