@extends('layouts.app')

@section('title', 'Action non autorisée')

@section('content')
<div class="bg-white rounded-xl shadow p-8 max-w-xl text-center">
    <div class="text-6xl mb-4"></div>
    <h2 class="text-xl font-semibold text-gray-700 mb-2">
        Création manuelle impossible
    </h2>
    <p class="text-gray-500 text-sm mb-6">
        Les quittances sont générées automatiquement
        lors de l'enregistrement d'un paiement complet.
    </p>
    <a href="{{ route('paiements.create') }}"
       class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 text-sm">
        Enregistrer un paiement
    </a>
</div>
@endsection
