@extends('layouts.app')

@section('title', 'Manuel de Gestion Opérationnelle - GESTLOYER')

@section('content')

<div class="max-w-5xl mx-auto py-12 px-6">
    <div class="flex items-center justify-between mb-10 no-print">
        <a href="{{ route('help.index') }}" class="text-slate-500 font-black text-xs uppercase tracking-widest flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Retour
        </a>
        <button onclick="window.print()" class="px-6 py-3 bg-slate-900 text-white font-black rounded-2xl text-xs uppercase tracking-widest shadow-xl">
            Imprimer le Manuel Gestionnaire
        </button>
    </div>

    <div class="bg-white rounded-[40px] p-12 shadow-sm border border-slate-100 mb-10">
        <h1 class="text-4xl font-black text-slate-800 mb-4 tracking-tighter">Guide d'Utilisation - Espace Gestionnaire</h1>
        <p class="text-slate-500 font-medium text-lg leading-relaxed">Procédures standards pour la gestion du parc immobilier et du suivi technique.</p>
    </div>

    <div class="space-y-12 text-slate-700 leading-relaxed">
        <div>
            <h2 class="text-2xl font-black text-slate-800 mb-4 uppercase tracking-tight">1. Gestion des Biens & Contrats</h2>
            <p>Le gestionnaire assure la mise à jour des fiches biens et la création des baux. Vérifiez toujours la caution avant de valider un contrat.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-800 mb-4 uppercase tracking-tight">2. Validation des Paiements</h2>
            <p>Vérifiez les preuves de paiement des locataires. Une fois les fonds confirmés, passez le statut à "Payé" pour générer la quittance.</p>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-800 mb-4 uppercase tracking-tight">3. Maintenance</h2>
            <p>Attribuez les incidents signalés aux artisans et suivez les réparations jusqu'à la clôture.</p>
        </div>
    </div>

    <div class="mt-20 pt-8 border-t border-slate-100 text-center text-slate-400 text-[10px] font-black uppercase tracking-widest">
        Document Interne - GESTLOYER
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; -webkit-print-color-adjust: exact !important; }
    }
</style>

@endsection
