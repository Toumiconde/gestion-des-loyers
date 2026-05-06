@extends('layouts.app')

@section('title', 'Modifier le bien')

@section('content')
<div class="bg-white rounded-xl shadow p-8 max-w-2xl">
    <form method="POST" action="{{ route('biens.update', $bien) }}">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 gap-6">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Libellé</label>
                <input type="text" name="libelle"
                       value="{{ old('libelle', $bien->libelle) }}"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach(['appartement','maison','studio','bureau','commerce','autre'] as $type)
                    <option value="{{ $type }}" {{ $bien->type == $type ? 'selected' : '' }}>
                        {{ ucfirst($type) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                <textarea name="adresse" rows="2"
                          class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('adresse', $bien->adresse) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Surface (m²)</label>
                    <input type="number" name="surface"
                           value="{{ old('surface', $bien->surface) }}" step="0.01"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loyer de base (GNF)</label>
                    <input type="number" name="loyer_base"
                           value="{{ old('loyer_base', $bien->loyer_base) }}"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Charges (GNF)</label>
                    <input type="number" name="charges"
                           value="{{ old('charges', $bien->charges) }}"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dépôt de garantie (GNF)</label>
                    <input type="number" name="depot_garantie"
                           value="{{ old('depot_garantie', $bien->depot_garantie) }}"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="statut"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach(['disponible','occupe','en_travaux','archive'] as $statut)
                    <option value="{{ $statut }}" {{ $bien->statut == $statut ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $statut)) }}
                    </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="flex gap-3 mt-8">
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Mettre à jour
            </button>
            <a href="{{ route('biens.index') }}"
               class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200">
                Annuler
            </a>
        </div>

    </form>
</div>
@endsection