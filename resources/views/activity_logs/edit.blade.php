@extends('layouts.app')

@section('title', 'Action non autorisée')

@section('content')
<div class="bg-white rounded-xl shadow p-8 max-w-xl text-center">
    <div class="text-6xl mb-4"></div>
    <h2 class="text-xl font-semibold text-gray-700 mb-2">
        Modification impossible
    </h2>
    <p class="text-gray-500 text-sm mb-6">
        Les logs d'activité sont des enregistrements
        immuables. Ils ne peuvent jamais être modifiés
        pour garantir l'intégrité de la traçabilité.
    </p>
    <a href="{{ route('activity-logs.index') }}"
       class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 text-sm">
        Retour aux logs
    </a>
</div>
@endsection