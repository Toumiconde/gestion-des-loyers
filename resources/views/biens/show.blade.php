@extends('layouts.app')

@section('title', $bien->libelle)

@section('actions')
    <a href="{{ route('biens.edit', $bien) }}"
       class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 text-sm">
        Modifier
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Infos bien --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Informations</h2>
        <dl class="space-y-3 text-sm">
            <div>
                <dt class="text-gray-400">Type</dt>
                <dd class="font-medium capitalize">{{ $bien->type }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Adresse</dt>
                <dd>{{ $bien->adresse }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Surface</dt>
                <dd>{{ $bien->surface ? $bien->surface . ' m²' : '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Loyer de base</dt>
                <dd class="font-semibold text-green-600">
                    {{ number_format($bien->loyer_base, 0, ',', ' ') }} GNF
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">Charges</dt>
                <dd>{{ number_format($bien->charges, 0, ',', ' ') }} GNF</dd>
            </div>
            <div>
                <dt class="text-gray-400">Dépôt de garantie</dt>
                <dd>{{ $bien->depot_garantie ? number_format($bien->depot_garantie, 0, ',', ' ') . ' GNF' : '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Propriétaire</dt>
                <dd>
                    <a href="{{ route('proprietaires.show', $bien->proprietaire) }}"
                       class="text-blue-600 hover:underline">
                        {{ $bien->proprietaire->user->name }}
                    </a>
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">Statut</dt>
                <dd>
                    <span class="px-2 py-1 rounded text-xs
                        {{ $bien->statut === 'occupe' ? 'bg-green-100 text-green-700' :
                           ($bien->statut === 'disponible' ? 'bg-yellow-100 text-yellow-700' :
                           'bg-gray-100 text-gray-600') }}">
                        {{ ucfirst($bien->statut) }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>

    {{-- Historique contrats --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-700">
                Contrats ({{ $bien->contrats->count() }})
            </h2>
            @if(!$bien->contratActif)
            <a href="{{ route('contrats.create') }}"
               class="text-blue-600 text-sm hover:underline">
                + Nouveau contrat
            </a>
            @endif
        </div>

        @forelse($bien->contrats as $contrat)
        <div class="flex justify-between items-center py-3 border-b last:border-0">
            <div>
                <p class="font-medium text-gray-800">{{ $contrat->numero_contrat }}</p>
                <p class="text-xs text-gray-400">
                    {{ $contrat->locataire->prenom }} {{ $contrat->locataire->nom }}
                    — Depuis le {{ $contrat->date_debut->format('d/m/Y') }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-2 py-1 rounded text-xs
                    {{ $contrat->statut === 'actif' ? 'bg-green-100 text-green-700' :
                       'bg-gray-100 text-gray-500' }}">
                    {{ ucfirst($contrat->statut) }}
                </span>
                <a href="{{ route('contrats.show', $contrat) }}"
                   class="text-blue-600 text-sm hover:underline">Voir</a>
            </div>
        </div>
        @empty
        <p class="text-gray-400 text-sm">Aucun contrat sur ce bien.</p>
        @endforelse

    </div>

</div>
@endsection