@extends('layouts.app')

@section('title', 'Quittances')

@section('content')
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 text-left">Numéro</th>
                <th class="px-6 py-3 text-left">Locataire</th>
                <th class="px-6 py-3 text-left">Bien</th>
                <th class="px-6 py-3 text-left">Mois</th>
                <th class="px-6 py-3 text-left">Montant</th>
                <th class="px-6 py-3 text-left">Email envoyé</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($quittances as $q)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">{{ $q->numero_quittance }}</td>
                <td class="px-6 py-4">
                    {{ $q->paiement->contrat->locataire->prenom }}
                    {{ $q->paiement->contrat->locataire->nom }}
                </td>
                <td class="px-6 py-4">{{ $q->paiement->contrat->bien->libelle }}</td>
                <td class="px-6 py-4">
                    {{ \Carbon\Carbon::parse($q->paiement->mois_concerne)->format('F Y') }}
                </td>
                <td class="px-6 py-4 font-semibold text-green-600">
                    {{ number_format($q->paiement->montant, 0, ',', ' ') }} GNF
                </td>
                <td class="px-6 py-4">
                    @if($q->envoye_par_email)
                        <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-700">
                            Oui
                        </span>
                    @else
                        <span class="px-2 py-1 rounded text-xs bg-gray-100 text-gray-500">
                            Non
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <a href="{{ route('quittances.show', $q) }}"
                       class="text-blue-600 hover:underline">Voir</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                    Aucune quittance générée.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $quittances->links() }}</div>
</div>
@endsection