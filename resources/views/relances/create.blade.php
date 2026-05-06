@extends('layouts.app')

@section('title', 'Nouvelle relance')

@section('content')
<div class="bg-white rounded-xl shadow p-8 max-w-2xl">
    <form method="POST" action="{{ route('relances.store') }}">
        @csrf

        <div class="grid grid-cols-1 gap-6">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contrat concerné</label>
                <select name="contrat_id"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <option value="">-- Choisir un contrat actif --</option>
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Niveau de relance</label>
                <select name="niveau"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <option value="">-- Choisir --</option>
                    <option value="niveau_1" {{ old('niveau') == 'niveau_1' ? 'selected' : '' }}>
                        Niveau 1 — Rappel amical
                    </option>
                    <option value="niveau_2" {{ old('niveau') == 'niveau_2' ? 'selected' : '' }}>
                        Niveau 2 — Mise en demeure
                    </option>
                    <option value="niveau_3" {{ old('niveau') == 'niveau_3' ? 'selected' : '' }}>
                        Niveau 3 — Dernier avertissement
                    </option>
                </select>
                @error('niveau')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Canal d'envoi</label>
                <select name="canal"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <option value="">-- Choisir --</option>
                    <option value="email" {{ old('canal') == 'email' ? 'selected' : '' }}>
                        Email uniquement
                    </option>
                    <option value="sms" {{ old('canal') == 'sms' ? 'selected' : '' }}>
                        SMS uniquement
                    </option>
                    <option value="email_sms" {{ old('canal') == 'email_sms' ? 'selected' : '' }}>
                        Email + SMS
                    </option>
                </select>
                @error('canal')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <div class="flex gap-3 mt-8">
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Envoyer la relance
            </button>
            <a href="{{ route('relances.index') }}"
               class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200">
                Annuler
            </a>
        </div>

    </form>
</div>
@endsection
