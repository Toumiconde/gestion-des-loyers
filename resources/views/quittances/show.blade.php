@extends('layouts.app')

@section('title', 'Quittance — ' . $quittance->numero_quittance)

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Quittance imprimable --}}
    <div class="bg-white rounded-xl shadow p-8 mb-6" id="quittance">

        {{-- Entête --}}
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-2xl font-bold text-blue-700">Billy Condé Immo</h1>
                <p class="text-gray-500 text-sm">Gestion Immobilière</p>
            </div>
            <div class="text-right">
                <p class="text-lg font-bold text-gray-700">QUITTANCE DE LOYER</p>
                <p class="text-sm text-gray-500">N° {{ $quittance->numero_quittance }}</p>
                <p class="text-sm text-gray-500">
                    Émise le {{ $quittance->created_at->format('d/m/Y') }}
                </p>
            </div>
        </div>

        <hr class="mb-6">

        {{-- Infos mois --}}
        <div class="bg-blue-50 rounded-lg p-4 mb-6 text-center">
            <p class="text-sm text-gray-500">Quittance pour le mois de</p>
            <p class="text-2xl font-bold text-blue-700">
                {{ \Carbon\Carbon::parse($quittance->paiement->mois_concerne)->format('F Y') }}
            </p>
        </div>

        {{-- Parties --}}
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Bailleur</h3>
                <p class="font-medium text-gray-800">
                    {{ $quittance->paiement->contrat->bien->proprietaire->user->name }}
                </p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Locataire</h3>
                <p class="font-medium text-gray-800">
                    {{ $quittance->paiement->contrat->locataire->prenom }}
                    {{ $quittance->paiement->contrat->locataire->nom }}
                </p>
            </div>
        </div>

        {{-- Bien --}}
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Bien loué</h3>
            <p class="font-medium text-gray-800">
                {{ $quittance->paiement->contrat->bien->libelle }}
            </p>
            <p class="text-sm text-gray-500">
                {{ $quittance->paiement->contrat->bien->adresse }}
            </p>
        </div>

        {{-- Montants --}}
        <div class="border rounded-lg overflow-hidden mb-6">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-600">Désignation</th>
                        <th class="px-4 py-3 text-right text-gray-600">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t">
                        <td class="px-4 py-3">Loyer</td>
                        <td class="px-4 py-3 text-right">
                            {{ number_format($quittance->paiement->contrat->loyer, 0, ',', ' ') }} GNF
                        </td>
                    </tr>
                    @if($quittance->paiement->penalite > 0)
                    <tr class="border-t">
                        <td class="px-4 py-3 text-red-600">Pénalité de retard</td>
                        <td class="px-4 py-3 text-right text-red-600">
                            {{ number_format($quittance->paiement->penalite, 0, ',', ' ') }} GNF
                        </td>
                    </tr>
                    @endif
                    <tr class="border-t bg-blue-50 font-bold">
                        <td class="px-4 py-3 text-blue-700">Total payé</td>
                        <td class="px-4 py-3 text-right text-blue-700">
                            {{ number_format($quittance->paiement->montant, 0, ',', ' ') }} GNF
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Mode paiement --}}
        <div class="text-sm text-gray-500 mb-8">
            <p>
                Paiement reçu le
                <span class="font-medium text-gray-700">
                    {{ $quittance->paiement->date_paiement->format('d/m/Y') }}
                </span>
                par
                <span class="font-medium text-gray-700 capitalize">
                    {{ $quittance->paiement->mode_reglement }}
                </span>
                @if($quittance->paiement->reference)
                    — Réf : {{ $quittance->paiement->reference }}
                @endif
            </p>
        </div>

        {{-- Signature --}}
        <div class="flex justify-end">
            <div class="text-center">
                <p class="text-sm text-gray-500 mb-8">Signature du bailleur</p>
                <div class="border-t border-gray-300 w-48 pt-2">
                    <p class="text-xs text-gray-400">
                        {{ $quittance->paiement->contrat->bien->proprietaire->user->name }}
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- Boutons --}}
    <div class="flex gap-3">
        <button onclick="window.print()"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 text-sm">
            🖨️ Imprimer
        </button>
        <a href="{{ route('quittances.index') }}"
           class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 text-sm">
            Retour
        </a>
    </div>

</div>
@endsection
