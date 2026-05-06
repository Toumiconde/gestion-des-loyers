@extends('layouts.app')

@section('title', $locataire->prenom . ' ' . $locataire->nom)

@section('actions')
    <a href="{{ route('locataires.edit', $locataire) }}"
       class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 text-sm">
        Modifier
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Infos locataire --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Informations</h2>
        <dl class="space-y-3 text-sm">
            <div>
                <dt class="text-gray-400">Nom complet</dt>
                <dd class="font-medium">{{ $locataire->prenom }} {{ $locataire->nom }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Email</dt>
                <dd>{{ $locataire->email ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Téléphone</dt>
                <dd>{{ $locataire->telephone ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Adresse</dt>
                <dd>{{ $locataire->adresse ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Pièce d'identité</dt>
                <dd>{{ $locataire->piece_identite ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Enregistré le</dt>
                <dd>{{ $locataire->created_at->format('d/m/Y') }}</dd>
            </div>
        </dl>
    </div>

    {{-- Historique contrats --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-700">
                Contrats ({{ $locataire->contrats->count() }})
            </h2>
            <a href="{{ route('contrats.create') }}"
               class="text-blue-600 text-sm hover:underline">
                + Nouveau contrat
            </a>
        </div>

        @forelse($locataire->contrats as $contrat)
        <div class="flex justify-between items-center py-3 border-b last:border-0">
            <div>
                <p class="font-medium text-gray-800">{{ $contrat->numero_contrat }}</p>
                <p class="text-xs text-gray-400">
                    {{ $contrat->bien->libelle }}
                    — Depuis le {{ $contrat->date_debut->format('d/m/Y') }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-green-600">
                    {{ number_format($contrat->loyer, 0, ',', ' ') }} GNF
                </span>
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
        <p class="text-gray-400 text-sm">Aucun contrat pour ce locataire.</p>
        @endforelse

    </div>

</div>
@endsection