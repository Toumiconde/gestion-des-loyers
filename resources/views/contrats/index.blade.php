@extends('layouts.app')

@section('title', 'Contrats')

@section('actions')
    <a href="{{ route('contrats.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
        + Nouveau contrat
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 text-left">Numéro</th>
                <th class="px-6 py-3 text-left">Bien</th>
                <th class="px-6 py-3 text-left">Locataire</th>
                <th class="px-6 py-3 text-left">Loyer</th>
                <th class="px-6 py-3 text-left">Début</th>
                <th class="px-6 py-3 text-left">Statut</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($contrats as $c)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">{{ $c->numero_contrat }}</td>
                <td class="px-6 py-4">{{ $c->bien->libelle }}</td>
                <td class="px-6 py-4">{{ $c->locataire->prenom }} {{ $c->locataire->nom }}</td>
                <td class="px-6 py-4 font-semibold text-green-600">
                    {{ number_format($c->loyer, 0, ',', ' ') }} GNF
                </td>
                <td class="px-6 py-4">{{ $c->date_debut->format('d/m/Y') }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded text-xs
                        {{ $c->statut === 'actif' ? 'bg-green-100 text-green-700' :
                           ($c->statut === 'resilie' ? 'bg-red-100 text-red-700' :
                           'bg-gray-100 text-gray-600') }}">
                        {{ ucfirst($c->statut) }}
                    </span>
                </td>
                <td class="px-6 py-4 flex gap-3">
                    <a href="{{ route('contrats.show', $c) }}"
                       class="text-blue-600 hover:underline">Voir</a>
                    <a href="{{ route('contrats.edit', $c) }}"
                       class="text-yellow-600 hover:underline">Modifier</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                    Aucun contrat enregistré.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $contrats->links() }}</div>
</div>
@endsection