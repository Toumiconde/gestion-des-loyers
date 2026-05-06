@extends('layouts.app')

@section('title', 'Biens')

@section('actions')
    <a href="{{ route('biens.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
        + Nouveau bien
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 text-left">Libellé</th>
                <th class="px-6 py-3 text-left">Type</th>
                <th class="px-6 py-3 text-left">Propriétaire</th>
                <th class="px-6 py-3 text-left">Loyer</th>
                <th class="px-6 py-3 text-left">Statut</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($biens as $bien)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">{{ $bien->libelle }}</td>
                <td class="px-6 py-4 capitalize">{{ $bien->type }}</td>
                <td class="px-6 py-4">{{ $bien->proprietaire->user->name }}</td>
                <td class="px-6 py-4 font-semibold text-green-600">
                    {{ number_format($bien->loyer_base, 0, ',', ' ') }} GNF
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded text-xs
                        {{ $bien->statut === 'occupe' ? 'bg-green-100 text-green-700' :
                           ($bien->statut === 'disponible' ? 'bg-yellow-100 text-yellow-700' :
                           'bg-gray-100 text-gray-600') }}">
                        {{ ucfirst($bien->statut) }}
                    </span>
                </td>
                <td class="px-6 py-4 flex gap-3">
                    <a href="{{ route('biens.show', $bien) }}"
                       class="text-blue-600 hover:underline">Voir</a>
                    <a href="{{ route('biens.edit', $bien) }}"
                       class="text-yellow-600 hover:underline">Modifier</a>
                    <form method="POST" action="{{ route('biens.destroy', $bien) }}"
                          onsubmit="return confirm('Supprimer ce bien ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">
                            Supprimer
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                    Aucun bien enregistré.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $biens->links() }}</div>
</div>
@endsection