@extends('layouts.app')

@section('title', 'Document — ' . $document->nom)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Détails document --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Informations</h2>
        <dl class="space-y-3 text-sm">
            <div>
                <dt class="text-gray-400">Nom</dt>
                <dd class="font-medium">{{ $document->nom }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Type</dt>
                <dd>
                    <span class="px-2 py-1 rounded text-xs
                        {{ $document->type === 'contrat_pdf' ? 'bg-blue-100 text-blue-700' :
                           ($document->type === 'quittance' ? 'bg-green-100 text-green-700' :
                           ($document->type === 'photo' ? 'bg-purple-100 text-purple-700' :
                           ($document->type === 'piece_identite' ? 'bg-yellow-100 text-yellow-700' :
                           'bg-gray-100 text-gray-600'))) }}">
                        {{ ucfirst(str_replace('_', ' ', $document->type)) }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">Taille</dt>
                <dd>{{ $document->taille_ko ? $document->taille_ko . ' Ko' : '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Uploadé par</dt>
                <dd>{{ $document->uploadedBy?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Date d'upload</dt>
                <dd>{{ $document->created_at->format('d/m/Y à H:i') }}</dd>
            </div>
        </dl>

        <div class="flex gap-3 mt-6">
            <a href="{{ asset('storage/' . $document->chemin) }}"
               target="_blank"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">
                📄 Ouvrir le fichier
            </a>
            <a href="{{ asset('storage/' . $document->chemin) }}"
               download="{{ $document->nom }}"
               class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 text-sm">
                ⬇️ Télécharger
            </a>
        </div>
    </div>

    {{-- Aperçu --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Aperçu</h2>
        @php
            $ext = pathinfo($document->chemin, PATHINFO_EXTENSION);
        @endphp

        @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png']))
            <img src="{{ asset('storage/' . $document->chemin) }}"
                 alt="{{ $document->nom }}"
                 class="w-full rounded-lg border">
        @elseif(strtolower($ext) === 'pdf')
            <iframe src="{{ asset('storage/' . $document->chemin) }}"
                    class="w-full h-96 rounded-lg border">
            </iframe>
        @else
            <div class="bg-gray-50 rounded-lg p-8 text-center text-gray-400">
                <p class="text-4xl mb-2">📄</p>
                <p class="text-sm">Aperçu non disponible pour ce type de fichier.</p>
            </div>
        @endif
    </div>

</div>
@endsection