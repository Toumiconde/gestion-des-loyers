@extends('layouts.app')

@section('title', 'Modifier le paramètre — ' . $parametre->cle)

@section('content')
<div class="bg-white rounded-xl shadow p-8 max-w-2xl">
    <form method="POST" action="{{ route('parametres.update', $parametre) }}">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 gap-6">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Clé</label>
                <input type="text"
                       value="{{ $parametre->cle }}"
                       class="w-full border rounded-lg px-3 py-2 font-mono bg-gray-50 text-gray-400 cursor-not-allowed"
                       disabled>
                <p class="text-xs text-gray-400 mt-1">
                    La clé ne peut pas être modifiée.
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Valeur</label>
                <input type="text" name="valeur"
                       value="{{ old('valeur', $parametre->valeur) }}"
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
                <input type="text" name="description"
                       value="{{ old('description', $parametre->description) }}"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

        </div>

        <div class="flex gap-3 mt-8">
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Mettre à jour
            </button>
            <a href="{{ route('parametres.index') }}"
               class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200">
                Annuler
            </a>
        </div>

    </form>
</div>
@endsection