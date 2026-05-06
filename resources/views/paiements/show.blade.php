@extends('layouts.app')

@section('title', 'Paiement — ' . \Carbon\Carbon::parse($paiement->mois_concerne)->format('F Y'))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Détails paiement --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Détails du paiement</h2>
        <dl class="space-y-3 text-sm">
            <div>
                <dt class="text-gray-400">Contrat</dt>
                <dd>
                    <a href="{{ route('contrats.show', $paiement->contrat) }}"
                       class="text-blue-600 hover:underline font-medium">
                        {{ $paiement->contrat->numero_contrat }}
                    </a>
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">Locataire</dt>
                <dd>
                    {{ $paiement->contrat->locataire->prenom }}
                    {{ $paiement->contrat->locataire->nom }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">Bien</dt>
                <dd>{{ $paiement->contrat->bien->libelle }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Mois concerné</dt>
                <dd class="font-medium">
                    {{ \Carbon\Carbon::parse($paiement->mois_concerne)->format('F Y') }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">Montant payé</dt>
                <dd class="font-semibold text-green-600 text-lg">
                    {{ number_format($paiement->montant, 0, ',', ' ') }} GNF
                </dd>
            </div>
            @if($paiement->penalite > 0)
            <div>
                <dt class="text-gray-400">Pénalité</dt>
                <dd class="text-red-600">
                    {{ number_format($paiement->penalite, 0, ',', ' ') }} GNF
                </dd>
            </div>
            @endif
            <div>
                <dt class="text-gray-400">Date de paiement</dt>
                <dd>{{ $paiement->date_paiement->format('d/m/Y') }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Mode de règlement</dt>
                <dd class="capitalize">{{ $paiement->mode_reglement }}</dd>
            </div>
            @if($paiement->reference)
            <div>
                <dt class="text-gray-400">Référence</dt>
                <dd>{{ $paiement->reference }}</dd>
            </div>
            @endif
            @if($paiement->notes)
            <div>
                <dt class="text-gray-400">Notes</dt>
                <dd>{{ $paiement->notes }}</dd>
            </div>
            @endif
            <div>
                <dt class="text-gray-400">Statut</dt>
                <dd>
                    <span class="px-2 py-1 rounded text-xs
                        {{ $paiement->statut === 'paye' ? 'bg-green-100 text-green-700' :
                           ($paiement->statut === 'partiel' ? 'bg-yellow-100 text-yellow-700' :
                           'bg-red-100 text-red-700') }}">
                        {{ ucfirst($paiement->statut) }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>

    {{-- Quittance --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Quittance</h2>
        @if($paiement->quittance)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-sm font-medium text-green-700 mb-2">
                    ✅ Quittance générée
                </p>
                <dl class="space-y-2 text-sm">
                    <div>
                        <dt class="text-gray-400">Numéro</dt>
                        <dd class="font-medium">{{ $paiement->quittance->numero_quittance }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Générée le</dt>
                        <dd>{{ $paiement->quittance->created_at->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Envoyée par email</dt>
                        <dd>{{ $paiement->quittance->envoye_par_email ? 'Oui' : 'Non' }}</dd>
                    </div>
                </dl>
                <a href="{{ route('quittances.show', $paiement->quittance) }}"
                   class="mt-4 inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
                    Voir la quittance
                </a>
            </div>
        @else
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                <p class="text-gray-400 text-sm">Aucune quittance pour ce paiement.</p>
            </div>
        @endif
    </div>

</div>
@endsection