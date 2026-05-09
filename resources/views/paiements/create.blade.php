@extends('layouts.app')

@section('title', 'Encaisser un loyer')

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
                    <a href="{{ route('paiements.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">Paiements</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-slate-300 mx-2 text-xs"></i>
                    <span class="text-slate-600">Encaisser</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-emerald-600 p-8 text-white relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full"></div>
            <div class="relative z-10 flex items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center text-3xl">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-black mb-1">Encaisser un loyer</h2>
                    <p class="text-emerald-100 italic">Enregistrez une transaction financière entrante.</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('paiements.store') }}" enctype="multipart/form-data" class="p-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                
                {{-- Détails de la transaction --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-file-contract text-blue-600"></i>
                        <h3 class="font-black text-slate-800 uppercase tracking-widest text-sm">Source & Période</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Contrat de bail</label>
                        <div class="relative">
                            <select name="contrat_id" required
                                    class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-semibold">
                                <option value="">-- Choisir un contrat --</option>
                                @foreach($contrats as $c)
                                <option value="{{ $c->id }}" {{ old('contrat_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->locataire->prenom }} {{ $c->locataire->nom }} — {{ $c->bien->libelle }}
                                </option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-file-signature absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Loyer du mois de</label>
                        <div class="relative">
                            <input type="date" name="mois_concerne" value="{{ old('mois_concerne', date('Y-m-01')) }}" required
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-semibold">
                            <i class="fa-solid fa-calendar-check absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2">Montant (GNF)</label>
                            <input type="number" name="montant" value="{{ old('montant') }}" required
                                   class="w-full bg-slate-50 border border-slate-200 text-emerald-600 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all font-black text-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2">Période</label>
                            <select name="type_paiement" class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all font-semibold">
                                <option value="mensuel" {{ old('type_paiement') == 'mensuel' ? 'selected' : '' }}>Mensuel</option>
                                <option value="annuel" {{ old('type_paiement') == 'annuel' ? 'selected' : '' }}>Annuel</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Date encaissé</label>
                        <input type="date" name="date_paiement" value="{{ old('date_paiement', date('Y-m-d')) }}" required
                               class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all font-semibold">
                    </div>
                </div>

                {{-- Règlement --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-credit-card text-purple-600"></i>
                        <h3 class="font-black text-slate-800 uppercase tracking-widest text-sm">Mode de Règlement</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Mode de paiement</label>
                        <div class="relative">
                            <select name="mode_reglement" required
                                    class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all font-semibold">
                                <option value="">-- Choisir --</option>
                                @foreach(['especes' => 'Espèces', 'virement' => 'Virement', 'mobile_money' => 'Mobile Money', 'cheque' => 'Chèque', 'autre' => 'Autre'] as $val => $label)
                                <option value="{{ $val }}" {{ old('mode_reglement') == $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-wallet absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Référence / N° de transaction</label>
                        <div class="relative">
                            <input type="text" name="reference" value="{{ old('reference') }}"
                                   placeholder="Ex: Orange Money ID, N° Chèque..."
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all">
                            <i class="fa-solid fa-hashtag absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Preuve de paiement (Optionnel)</label>
                        <div class="relative group">
                            <input type="file" name="preuve_paiement" 
                                   class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 border border-slate-200 p-2 rounded-xl bg-slate-50 group-hover:border-purple-300 transition-all">
                            <p class="mt-1 text-[10px] text-slate-400 font-bold italic">Photo du reçu, capture d'écran virement, etc. (PDF, JPG, PNG)</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Notes internes</label>
                        <textarea name="notes" rows="2"
                                  placeholder="Observations éventuelles..."
                                  class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all">{{ old('notes') }}</textarea>
                    </div>
                </div>

            </div>

            {{-- INFOS BANCAIRES DU PROPRIÉTAIRE --}}
            <div id="bank-info-container" class="mt-8 p-6 bg-blue-50 rounded-3xl border border-blue-100 hidden">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-xl">
                        <i class="fa-solid fa-university"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-blue-900">Coordonnées bancaires du propriétaire</h3>
                        <p class="text-blue-700 text-xs italic">Veuillez effectuer le virement sur ce compte pour valider votre loyer.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Banque</p>
                        <p id="bank-name" class="font-bold text-blue-900">-</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">RIB / N° de compte</p>
                        <p id="bank-rib" class="font-bold text-blue-900">-</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Titulaire</p>
                        <p id="bank-holder" class="font-bold text-blue-900">-</p>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const selectContrat = document.querySelector('select[name="contrat_id"]');
                    const bankContainer = document.getElementById('bank-info-container');
                    const bankName = document.getElementById('bank-name');
                    const bankRib = document.getElementById('bank-rib');
                    const bankHolder = document.getElementById('bank-holder');

                    const contractsData = {
                        @foreach($contrats as $c)
                        "{{ $c->id }}": {
                            "banque": "{{ $c->bien->proprietaire->nom_banque ?? 'Non spécifiée' }}",
                            "rib": "{{ $c->bien->proprietaire->rib_bancaire ?? 'Non spécifié' }}",
                            "titulaire": "{{ $c->bien->proprietaire->titulaire_compte ?? $c->bien->proprietaire->user->name }}"
                        },
                        @endforeach
                    };

                    function updateBankInfo() {
                        const selectedId = selectContrat.value;
                        if (selectedId && contractsData[selectedId]) {
                            const data = contractsData[selectedId];
                            bankName.textContent = data.banque;
                            bankRib.textContent = data.rib;
                            bankHolder.textContent = data.titulaire;
                            bankContainer.classList.remove('hidden');
                        } else {
                            bankContainer.classList.add('hidden');
                        }
                    }

                    selectContrat.addEventListener('change', updateBankInfo);
                    updateBankInfo(); // Initial check
                });
            </script>

            <div class="flex items-center gap-4 mt-12 pt-8 border-t border-slate-100">
                <button type="submit"
                        class="px-10 py-4 bg-emerald-600 text-white font-black rounded-2xl hover:bg-emerald-700 shadow-xl shadow-emerald-200 transition-all active:scale-95">
                    Confirmer l'encaissement
                </button>
                <a href="{{ route('paiements.index') }}"
                   class="px-10 py-4 bg-slate-100 text-slate-600 font-bold rounded-2xl hover:bg-slate-200 transition-all">
                    Annuler
                </a>
            </div>

        </form>
    </div>
</div>

@endsection
