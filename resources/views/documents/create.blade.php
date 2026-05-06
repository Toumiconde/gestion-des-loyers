@extends('layouts.app')

@section('title', 'Uploader un document')

@section('content')
<div class="bg-white rounded-xl shadow p-8 max-w-2xl">
    <form method="POST" action="{{ route('documents.store') }}"
          enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 gap-6">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Lié à quel élément ?
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <select name="documentable_type"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                        <option value="">-- Type --</option>
                        <option value="App\Models\Contrat"
                            {{ old('documentable_type') == 'App\Models\Contrat' ? 'selected' : '' }}>
                            Contrat
                        </option>
                        <option value="App\Models\Bien"
                            {{ old('documentable_type') == 'App\Models\Bien' ? 'selected' : '' }}>
                            Bien
                        </option>
                        <option value="App\Models\Locataire"
                            {{ old('documentable_type') == 'App\Models\Locataire' ? 'selected' : '' }}>
                            Locataire
                        </option>
                    </select>
                    <input type="number" name="documentable_id"
                           value="{{ old('documentable_id') }}"
                           placeholder="ID de l'élément"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
                @error('documentable_type')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom du document</label>
                <input type="text" name="nom" value="{{ old('nom') }}"
                       placeholder="Ex: Contrat signé janvier 2026"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
                @error('nom')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type de document</label>
                <select name="type"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <option value="">-- Choisir --</option>
                    @foreach([
                        'contrat_pdf' => 'Contrat PDF',
                        'quittance' => 'Quittance',
                        'photo' => 'Photo',
                        'piece_identite' => "Pièce d'identité",
                        'autre' => 'Autre'
                    ] as $val => $label)
                    <option value="{{ $val }}" {{ old('type') == $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
                @error('type')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fichier</label>
                <input type="file" name="fichier"
                       accept=".jpg,.jpeg,.png,.pdf"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
                <p class="text-xs text-gray-400 mt-1">
                    Formats acceptés : JPG, PNG, PDF — Taille max : 5 Mo
                </p>
                @error('fichier')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <div class="flex gap-3 mt-8">
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Uploader
            </button>
            <a href="{{ route('documents.index') }}"
               class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200">
                Annuler
            </a>
        </div>

    </form>
</div>
@endsection