@extends('layouts.app')

@section('title', 'Contrat — ' . $contrat->numero_contrat)

@section('actions')
    @if($contrat->statut === 'actif')
    <a href="{{ route('contrats.edit', $contrat) }}"
       class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 text-sm">
        Modifier
    </a>
    <form method="POST" action="{{ route('contrats.destroy', $contrat) }}"
          onsubmit="return confirm('Résilier ce contrat ?')" class="inline">
        @csrf @method('DELETE')
        <select name="motif_resiliation" required
                class="border rounded px-2 py-2 text-sm">
            <option value="">-- Motif résiliation --</option>
            <option value="depart_volontaire">Départ volontaire</option>
            <option value="non_paiement">Non paiement</option>
            <option value="fin_bail">Fin de bail</option>
            <option value="autre">Autre</option>
        </select>
        <button type="submit"
                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm ml-2">
            Résilier
        </button>
    </form>
    @endif
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Infos contrat --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Informations</h2>
        <dl class="space-y-3 text-sm">
            <div>
                <dt class="text-gray-400">Numéro</dt>
                <dd class="font-medium">{{ $contrat->numero_contrat }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Bien</dt>
                <dd>
                    <a href="{{ route('biens.show', $contrat->bien) }}"
                       class="text-blue-600 hover:underline">
                        {{ $contrat->bien->libelle }}
                    </a>
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">Locataire</dt>
                <dd>
                    <a href="{{ route('locataires.show', $contrat->locataire) }}"
                       class="text-blue-600 hover:underline">
                        {{ $contrat->locataire->prenom }} {{ $contrat->locataire->nom }}
                    </a>
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">Loyer mensuel</dt>
                <dd class="font-semibold text-green-600">
                    {{ number_format($contrat->loyer, 0, ',', ' ') }} GNF
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">Dépôt de garantie</dt>
                <dd>{{ number_format($contrat->depot_garantie, 0, ',', ' ') }} GNF</dd>
            </div>
            <div>
                <dt class="text-gray-400">Date de début</dt>
                <dd>{{ $contrat->date_debut->format('d/m/Y') }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Date de fin</dt>
                <dd>{{ $contrat->date_fin ? $contrat->date_fin->format('d/m/Y') : 'Indéterminée' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Jour d'échéance</dt>
                <dd>Le {{ $contrat->jour_echeance }} de chaque mois</dd>
            </div>
            <div>
                <dt class="text-gray-400">Statut</dt>
                <dd>
                    <span class="px-2 py-1 rounded text-xs
                        {{ $contrat->statut === 'actif' ? 'bg-green-100 text-green-700' :
                           ($contrat->statut === 'resilie' ? 'bg-red-100 text-red-700' :
                           'bg-gray-100 text-gray-600') }}">
                        {{ ucfirst($contrat->statut) }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>

    {{-- Historique paiements --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-700">
                Paiements ({{ $contrat->paiements->count() }})
            </h2>
            @if($contrat->statut === 'actif')
            <a href="{{ route('paiements.create') }}"
               class="text-blue-600 text-sm hover:underline">
                + Enregistrer un paiement
            </a>
            @endif
        </div>

        @forelse($contrat->paiements as $paiement)
        <div class="flex justify-between items-center py-3 border-b last:border-0">
            <div>
                <p class="font-medium text-gray-800">
                    {{ \Carbon\Carbon::parse($paiement->mois_concerne)->format('F Y') }}
                </p>
                <p class="text-xs text-gray-400">
                    Payé le {{ $paiement->date_paiement->format('d/m/Y') }}
                    — {{ ucfirst($paiement->mode_reglement) }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="font-semibold text-green-600">
                    {{ number_format($paiement->montant, 0, ',', ' ') }} GNF
                </span>
                <span class="px-2 py-1 rounded text-xs
                    {{ $paiement->statut === 'paye' ? 'bg-green-100 text-green-700' :
                       ($paiement->statut === 'partiel' ? 'bg-yellow-100 text-yellow-700' :
                       'bg-red-100 text-red-700') }}">
                    {{ ucfirst($paiement->statut) }}
                </span>
                <a href="{{ route('paiements.show', $paiement) }}"
                   class="text-blue-600 text-sm hover:underline">Voir</a>
            </div>
        </div>
        @empty
        <p class="text-gray-400 text-sm">Aucun paiement enregistré.</p>
        @endforelse

    </div>

</div>
@endsection