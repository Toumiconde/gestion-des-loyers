@extends('layouts.app')

@section('title', 'Modifier l\'incident')

@section('content')
<div class="bg-white rounded-xl shadow p-8 max-w-2xl">
    <form method="POST" action="{{ route('incidents.update', $incident) }}">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 gap-6">

            <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-600">
                <p><span class="font-medium">Contrat :</span> {{ $incident->contrat->numero_contrat }}</p>
                <p><span class="font-medium">Bien :</span> {{ $incident->contrat->bien->libelle }}</p>
                <p><span class="font-medium">Locataire :</span>
                    {{ $incident->contrat->locataire->prenom }}
                    {{ $incident->contrat->locataire->nom }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="statut"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    @foreach(['ouvert' => 'Ouvert', 'en_cours' => 'En cours', 'resolu' => 'Résolu', 'ferme' => 'Fermé'] as $val => $label)
                    <option value="{{ $val }}" {{ $incident->statut == $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Date de résolution
                    <span class="text-gray-400 font-normal">(si résolu)</span>
                </label>
                <input type="date" name="date_resolution"
                       value="{{ old('date_resolution', $incident->date_resolution?->format('Y-m-d')) }}"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

        </div>

        <div class="flex gap-3 mt-8">
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Mettre à jour
            </button>
            <a href="{{ route('incidents.show', $incident) }}"
               class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200">
                Annuler
            </a>
        </div>

    </form>
</div>
@endsection