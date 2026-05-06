@extends('layouts.app')

@section('title', 'Locataires')

@section('actions')
    <a href="{{ route('locataires.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
        + Nouveau locataire
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 text-left">Nom complet</th>
                <th class="px-6 py-3 text-left">Email</th>
                <th class="px-6 py-3 text-left">Téléphone</th>
                <th class="px-6 py-3 text-left">Contrat actif</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($locataires as $l)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">{{ $l->prenom }} {{ $l->nom }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $l->email ?? '—' }}</td>
                <td class="px-6 py-4">{{ $l->telephone ?? '—' }}</td>
                <td class="px-6 py-4">
                    @if($l->contratActif)
                        <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-700">
                            {{ $l->contratActif->bien->libelle }}
                        </span>
                    @else
                        <span class="text-gray-400 text-xs">Aucun</span>
                    @endif
                </td>
                <td class="px-6 py-4 flex gap-3">
                    <a href="{{ route('locataires.show', $l) }}"
                       class="text-blue-600 hover:underline">Voir</a>
                    <a href="{{ route('locataires.edit', $l) }}"
                       class="text-yellow-600 hover:underline">Modifier</a>
                    <form method="POST" action="{{ route('locataires.destroy', $l) }}"
                          onsubmit="return confirm('Supprimer ce locataire ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">
                            Supprimer
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                    Aucun locataire enregistré.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $locataires->links() }}</div>
</div>
@endsection