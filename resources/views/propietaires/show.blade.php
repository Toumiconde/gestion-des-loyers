@extends('layouts.app')

@section('title', 'Propriétaire — ' . $proprietaire->user->name)

@section('actions')
    <a href="{{ route('proprietaires.edit', $proprietaire) }}"
       class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 text-sm">
        Modifier
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Infos propriétaire --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Informations</h2>
        <dl class="space-y-3 text-sm">
            <div>
                <dt class="text-gray-400">Nom</dt>
                <dd class="font-medium">{{ $proprietaire->user->name }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Email</dt>
                <dd>{{ $proprietaire->user->email }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Téléphone</dt>
                <dd>{{ $proprietaire->telephone ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Adresse</dt>
                <dd>{{ $proprietaire->adresse ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">RIB Bancaire</dt>
                <dd>{{ $proprietaire->rib_bancaire ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Membre depuis</dt>
                <dd>{{ $proprietaire->created_at->format('d/m/Y') }}</dd>
            </div>
        </dl>
    </div>

    {{-- Liste de ses biens --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-700">
                Biens ({{ $proprietaire->biens->count() }})
            </h2>
            <a href="{{ route('biens.create') }}"
               class="text-blue-600 text-sm hover:underline">
                + Ajouter un bien
            </a>
        </div>

        @forelse($proprietaire->biens as $bien)
        <div class="flex justify-between items-center py-3 border-b last:border-0">
            <div>
                <p class="font-medium text-gray-800">{{ $bien->libelle }}</p>
                <p class="text-xs text-gray-400">{{ $bien->adresse }}</p>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm font-semibold text-green-600">
                    {{ number_format($bien->loyer_base, 0, ',', ' ') }} GNF
                </span>
                <span class="px-2 py-1 rounded text-xs
                    {{ $bien->statut === 'occupe' ? 'bg-green-100 text-green-700' :
                       ($bien->statut === 'disponible' ? 'bg-yellow-100 text-yellow-700' :
                       'bg-gray-100 text-gray-600') }}">
                    {{ ucfirst($bien->statut) }}
                </span>
                <a href="{{ route('biens.show', $bien) }}"
                   class="text-blue-600 text-sm hover:underline">Voir</a>
            </div>
        </div>
        @empty
        <p class="text-gray-400 text-sm">Aucun bien enregistré pour ce propriétaire.</p>
        @endforelse

    </div>

</div>
@endsection
