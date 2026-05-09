@extends('layouts.app')

@section('title', 'Nouveau paramètre')

@section('content')
<div class="bg-white rounded-xl shadow p-8 max-w-2xl">
    <form method="POST" action="{{ route('parametres.store') }}">
        @csrf

        <div class="grid grid-cols-1 gap-6">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Clé
                </label>
                <input type="text" name="cle" value="{{ old('cle') }}"
                       placeholder="Ex: devise, taux_penalite, taux_revision"
                       class="w-full border rounded-lg px-3 py-2 font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
                <p class="text-xs text-gray-400 mt-1">
                    Utiliser des minuscules sans espaces. Ex: taux_penalite
                </p>
                @error('cle')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Valeur</label>
                <input type="text" name="valeur" value="{{ old('valeur') }}"
                       placeholder="Ex: GNF, 5, 3.5"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
                @error('valeur')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                    <span class="text-gray-400 font-normal">(optionnel)</span>
                </label>
                <input type="text" name="description" value="{{ old('description') }}"
                       placeholder="Ex: Devise utilisée pour les montants"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

        </div>

        {{-- Paramètres suggérés --}}
        <div class="mt-6 bg-blue-50 rounded-lg p-4">
            <p class="text-sm font-medium text-blue-700 mb-2">
                 Paramètres suggérés
            </p>
            <div class="grid grid-cols-2 gap-2 text-xs text-blue-600">
                <span>• devise → GNF</span>
                <span>• taux_penalite → 5</span>
                <span>• taux_revision → 3</span>
                <span>• jour_rappel → 5</span>
                <span>• nom_agence → Billy Condé Immo</span>
                <span>• email_agence → contact@billy.gn</span>
            </div>
        </div>

        <div class="flex gap-3 mt-8">
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Enregistrer
            </button>
            <a href="{{ route('parametres.index') }}"
               class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200">
                Annuler
            </a>
        </div>

    </form>
</div>
@endsection