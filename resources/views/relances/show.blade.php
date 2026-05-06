@extends('layouts.app')

@section('title', 'Relance — ' . $relance->contrat->numero_contrat)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Détails relance --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Détails</h2>
        <dl class="space-y-3 text-sm">
            <div>
                <dt class="text-gray-400">Contrat</dt>
                <dd>
                    <a href="{{ route('contrats.show', $relance->contrat) }}"
                       class="text-blue-600 hover:underline font-medium">
                        {{ $relance->contrat->numero_contrat }}
                    </a>
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">Locataire</dt>
                <dd>
                    {{ $relance->contrat->locataire->prenom }}
                    {{ $relance->contrat->locataire->nom }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">Bien</dt>
                <dd>{{ $relance->contrat->bien->libelle }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Niveau</dt>
                <dd>
                    <span class="px-2 py-1 rounded text-xs
                        {{ $relance->niveau === 'niveau_1' ? 'bg-yellow-100 text-yellow-700' :
                           ($relance->niveau === 'niveau_2' ? 'bg-orange-100 text-orange-700' :
                           'bg-red-100 text-red-700') }}">
                        {{ ucfirst(str_replace('_', ' ', $relance->niveau)) }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">Canal</dt>
                <dd class="capitalize">{{ str_replace('_', ' + ', $relance->canal) }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Date d'envoi</dt>
                <dd>{{ $relance->date_envoi->format('d/m/Y à H:i') }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Statut</dt>
                <dd>
                    <span class="px-2 py-1 rounded text-xs
                        {{ $relance->statut === 'envoyee' ? 'bg-blue-100 text-blue-700' :
                           ($relance->statut === 'acquittee' ? 'bg-green-100 text-green-700' :
                           'bg-red-100 text-red-700') }}">
                        {{ ucfirst($relance->statut) }}
                    </span>
                </dd>
            </div>
            @if($relance->acquittePar)
            <div>
                <dt class="text-gray-400">Acquittée par</dt>
                <dd>{{ $relance->acquittePar->name }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Date d'acquittement</dt>
                <dd>{{ $relance->date_acquittement->format('d/m/Y à H:i') }}</dd>
            </div>
            @endif
        </dl>
    </div>

    {{-- Action acquittement --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Action</h2>

        @if($relance->statut === 'envoyee')
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-yellow-700 mb-4">
                ⏳ Cette relance est en attente d'acquittement.
                Cliquez ci-dessous une fois que le locataire a répondu.
            </p>
            <form method="POST" action="{{ route('relances.update', $relance) }}">
                @csrf @method('PUT')
                <button type="submit"
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 text-sm">
                    ✅ Marquer comme acquittée
                </button>
            </form>
        </div>
        @elseif($relance->statut === 'acquittee')
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-sm text-green-700">
                ✅ Cette relance a été acquittée le
                {{ $relance->date_acquittement->format('d/m/Y') }}.
            </p>
        </div>
        @else
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-sm text-red-700">
                ❌ Cette relance a échoué.
            </p>
        </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('relances.index') }}"
               class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 text-sm">
                Retour aux relances
            </a>
        </div>
    </div>

</div>
@endsection