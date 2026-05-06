@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    {{-- Carte : Total biens --}}
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
        <div class="text-gray-500 text-sm mb-1">Total Biens</div>
        <div class="text-3xl font-bold text-blue-600">{{ $stats['total_biens'] }}</div>
    </div>

    {{-- Carte : Biens occupés --}}
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
        <div class="text-gray-500 text-sm mb-1">Biens Occupés</div>
        <div class="text-3xl font-bold text-green-600">{{ $stats['biens_occupes'] }}</div>
    </div>

    {{-- Carte : Biens disponibles --}}
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-yellow-500">
        <div class="text-gray-500 text-sm mb-1">Biens Disponibles</div>
        <div class="text-3xl font-bold text-yellow-600">{{ $stats['biens_disponibles'] }}</div>
    </div>

    {{-- Carte : Contrats actifs --}}
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-purple-500">
        <div class="text-gray-500 text-sm mb-1">Contrats Actifs</div>
        <div class="text-3xl font-bold text-purple-600">{{ $stats['contrats_actifs'] }}</div>
    </div>

    {{-- Carte : Paiements ce mois --}}
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-indigo-500">
        <div class="text-gray-500 text-sm mb-1">Paiements ce mois</div>
        <div class="text-3xl font-bold text-indigo-600">{{ $stats['paiements_ce_mois'] }}</div>
    </div>

    {{-- Carte : Incidents ouverts --}}
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-orange-500">
        <div class="text-gray-500 text-sm mb-1">Incidents Ouverts</div>
        <div class="text-3xl font-bold text-orange-600">{{ $stats['incidents_ouverts'] }}</div>
    </div>

    {{-- Carte : Loyers en retard --}}
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-red-500">
        <div class="text-gray-500 text-sm mb-1">Loyers en Retard</div>
        <div class="text-3xl font-bold text-red-600">{{ $stats['loyers_en_retard'] }}</div>
    </div>

</div>

{{-- Liens rapides --}}
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Actions rapides</h2>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('biens.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
            + Ajouter un bien
        </a>
        <a href="{{ route('contrats.create') }}"
           class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
            + Nouveau contrat
        </a>
        <a href="{{ route('paiements.create') }}"
           class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 text-sm">
            + Enregistrer un paiement
        </a>
        <a href="{{ route('incidents.create') }}"
           class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700 text-sm">
            + Déclarer un incident
        </a>
    </div>
</div>

@endsection