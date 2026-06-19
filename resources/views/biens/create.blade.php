@extends('layouts.app')

@section('title', 'Nouveau Bien Immobilier')

@section('content')

<div class="max-w-4xl mx-auto py-8">
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm font-medium">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-blue-600 transition-colors">
                    <i class="fa-solid fa-house mr-2"></i> Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-slate-300 mx-2 text-xs"></i>
                    <a href="{{ route('biens.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">Biens</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-slate-300 mx-2 text-xs"></i>
                    <span class="text-slate-600">Ajouter un bien</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-[#02132D] p-8 text-white relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/10 rounded-full"></div>
            <div class="relative z-10">
                <h2 class="text-3xl font-black mb-2">Ajouter un bien</h2>
                <p class="text-slate-400">Enregistrez une nouvelle propriété dans votre parc immobilier.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('biens.store') }}" enctype="multipart/form-data" class="p-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                {{-- Informations Générales --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-info-circle text-blue-600"></i>
                        <h3 class="font-black text-slate-800 uppercase tracking-widest text-sm">Généralités</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Propriétaire</label>
                        <div class="relative">
                            @if(auth()->user()->isProprietaire())
                                <input type="hidden" name="proprietaire_id" value="{{ auth()->user()->proprietaire->id }}">
                                <div class="w-full bg-slate-100 border border-slate-200 text-slate-500 py-3 pl-10 pr-4 rounded-xl font-semibold">
                                    {{ auth()->user()->name }} (Moi)
                                </div>
                            @else
                                <select name="proprietaire_id" required
                                        class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-semibold cursor-pointer">
                                    <option value="">-- Choisir un propriétaire --</option>
                                    @foreach($proprietaires as $p)
                                    <option value="{{ $p->id }}" {{ old('proprietaire_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->user->name }}
                                    </option>
                                    @endforeach
                                </select>
                            @endif
                            <i class="fa-solid fa-user-tie absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                            @if(!auth()->user()->isProprietaire())
                                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                            @endif
                        </div>
                        @error('proprietaire_id') <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Libellé du bien</label>
                        <div class="relative">
                            <input type="text" name="libelle" value="{{ old('libelle') }}"
                                   placeholder="Ex: Villa de Luxe - Camayenne"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                   required>
                            <i class="fa-solid fa-tag absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                        @error('libelle') <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Type de bien</label>
                        <div class="relative">
                            <select name="type" required
                                    class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-semibold cursor-pointer">
                                <option value="">-- Choisir un type --</option>
                                @foreach(['appartement','maison','studio','bureau','commerce','immeuble','autre'] as $type)
                                <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                    {{ $type === 'immeuble' ? 'Immeuble / Étage' : ucfirst($type) }}
                                </option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-house-chimney absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                        </div>
                    </div>

                    <div id="etage_field" class="{{ old('type') === 'immeuble' ? '' : 'hidden' }}">
                        <label class="block text-sm font-black text-slate-700 mb-2">Précisions Étage (si applicable)</label>
                        <div class="relative">
                            <input type="text" name="details_etage" value="{{ old('details_etage') }}"
                                   placeholder="Ex: 3 dalles tout dispo, 1er étage gauche..."
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            <i class="fa-solid fa-layer-group absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Adresse exacte</label>
                        <div class="relative">
                            <textarea name="adresse" rows="2" required
                                      placeholder="Ex: Rue de l'Indépendance, Kaloum"
                                      class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">{{ old('adresse') }}</textarea>
                            <i class="fa-solid fa-map-location-dot absolute left-4 top-4 text-slate-400"></i>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2">Nombre de chambres</label>
                            <div class="relative">
                                <input type="number" name="nombre_chambres" value="{{ old('nombre_chambres', 1) }}" min="0" required
                                       class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                <i class="fa-solid fa-bed absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2">Type de douche</label>
                            <div class="relative">
                                <select name="type_douche" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-semibold cursor-pointer">
                                    <option value="interne" {{ old('type_douche') == 'interne' ? 'selected' : '' }}>Douche Interne</option>
                                    <option value="externe" {{ old('type_douche') == 'externe' ? 'selected' : '' }}>Douche Externe</option>
                                    <option value="les_deux" {{ old('type_douche') == 'les_deux' ? 'selected' : '' }}>Les deux (Interne & Externe)</option>
                                </select>
                                <i class="fa-solid fa-shower absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Spécifications financières --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-coins text-emerald-600"></i>
                        <h3 class="font-black text-slate-800 uppercase tracking-widest text-sm">Aspects Financiers</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2">Surface (m²)</label>
                            <div class="relative">
                                <input type="number" name="surface" value="{{ old('surface') }}" step="0.01"
                                       class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                                <i class="fa-solid fa-maximize absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2">Loyer Base (GNF)</label>
                            <div class="relative">
                                <input type="number" name="loyer_base" value="{{ old('loyer_base') }}" required
                                       class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                                <i class="fa-solid fa-money-bill-1 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                            @error('loyer_base') <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2">Charges (GNF)</label>
                            <div class="relative">
                                <input type="number" name="charges" value="{{ old('charges', 0) }}"
                                       class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                                <i class="fa-solid fa-bolt absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2">Garantie (GNF)</label>
                            <div class="relative">
                                <input type="number" name="depot_garantie" value="{{ old('depot_garantie') }}"
                                       class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                                <i class="fa-solid fa-shield-halved absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 p-6 rounded-2xl border border-blue-100 mt-4">
                        <div class="flex items-start gap-4">
                            <i class="fa-solid fa-circle-info text-blue-500 mt-1"></i>
                            <p class="text-xs text-blue-700 leading-relaxed">
                                Le loyer total sera calculé comme la somme du loyer de base et des charges lors de la génération des quittances.
                            </p>
                        </div>
                    </div>

                    {{-- Photo principale --}}
                    <div class="mt-6">
                        <label class="block text-sm font-black text-slate-700 mb-2">Photo principale du bien</label>
                        <div class="relative">
                            <input type="file" name="photo" accept="image/*"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                            <i class="fa-solid fa-image absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1 italic">Format recommandé: JPG, PNG (Max 2Mo)</p>
                    </div>
                </div>

            </div>

            <div class="flex items-center gap-4 mt-12 pt-8 border-t border-slate-100">
                <button type="submit"
                        class="px-10 py-4 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 shadow-xl shadow-blue-200 transition-all active:scale-95">
                    Enregistrer le bien
                </button>
                <a href="{{ route('biens.index') }}"
                   class="px-10 py-4 bg-slate-100 text-slate-600 font-bold rounded-2xl hover:bg-slate-200 transition-all">
                    Annuler
                </a>
            </div>

        </form>
    </div>
</div>

<script>
    document.querySelector('select[name="type"]').addEventListener('change', function() {
        const etageField = document.getElementById('etage_field');
        if (this.value === 'immeuble') {
            etageField.classList.remove('hidden');
        } else {
            etageField.classList.add('hidden');
        }
    });
</script>
@endsection