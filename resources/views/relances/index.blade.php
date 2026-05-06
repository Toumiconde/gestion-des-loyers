@extends('layouts.app')

@section('title', 'Relances')

@section('content')
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 text-left">Contrat</th>
                <th class="px-6 py-3 text-left">Locataire</th>
                <th class="px-6 py-3 text-left">Niveau</th>
                <th class="px-6 py-3 text-left">Canal</th>
                <th class="px-6 py-3 text-left">Statut</th>
                <th class="px-6 py-3 text-left">Date envoi</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($relances as $r)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">{{ $r->contrat->numero_contrat }}</td>
                <td class="px-6 py-4">
                    {{ $r->contrat->locataire->prenom }}
                    {{ $r->contrat->locataire->nom }}
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded text-xs
                        {{ $r->niveau === 'niveau_1' ? 'bg-yellow-100 text-yellow-700' :
                           ($r->niveau === 'niveau_2' ? 'bg-orange-100 text-orange-700' :
                           'bg-red-100 text-red-700') }}">
                        {{ ucfirst(str_replace('_', ' ', $r->niveau)) }}
                    </span>
                </td>
                <td class="px-6 py-4 capitalize">
                    {{ str_replace('_', ' + ', $r->canal) }}
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded text-xs
                        {{ $r->statut === 'envoyee' ? 'bg-blue-100 text-blue-700' :
                           ($r->statut === 'acquittee' ? 'bg-green-100 text-green-700' :
                           'bg-red-100 text-red-700') }}">
                        {{ ucfirst($r->statut) }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    {{ $r->date_envoi->format('d/m/Y') }}
                </td>
                <td class="px-6 py-4">
                    <a href="{{ route('relances.show', $r) }}"
                       class="text-blue-600 hover:underline">Voir</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                    Aucune relance envoyée.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $relances->links() }}</div>
</div>
@endsection