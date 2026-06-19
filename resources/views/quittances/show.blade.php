@extends('layouts.app')

@section('title', 'Quittance de Loyer Premium — ' . $quittance->numero_quittance)

@section('content')

<div class="max-w-4xl mx-auto py-10">

    {{-- Actions --}}
    <div class="mb-8 flex justify-between items-center no-print">
        <a href="{{ route('quittances.index') }}" class="text-xs font-black text-slate-400 uppercase tracking-widest hover:text-blue-600 transition-colors flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Retour aux quittances
        </a>
        <div class="flex gap-4">
            <a href="{{ route('quittances.show', [$quittance->id, 'print' => 1]) }}" target="_blank" class="px-6 py-3 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-200">
                <i class="fa-solid fa-print mr-2"></i> Imprimer / PDF
            </a>
        </div>
    </div>

    {{-- QUITTANCE PREMIUM --}}
    <div class="bg-white rounded-[40px] shadow-2xl shadow-slate-200/50 p-16 relative overflow-hidden border border-slate-100" id="quittance-area">
        
        {{-- Filigrane PAYÉ --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-[0.03] pointer-events-none rotate-[-35deg] select-none">
            <p class="text-[200px] font-black tracking-tighter">PAYÉ</p>
        </div>

        {{-- Entête --}}
        <div class="flex justify-between items-start mb-16 relative z-10">
            <div class="flex items-center gap-6">
                @php 
                    $settings = \App\Models\Parametre::all()->pluck('valeur', 'cle');
                @endphp
                @if(!empty($settings['logo']))
                    <img src="{{ Storage::url($settings['logo']) }}" class="h-20 w-auto object-contain">
                @else
                    <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-blue-100">G</div>
                @endif
                <div>
                    <h1 class="text-2xl font-black text-slate-900">{{ $settings['nom_agence'] ?? 'GESTLOYER IMMOBILIER' }}</h1>
                    <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em]">{{ $settings['adresse'] ?? 'Agence Immobilière Agréée' }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">{{ $settings['telephone'] ?? '' }}</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-4xl font-black text-slate-900 mb-2">QUITTANCE</h2>
                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Référence : <span class="text-slate-900">{{ $quittance->numero_quittance }}</span></p>
                <p class="text-[10px] font-bold text-slate-400 mt-1 italic">Document officiel de règlement</p>
            </div>
        </div>

        {{-- Bloc Parties --}}
        <div class="grid grid-cols-2 gap-12 mb-16 relative z-10">
            <div class="p-8 bg-slate-50 rounded-[30px] border border-slate-100">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Bailleur (Propriétaire)</h3>
                <p class="text-lg font-black text-slate-900 mb-1">{{ $quittance->paiement->contrat->bien->proprietaire->user->name }}</p>
                <p class="text-xs text-slate-500 leading-relaxed">{{ $quittance->paiement->contrat->bien->proprietaire->adresse_professionnelle ?: $quittance->paiement->contrat->bien->proprietaire->adresse }}</p>
            </div>
            <div class="p-8 bg-blue-50 rounded-[30px] border border-blue-100/50">
                <h3 class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-4">Locataire</h3>
                <p class="text-lg font-black text-slate-900 mb-1">{{ $quittance->paiement->contrat->locataire->prenom }} {{ $quittance->paiement->contrat->locataire->nom }}</p>
                <p class="text-xs text-slate-500 leading-relaxed">{{ $quittance->paiement->contrat->bien->libelle }} — {{ $quittance->paiement->contrat->bien->adresse }}</p>
            </div>
        </div>

        {{-- Description du Mois --}}
        <div class="mb-12 text-center relative z-10">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Période concernée</p>
            <div class="inline-block px-10 py-4 bg-slate-900 text-white rounded-2xl font-black text-xl shadow-xl shadow-slate-200">
                {{ \Carbon\Carbon::parse($quittance->paiement->mois_concerne)->locale('fr')->isoFormat('MMMM YYYY') }}
            </div>
        </div>

        {{-- Tableau des Montants --}}
        <div class="mb-16 relative z-10">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                        <th class="py-4 px-2">Désignation</th>
                        <th class="py-4 px-2 text-right">Montant (GNF)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        $paiement     = $quittance->paiement;
                        $contrat      = $paiement->contrat;
                        $charges      = $contrat->bien->charges ?? 0;
                        $loyerContrat = $contrat->loyer; // Prix exact du loyer contractuel
                        $totalVerse   = $paiement->total_verse > 0 ? $paiement->total_verse : $paiement->montant;
                        $soldeRestant = $paiement->solde_restant ?? max(0, $loyerContrat - $totalVerse);
                        $loyerAttendu = $paiement->loyer_attendu > 0 ? $paiement->loyer_attendu : $loyerContrat;
                    @endphp

                    {{-- Loyer Principal --}}
                    <tr>
                        <td class="py-6 px-2">
                            <p class="font-black text-slate-800">Loyer Principal</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Prix contractuel — occupation mensuelle du logement</p>
                        </td>
                        <td class="py-6 px-2 text-right font-black text-slate-800">{{ number_format($loyerContrat, 0, ',', ' ') }}</td>
                    </tr>

                    {{-- Charges locatives si applicable --}}
                    @if($charges > 0)
                    <tr>
                        <td class="py-6 px-2">
                            <p class="font-black text-slate-800">Charges Locatives</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Provisions pour charges (eau, électricité, communs)</p>
                        </td>
                        <td class="py-6 px-2 text-right font-black text-slate-800">{{ number_format($charges, 0, ',', ' ') }}</td>
                    </tr>
                    @endif

                    {{-- Pénalités de retard si applicable --}}
                    @if($paiement->penalite > 0)
                    <tr>
                        <td class="py-6 px-2">
                            <p class="font-black text-rose-600">Pénalités de retard</p>
                        </td>
                        <td class="py-6 px-2 text-right font-black text-rose-600">{{ number_format($paiement->penalite, 0, ',', ' ') }}</td>
                    </tr>
                    @endif

                    {{-- Report de solde du mois précédent --}}
                    @if($loyerAttendu > $loyerContrat)
                    <tr>
                        <td class="py-6 px-2">
                            <p class="font-black text-amber-700">Report solde mois précédent</p>
                            <p class="text-[10px] text-amber-500 font-bold uppercase">Solde non réglé reporté sur ce mois</p>
                        </td>
                        <td class="py-6 px-2 text-right font-black text-amber-700">{{ number_format($loyerAttendu - $loyerContrat, 0, ',', ' ') }}</td>
                    </tr>
                    @endif

                    {{-- Total Net Payé --}}
                    <tr class="bg-blue-50/50">
                        <td class="py-6 px-6 rounded-l-[20px]">
                            <p class="text-lg font-black text-slate-900 uppercase tracking-tighter">Total Net Versé</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Règlement reçu le {{ $paiement->date_paiement->format('d/m/Y') }} par {{ str_replace('_', ' ', $paiement->mode_reglement) }}</p>
                        </td>
                        <td class="py-6 px-6 text-right rounded-r-[20px]">
                            <p class="text-3xl font-black text-blue-600 tracking-tight">{{ number_format($totalVerse, 0, ',', ' ') }} <span class="text-xs">GNF</span></p>
                        </td>
                    </tr>

                    {{-- Solde Restant --}}
                    @if($soldeRestant > 0)
                    <tr class="bg-amber-50">
                        <td class="py-6 px-6 rounded-l-[20px]">
                            <p class="text-lg font-black text-amber-700 uppercase tracking-tighter flex items-center gap-2">
                                <i class="fa-solid fa-circle-exclamation"></i> Solde Restant à Payer
                            </p>
                            <p class="text-[10px] text-amber-500 font-bold uppercase">Ce montant reste dû et sera reporté sur le prochain mois</p>
                        </td>
                        <td class="py-6 px-6 text-right rounded-r-[20px]">
                            <p class="text-3xl font-black text-amber-600 tracking-tight">{{ number_format($soldeRestant, 0, ',', ' ') }} <span class="text-xs">GNF</span></p>
                        </td>
                    </tr>
                    @else
                    <tr class="bg-emerald-50/50">
                        <td class="py-4 px-6 rounded-l-[20px]">
                            <p class="font-black text-emerald-700 flex items-center gap-2">
                                <i class="fa-solid fa-check-double"></i> Loyer du mois intégralement soldé
                            </p>
                        </td>
                        <td class="py-4 px-6 text-right rounded-r-[20px]">
                            <p class="font-black text-emerald-600">0 <span class="text-xs">GNF</span></p>
                        </td>
                    </tr>
                    @endif

                </tbody>
            </table>
        </div>

        {{-- Mentions Légales & Signature --}}
        <div class="grid grid-cols-2 gap-12 mt-20 relative z-10">
            <div class="text-xs text-slate-400 leading-relaxed font-medium">
                <p class="mb-4">Cette quittance annule tout reçu à valoir précédemment délivré pour la même période.</p>
                <p>En cas de paiement par chèque, la quittance n'est valable que sous réserve de l'encaissement définitif de celui-ci.</p>
                <div class="mt-8">
                    <p class="font-black text-slate-900 uppercase text-[8px] tracking-widest">Tampon de l'agence</p>
                    <div class="mt-2 w-24 h-24 border-2 border-blue-600/20 rounded-full flex items-center justify-center rotate-12">
                        <p class="text-blue-600 font-black text-[10px] text-center uppercase tracking-tighter">Payé<br>Directement</p>
                    </div>
                </div>
            </div>
            
            <div class="text-right">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Signature & Cachet Officiel</p>
                <div class="inline-block relative">
                    <div class="w-64 h-32 flex items-center justify-end">
                        @php
                            $proprioSignature = $quittance->paiement->contrat->bien->proprietaire->signature;
                            $agencySignature = $settings['signature'] ?? null;
                        @endphp
                        @if(!empty($proprioSignature))
                            <img src="{{ Storage::url($proprioSignature) }}" class="max-h-full max-w-full object-contain grayscale hover:grayscale-0 transition-all duration-500">
                        @elseif(!empty($agencySignature))
                            <img src="{{ Storage::url($agencySignature) }}" class="max-h-full max-w-full object-contain grayscale hover:grayscale-0 transition-all duration-500">
                        @else
                            <div class="w-48 border-b-2 border-slate-200 h-20"></div>
                        @endif
                    </div>
                    <p class="mt-4 font-black text-slate-900 uppercase tracking-widest text-[10px]">{{ $quittance->paiement->contrat->bien->proprietaire->user->name }}</p>
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Le Bailleur / Mandataire</p>
                </div>
            </div>
        </div>

    </div>

    {{-- Footer --}}
    <div class="mt-12 text-center no-print">
        <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.3em]">Édité par le système de gestion GESTLOYER Pro</p>
    </div>

