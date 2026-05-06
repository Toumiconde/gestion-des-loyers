@extends('layouts.app')

@section('title', 'Paramètres')

@section('actions')
    <a href="{{ route('parametres.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
        + Nouveau paramètre
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 text-left">Clé</th>
                <th class="px-6 py-3 text-left">Valeur</th>
                <th class="px-6 py-3 text-left">Description</th>
                <th class="px-6 py-3 text-left">Modifié par</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($parametres as $p)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-mono font-medium text-blue-700">
                    {{ $p->cle }}
                </td>
                <td class="px-6 py-4 font-semibold">{{ $p->valeur }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $p->description ?? '—' }}</td>
                <td class="px-6 py-4">{{ $p->updatedBy?->name ?? '—' }}</td>
                <td class="px-6 py-4 flex gap-3">
                    <a href="{{ route('parametres.edit', $p) }}"
                       class="text-yellow-600 hover:underline">Modifier</a>
                    <form method="POST" action="{{ route('parametres.destroy', $p) }}"
                          onsubmit="return confirm('Supprimer ce paramètre ?')">
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
                    Aucun paramètre configuré.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection