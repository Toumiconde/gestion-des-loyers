@extends('layouts.app')

@section('title', 'Incidents')

@section('actions')
    <a href="{{ route('incidents.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
        + Déclarer un incident
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 text-left">Titre</th>
                <th class="px-6 py-3 text-left">Contrat</th>
                <th class="px-6 py-3 text-left">Locataire</th>
                <th class="px-6 py-3 text-left">Priorité</th>
                <th class="px-6 py-3 text-left">Statut</th>
                <th class="px-6 py-3 text-left">Date</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($incidents as $i)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">{{ $i->titre }}</td>
                <td class="px-6 py-4">{{ $i->contrat->numero_contrat }}</td>
                <td class="px-6 py-4">
                    {{ $i->contrat->locataire->prenom }}
                    {{ $i->contrat->locataire->nom }}
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded text-xs
                        {{ $i->priorite === 'urgent' ? 'bg-red-100 text-red-700' :
                           ($i->priorite === 'moyen' ? 'bg-yellow-100 text-yellow-700' :
                           'bg-gray-100 text-gray-600') }}">
                        {{ ucfirst($i->priorite) }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded text-xs
                        {{ $i->statut === 'ouvert' ? 'bg-red-100 text-red-700' :
                           ($i->statut === 'en_cours' ? 'bg-yellow-100 text-yellow-700' :
                           ($i->statut === 'resolu' ? 'bg-green-100 text-green-700' :
                           'bg-gray-100 text-gray-600')) }}">
                        {{ ucfirst(str_replace('_', ' ', $i->statut)) }}
                    </span>
                </td>
                <td class="px-6 py-4">{{ $i->created_at->format('d/m/Y') }}</td>
                <td class="px-6 py-4 flex gap-3">
                    <a href="{{ route('incidents.show', $i) }}"
                       class="text-blue-600 hover:underline">Voir</a>
                    <a href="{{ route('incidents.edit', $i) }}"
                       class="text-yellow-600 hover:underline">Modifier</a>
                    <form method="POST" action="{{ route('incidents.destroy', $i) }}"
                          onsubmit="return confirm('Supprimer cet incident ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">
                            Supprimer
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                    Aucun incident déclaré.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $incidents->links() }}</div>
</div>
@endsection