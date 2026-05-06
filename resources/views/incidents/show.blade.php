@extends('layouts.app')

@section('title', 'Incident — ' . $incident->titre)

@section('actions')
    <a href="{{ route('incidents.edit', $incident) }}"
       class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 text-sm">
        Modifier le statut
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Détails incident --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Détails</h2>
        <dl class="space-y-3 text-sm">
            <div>
                <dt class="text-gray-400">Titre</dt>
                <dd class="font-medium">{{ $incident->titre }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Contrat</dt>
                <dd>
                    <a href="{{ route('contrats.show', $incident->contrat) }}"
                       class="text-blue-600 hover:underline">
                        {{ $incident->contrat->numero_contrat }}
                    </a>
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">Bien</dt>
                <dd>{{ $incident->contrat->bien->libelle }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Locataire</dt>
                <dd>
                    {{ $incident->contrat->locataire->prenom }}
                    {{ $incident->contrat->locataire->nom }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">Déclaré par</dt>
                <dd>{{ $incident->declarePar?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Date de déclaration</dt>
                <dd>{{ $incident->created_at->format('d/m/Y') }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Date de résolution</dt>
                <dd>{{ $incident->date_resolution?->format('d/m/Y') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Priorité</dt>
                <dd>
                    <span class="px-2 py-1 rounded text-xs
                        {{ $incident->priorite === 'urgent' ? 'bg-red-100 text-red-700' :
                           ($incident->priorite === 'moyen' ? 'bg-yellow-100 text-yellow-700' :
                           'bg-gray-100 text-gray-600') }}">
                        {{ ucfirst($incident->priorite) }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">Statut</dt>
                <dd>
                    <span class="px-2 py-1 rounded text-xs
                        {{ $incident->statut === 'ouvert' ? 'bg-red-100 text-red-700' :
                           ($incident->statut === 'en_cours' ? 'bg-yellow-100 text-yellow-700' :
                           ($incident->statut === 'resolu' ? 'bg-green-100 text-green-700' :
                           'bg-gray-100 text-gray-600')) }}">
                        {{ ucfirst(str_replace('_', ' ', $incident->statut)) }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>

    {{-- Description --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Description</h2>
        <p class="text-gray-600 text-sm leading-relaxed">
            {{ $incident->description }}
        </p>
    </div>

</div>
@endsection