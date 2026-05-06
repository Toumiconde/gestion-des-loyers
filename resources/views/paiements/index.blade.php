@extends('layouts.app')

@section('title', 'Paiements')

@section('actions')
    <a href="{{ route('paiements.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
        + Enregistrer un paiement
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 text-left">Contrat</th>
                <th class="px-6 py-3 text-left">Locataire</th>
                <th class="px-6 py-3 text-left">Mois concerné</th>
                <th class="px-6 py-3 text-left">Montant</th>
                <th class="px-6 py-3 text-left">Mode</th>
                <th class="px-6 py-3 text-left">Statut</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($paiements as $p)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">{{ $p->contrat->numero_contrat }}</td>
                <td class="px-6 py-4">
                    {{ $p->contrat->locataire->prenom }} {{ $p->contrat->locataire->nom }}
                </td>
                <td class="px-6 py-4">
                    {{ \Carbon\Carbon::parse($p->mois_concerne)->format('F Y') }}
                </td>
                <td class="px-6 py-4 font-semibold text-green-600">
                    {{ number_format($p->montant, 0, ',', ' ') }} GNF
                </td>
                <td class="px-6 py-4 capitalize">{{ $p->mode_reglement }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded text-xs
                        {{ $p->statut === 'paye' ? 'bg-green-100 text-green-700' :
                           ($p->statut === 'partiel' ? 'bg-yellow-100 text-yellow-700' :
                           ($p->statut === 'en_retard' ? 'bg-red-100 text-red-700' :
                           'bg-gray-100 text-gray-600')) }}">
                        {{ ucfirst($p->statut) }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <a href="{{ route('paiements.show', $p) }}"
                       class="text-blue-600 hover:underline">Voir</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                    Aucun paiement enregistré.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $paiements->links() }}</div>
</div>
@endsection
