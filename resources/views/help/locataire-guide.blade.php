@extends('layouts.app')

@section('title', 'Guide de Vie Locative - GESTLOYER')

@section('content')

<div class="max-w-5xl mx-auto py-12 px-6">
    <div class="flex items-center justify-between mb-10 no-print">
        <a href="{{ route('help.index') }}" class="text-slate-500 font-black text-xs uppercase tracking-widest flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Retour
        </a>
        <button onclick="window.print()" class="px-6 py-3 bg-blue-600 text-white font-black rounded-2xl text-xs uppercase tracking-widest shadow-xl">
            Imprimer mon Guide
        </button>
    </div>

    <div class="bg-blue-600 rounded-[40px] p-12 text-white shadow-xl shadow-blue-100 mb-10">
        <h1 class="text-4xl font-black mb-4 tracking-tighter">Besoin d'aide ? Votre Guide Locataire Complet</h1>
        <p class="text-blue-50 font-medium text-lg leading-relaxed">Nous avons conçu ce guide pour vous aider à utiliser la plateforme efficacement et sans stress.</p>
    </div>

    <div class="space-y-12 text-slate-700 leading-relaxed">
        <div>
            <h2 class="text-2xl font-black text-slate-800 mb-4 uppercase tracking-tight">1. Paiements & Quittances</h2>
            <p>Pour payer votre loyer, effectuez votre virement ou dépôt, puis allez dans <strong>"Déclarer un paiement"</strong>. Uploadez votre preuve de paiement. Une fois validé, votre quittance sera disponible dans <strong>"Mes Paiements"</strong>.</p>
        </div>

        <div>
            <h2 class="text-2xl font-black text-slate-800 mb-4 uppercase tracking-tight">2. Signalement d'Incidents</h2>
            <p>En cas de panne, utilisez le bouton <strong>"Signaler une panne"</strong>. Décrivez le problème et ajoutez une photo. L'agence traitera votre demande et vous tiendra informé via votre dashboard.</p>
        </div>

        <div>
            <h2 class="text-2xl font-black text-slate-800 mb-4 uppercase tracking-tight">3. Messagerie & Support</h2>
            <p>Utilisez l'onglet <strong>"Messages"</strong> pour toute question administrative. Si c'est urgent, mentionnez-le dans votre message.</p>
        </div>

        <div>
            <h2 class="text-2xl font-black text-slate-800 mb-4 uppercase tracking-tight">4. Mes Documents</h2>
            <p>Votre contrat de bail et autres documents officiels sont archivés dans votre espace. Vous pouvez les télécharger à tout moment.</p>
        </div>
    </div>

    <div class="mt-20 pt-8 border-t border-slate-100 text-center text-slate-400 text-[10px] font-black uppercase tracking-widest">
        Guide Locataire GESTLOYER — Version Professionnelle
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; -webkit-print-color-adjust: exact !important; }
        section, div { page-break-inside: avoid; }
    }
</style>

@endsection
