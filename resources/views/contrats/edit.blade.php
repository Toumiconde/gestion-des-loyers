@extends('layouts.app')

@section('title', 'Modifier le contrat — ' . $contrat->numero_contrat)

@section('content')
<div class="bg-white rounded-xl shadow p-8 max-w-2xl">
    <form method="POST" action="{{ route('contrats.update', $contrat) }}">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 gap-6">

            <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-600">
                <p><span class="font-medium">Bien :</span> {{ $contrat->bien->libelle }}</p>
                <p><span class="font-medium">Locataire :</span> {{ $contrat->locataire->prenom }} {{ $contrat->locataire->nom }}</p>
                <p><span class="font-medium">Numéro :</span> {{ $contrat->numero_contrat }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loyer (GNF)</label>
                    <input type="number" name="loyer"
                           value="{{ old('loyer', $contrat->loyer) }}"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dépôt de garantie (GNF)</label>
                    <input type="number" name="depot_garantie"
                           value="{{ old('depot_garantie', $contrat->depot_garantie) }}"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jour d'échéance</label>
                    <input type="number" name="jour_echeance"
                           value="{{ old('jour_echeance', $contrat->jour_echeance) }}"
                           min="1" max="28"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Taux de révision (%)</label>
                    <input type="number" name="taux_revision"
                           value="{{ old('taux_revision', $contrat->taux_revision) }}"
                           step="0.01"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

        </div>

        <div class="flex gap-3 mt-8">
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Mettre à jour
            </button>
            <a href="{{ route('contrats.show', $contrat) }}"
               class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200">
                Annuler
            </a>
        </div>

    </form>
</div>
@endsection