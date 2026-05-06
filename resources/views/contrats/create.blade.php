@extends('layouts.app')

@section('title', 'Nouveau contrat')

@section('content')
<div class="bg-white rounded-xl shadow p-8 max-w-2xl">
    <form method="POST" action="{{ route('contrats.store') }}">
        @csrf

        <div class="grid grid-cols-1 gap-6">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bien</label>
                <select name="bien_id"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <option value="">-- Choisir un bien disponible --</option>
                    @foreach($biens as $bien)
                    <option value="{{ $bien->id }}" {{ old('bien_id') == $bien->id ? 'selected' : '' }}>
                        {{ $bien->libelle }} — {{ number_format($bien->loyer_base, 0, ',', ' ') }} GNF
                    </option>
                    @endforeach
                </select>
                @error('bien_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Locataire</label>
                <select name="locataire_id"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <option value="">-- Choisir un locataire --</option>
                    @foreach($locataires as $l)
                    <option value="{{ $l->id }}" {{ old('locataire_id') == $l->id ? 'selected' : '' }}>
                        {{ $l->prenom }} {{ $l->nom }}
                    </option>
                    @endforeach
                </select>
                @error('locataire_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                    <input type="date" name="date_debut" value="{{ old('date_debut') }}"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                    @error('date_debut')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durée (mois)</label>
                    <input type="number" name="duree_mois" value="{{ old('duree_mois') }}"
                           placeholder="Ex: 12"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loyer (GNF)</label>
                    <input type="number" name="loyer" value="{{ old('loyer') }}"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                    @error('loyer')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dépôt de garantie (GNF)</label>
                    <input type="number" name="depot_garantie" value="{{ old('depot_garantie') }}"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                    @error('depot_garantie')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jour d'échéance</label>
                    <input type="number" name="jour_echeance" value="{{ old('jour_echeance', 5) }}"
                           min="1" max="28"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Taux de révision (%)</label>
                    <input type="number" name="taux_revision" value="{{ old('taux_revision', 0) }}"
                           step="0.01"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

        </div>

        <div class="flex gap-3 mt-8">
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Créer le contrat
            </button>
            <a href="{{ route('contrats.index') }}"
               class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200">
                Annuler
            </a>
        </div>

    </form>
</div>
@endsection