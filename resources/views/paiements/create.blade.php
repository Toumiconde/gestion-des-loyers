@extends('layouts.app')

@section('title', 'Enregistrer un paiement')

@section('content')
<div class="bg-white rounded-xl shadow p-8 max-w-2xl">
    <form method="POST" action="{{ route('paiements.store') }}">
        @csrf

        <div class="grid grid-cols-1 gap-6">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contrat</label>
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Mois concerné</label>
                <input type="date" name="mois_concerne" value="{{ old('mois_concerne') }}"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
                <p class="text-xs text-gray-400 mt-1">
                    Mettre le 1er jour du mois. Ex: 2026-05-01 pour mai 2026
                </p>
                @error('mois_concerne')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Montant (GNF)</label>
                    <input type="number" name="montant" value="{{ old('montant') }}"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                    @error('montant')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de paiement</label>
                    <input type="date" name="date_paiement"
                           value="{{ old('date_paiement', date('Y-m-d')) }}"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                    @error('date_paiement')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mode de règlement</label>
                <select name="mode_reglement"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <option value="">-- Choisir --</option>
                    @foreach(['especes' => 'Espèces', 'virement' => 'Virement', 'mobile_money' => 'Mobile Money', 'cheque' => 'Chèque', 'autre' => 'Autre'] as $val => $label)
                    <option value="{{ $val }}" {{ old('mode_reglement') == $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
                @error('mode_reglement')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Référence
                    <span class="text-gray-400 font-normal">(optionnel)</span>
                </label>
                <input type="text" name="reference" value="{{ old('reference') }}"
                       placeholder="Ex: TXN-123456"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Notes
                    <span class="text-gray-400 font-normal">(optionnel)</span>
                </label>
                <textarea name="notes" rows="2"
                          class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
            </div>

        </div>

        <div class="flex gap-3 mt-8">
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Enregistrer
            </button>
            <a href="{{ route('paiements.index') }}"
               class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200">
                Annuler
            </a>
        </div>

    </form>
</div>
@endsection