</div>

<style>
@media print {
    @page {
        size: A4;
        margin: 10mm;
    }
    
    body { 
        background: white !important; 
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Masquer les éléments de layout */
    aside, header, footer, .no-print { display: none !important; }
    
    /* Forcer la visibilité du conteneur principal mais sans styles gênants */
    main, .flex, .flex-col { 
        display: block !important; 
        margin: 0 !important; 
        padding: 0 !important; 
        overflow: visible !important;
        background: white !important;
    }
    
    /* Tableau de comptabilité */
    table { width: 100% !important; border-collapse: collapse !important; margin-bottom: 30px !important; }
    th { background-color: #f1f5f9 !important; border-bottom: 2px solid #000 !important; padding: 10px !important; }
    td { border-bottom: 1px solid #eee !important; padding: 12px 10px !important; }
    .bg-slate-50\/50 { background-color: #f8fafc !important; }
    
    #quittance-area { 
        display: block !important;
        width: 100% !important;
        box-shadow: none !important; 
        border: 2px solid #000 !important;
        margin: 0 !important;
        padding: 50px !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        border-radius: 0 !important; /* Plus pro pour un doc officiel */
    }

    .no-print { display: none !important; }

    /* Ajustements de texte pour le print */
    .text-slate-400 { color: #475569 !important; }
    .text-blue-600 { color: #1e40af !important; }
    .bg-slate-900 { background-color: #000 !important; color: #fff !important; }
    
    /* Filigrane plus visible au print */
    .opacity-\[0\.03\] { opacity: 0.1 !important; }
}
</style>

@endsection
