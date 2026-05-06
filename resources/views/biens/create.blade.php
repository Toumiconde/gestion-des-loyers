@extends('layouts.app')

@section('title', 'Nouveau bien')

@section('content')
<div class="bg-white rounded-xl shadow p-8 max-w-2xl">
    <form method="POST" action="{{ route('biens.store') }}">
        @csrf

        <div class="grid grid-cols-1 gap-6">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Propriétaire</label>
                <select name="proprietaire_id"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <option value="">-- Choisir un propriétaire --</option>
                    @foreach($proprietaires as $p)
                    <option value="{{ $p->id }}" {{ old('proprietaire_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->user->name }}
                    </option>
                    @endforeach
                </select>
                @error('proprietaire_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Libellé</label>
                <input type="text" name="libelle" value="{{ old('libelle') }}"
                       placeholder="Ex: Appartement F3 Kaloum"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
                @error('libelle')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <option value="">-- Choisir --</option>
                    @foreach(['appartement','maison','studio','bureau','commerce','autre'] as $type)
                    <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                        {{ ucfirst($type) }}
                    </option>
                    @endforeach
                </select>
                @error('type')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                <textarea name="adresse" rows="2"
                          placeholder="Ex: Kaloum, Conakry"
                          class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                          required>{{ old('adresse') }}</textarea>
                @error('adresse')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Surface (m²)</label>
                    <input type="number" name="surface" value="{{ old('surface') }}" step="0.01"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loyer de base (GNF)</label>
                    <input type="number" name="loyer_base" value="{{ old('loyer_base') }}"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                    @error('loyer_base')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Charges (GNF)</label>
                    <input type="number" name="charges" value="{{ old('charges', 0) }}"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dépôt de garantie (GNF)</label>
                    <input type="number" name="depot_garantie" value="{{ old('depot_garantie') }}"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

        </div>

        <div class="flex gap-3 mt-8">
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Enregistrer
            </button>
            <a href="{{ route('biens.index') }}"
               class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200">
                Annuler
            </a>
        </div>

    </form>
</div>
@endsection