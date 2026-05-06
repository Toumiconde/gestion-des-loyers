@extends('layouts.app')

@section('title', 'Paramètre — ' . $parametre->cle)

@section('actions')
    <a href="{{ route('parametres.edit', $parametre) }}"
       class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 text-sm">
        Modifier
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow p-6 max-w-xl">
    <dl class="space-y-4 text-sm">
        <div>
            <dt class="text-gray-400 mb-1">Clé</dt>
            <dd class="font-mono font-bold text-blue-700 text-lg">
                {{ $parametre->cle }}
            </dd>
        </div>
        <div>
            <dt class="text-gray-400 mb-1">Valeur</dt>
            <dd class="font-semibold text-gray-800 text-lg">
                {{ $parametre->valeur }}
            </dd>
        </div>
        <div>
            <dt class="text-gray-400 mb-1">Description</dt>
            <dd>{{ $parametre->description ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-gray-400 mb-1">Dernière modification par</dt>
            <dd>{{ $parametre->updatedBy?->name ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-gray-400 mb-1">Créé le</dt>
            <dd>{{ $parametre->created_at->format('d/m/Y à H:i') }}</dd>
        </div>
        <div>
            <dt class="text-gray-400 mb-1">Modifié le</dt>
            <dd>{{ $parametre->updated_at->format('d/m/Y à H:i') }}</dd>
        </div>
    </dl>
</div>
@endsection