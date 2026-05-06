@extends('layouts.app')

@section('title', 'Propriétaires')

@section('actions')
    <a href="{{ route('proprietaires.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
        + Nouveau propriétaire
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 text-left">Nom</th>
                <th class="px-6 py-3 text-left">Email</th>
                <th class="px-6 py-3 text-left">Téléphone</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($proprietaires as $p)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">{{ $p->user->name }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $p->user->email }}</td>
                <td class="px-6 py-4">{{ $p->telephone ?? '—' }}</td>
                <td class="px-6 py-4 flex gap-3">
                    <a href="{{ route('proprietaires.show', $p) }}"
                       class="text-blue-600 hover:underline">Voir</a>
                    <a href="{{ route('proprietaires.edit', $p) }}"
                       class="text-yellow-600 hover:underline">Modifier</a>
                    <form method="POST" action="{{ route('proprietaires.destroy', $p) }}"
                          onsubmit="return confirm('Supprimer ce propriétaire ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">
                            Supprimer
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                    Aucun propriétaire enregistré.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $proprietaires->links() }}</div>
</div>
@endsection