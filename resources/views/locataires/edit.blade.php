@extends('layouts.app')

@section('title', 'Modifier le locataire')

@section('content')
<div class="bg-white rounded-xl shadow p-8 max-w-2xl">
    <form method="POST" action="{{ route('locataires.update', $locataire) }}">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 gap-6">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                    <input type="text" name="prenom"
                           value="{{ old('prenom', $locataire->prenom) }}"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input type="text" name="nom"
                           value="{{ old('nom', $locataire->nom) }}"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email"
                       value="{{ old('email', $locataire->email) }}"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                <input type="text" name="telephone"
                       value="{{ old('telephone', $locataire->telephone) }}"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                <textarea name="adresse" rows="2"
                          class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('adresse', $locataire->adresse) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pièce d'identité</label>
                <input type="text" name="piece_identite"
                       value="{{ old('piece_identite', $locataire->piece_identite) }}"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

        </div>

        <div class="flex gap-3 mt-8">
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Mettre à jour
            </button>
            <a href="{{ route('locataires.index') }}"
               class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200">
                Annuler
            </a>
        </div>

    </form>
</div>
@endsection