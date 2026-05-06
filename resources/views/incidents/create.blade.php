@extends('layouts.app')

@section('title', 'Déclarer un incident')

@section('content')
<div class="bg-white rounded-xl shadow p-8 max-w-2xl">
    <form method="POST" action="{{ route('incidents.store') }}">
        @csrf

        <div class="grid grid-cols-1 gap-6">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contrat concerné</label>
                <select name="contrat_id"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <option value="">-- Choisir un contrat --</option>
                    @foreach($contrats as $c)
                    <option value="{{ $c->id }}" {{ old('contrat_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->numero_contrat }} — {{ $c->locataire->prenom }} {{ $c->locataire->nom }}
                        — {{ $c->bien->libelle }}
                    </option>
                    @endforeach
                </select>
                @error('contrat_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
                <input type="text" name="titre" value="{{ old('titre') }}"
                       placeholder="Ex: Fuite d'eau dans la salle de bain"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
                @error('titre')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="4"
                          placeholder="Décrivez l'incident en détail..."
                          class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                          required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Priorité</label>
                <select name="priorite"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <option value="faible" {{ old('priorite') == 'faible' ? 'selected' : '' }}>
                        Faible
                    </option>
                    <option value="moyen" {{ old('priorite', 'moyen') == 'moyen' ? 'selected' : '' }}>
                        Moyen
                    </option>
                    <option value="urgent" {{ old('priorite') == 'urgent' ? 'selected' : '' }}>
                        Urgent
                    </option>
                </select>
                @error('priorite')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <div class="flex gap-3 mt-8">
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Déclarer
            </button>
            <a href="{{ route('incidents.index') }}"
               class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200">
                Annuler
            </a>
        </div>

    </form>
</div>
@endsection