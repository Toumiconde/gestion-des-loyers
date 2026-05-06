@extends('layouts.app')

@section('title', 'Documents')

@section('actions')
    <a href="{{ route('documents.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
        + Ajouter un document
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 text-left">Nom</th>
                <th class="px-6 py-3 text-left">Type</th>
                <th class="px-6 py-3 text-left">Taille</th>
                <th class="px-6 py-3 text-left">Uploadé par</th>
                <th class="px-6 py-3 text-left">Date</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($documents as $d)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">{{ $d->nom }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded text-xs
                        {{ $d->type === 'contrat_pdf' ? 'bg-blue-100 text-blue-700' :
                           ($d->type === 'quittance' ? 'bg-green-100 text-green-700' :
                           ($d->type === 'photo' ? 'bg-purple-100 text-purple-700' :
                           ($d->type === 'piece_identite' ? 'bg-yellow-100 text-yellow-700' :
                           'bg-gray-100 text-gray-600'))) }}">
                        {{ ucfirst(str_replace('_', ' ', $d->type)) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-gray-500">
                    {{ $d->taille_ko ? $d->taille_ko . ' Ko' : '—' }}
                </td>
                <td class="px-6 py-4">{{ $d->uploadedBy?->name ?? '—' }}</td>
                <td class="px-6 py-4">{{ $d->created_at->format('d/m/Y') }}</td>
                <td class="px-6 py-4 flex gap-3">
                    <a href="{{ route('documents.show', $d) }}"
                       class="text-blue-600 hover:underline">Voir</a>
                    <form method="POST" action="{{ route('documents.destroy', $d) }}"
                          onsubmit="return confirm('Supprimer ce document ?')">
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
                    Aucun document enregistré.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $documents->links() }}</div>
</div>
@endsection