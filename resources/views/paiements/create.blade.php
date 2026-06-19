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

            @if ($errors->any())
                <div class="mb-8 p-6 bg-rose-50 border border-rose-200 rounded-2xl flex items-start gap-4">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500 text-xl mt-1"></i>
                    <div>
                        <h4 class="font-black text-rose-800 uppercase tracking-widest text-xs mb-2">Erreurs de saisie</h4>
                        <ul class="text-sm text-rose-600 font-medium list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- ALERTE PAIEMENT PARTIEL EXISTANT --}}
            @if(isset($paiementPartielExistant) && $paiementPartielExistant)
                @php
                    $moisLabel = \Carbon\Carbon::parse($paiementPartielExistant->mois_concerne)->locale('fr')->isoFormat('MMMM YYYY');
                @endphp
                <div class="mb-8 p-6 bg-amber-50 border-2 border-amber-300 rounded-2xl flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-400 text-white flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-black text-amber-900 text-sm mb-1">Paiement partiel détecté pour {{ $moisLabel }}</h4>
                        <div class="grid grid-cols-3 gap-4 mt-3">
                            <div class="bg-white rounded-xl p-3 border border-amber-200 text-center">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Loyer dû</p>
                                <p class="font-black text-slate-800 mt-1">{{ number_format($paiementPartielExistant->loyer_attendu, 0, ',', ' ') }} <span class="text-[10px]">GNF</span></p>
                            </div>
                            <div class="bg-white rounded-xl p-3 border border-amber-200 text-center">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Déjà versé</p>
                                <p class="font-black text-blue-700 mt-1">{{ number_format($paiementPartielExistant->total_verse, 0, ',', ' ') }} <span class="text-[10px]">GNF</span></p>
                            </div>
                            <div class="bg-amber-400 rounded-xl p-3 text-center">
                                <p class="text-[10px] font-black text-amber-900 uppercase tracking-widest">Reste à payer</p>
                                <p class="font-black text-white mt-1 text-lg">{{ number_format($paiementPartielExistant->solde_restant, 0, ',', ' ') }} <span class="text-[10px]">GNF</span></p>
                            </div>
                        </div>
                        <p class="text-xs text-amber-700 font-medium mt-3 italic">
                            <i class="fa-solid fa-circle-info mr-1"></i>
                            Vous pouvez verser le montant restant ou un acompte. Le système l'ajoutera automatiquement à votre versement précédent.
                        </p>
                    </div>
                </div>
            @endif

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
                        <select name="{{ auth()->user()->isLocataire() ? 'contrat_id_display' : 'contrat_id' }}" id="contrat_select" required @if(auth()->user()->isLocataire()) disabled @endif
                                class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-semibold @if(auth()->user()->isLocataire()) bg-slate-200 cursor-not-allowed @endif">
                                @if(count($contrats) > 1 && !auth()->user()->isLocataire())
                                    <option value="">-- Choisir un contrat --</option>
                                @endif
                                @foreach($contrats as $c)
                                <option value="{{ $c->id }}" {{ (old('contrat_id') ?? request('contrat_id') ?? ($contrats->count() == 1 ? $contrats->first()->id : '')) == $c->id ? 'selected' : '' }}>
                                    {{ $c->locataire->prenom }} {{ $c->locataire->nom }} — {{ $c->bien->libelle }}
                                </option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-file-signature absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                            
                            @if(auth()->user()->isLocataire())
                                <input type="hidden" name="contrat_id" value="{{ old('contrat_id', request('contrat_id', $contrats->first()->id ?? '')) }}">
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2">Loyer du mois de</label>
                            <div class="relative">
                                @php
                                    // Verrouiller le mois si un paiement partiel existe (pour garantir la correspondance)
                                    $moisVerrouille = (isset($paiementPartielExistant) && $paiementPartielExistant)
                                        || auth()->user()->isComptable()
                                        || auth()->user()->isGestionnaire();
                                @endphp
                                <input type="date" name="mois_concerne" id="mois_concerne" value="{{ old('mois_concerne', $defaultMonth) }}" required
                                       @if($moisVerrouille) readonly @endif
                                       class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-semibold @if($moisVerrouille) bg-slate-200 text-slate-500 cursor-not-allowed @endif">
                                <i class="fa-solid fa-calendar-check absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2">Période payée</label>
                            <div class="relative">
                                <select name="type_paiement_display" @if(auth()->user()->isComptable() || auth()->user()->isGestionnaire()) disabled @endif class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all font-semibold @if(auth()->user()->isComptable() || auth()->user()->isGestionnaire()) bg-slate-200 cursor-not-allowed @endif">
                                    <option value="mensuel" {{ old('type_paiement') == 'mensuel' ? 'selected' : '' }}>Mensuel (1 mois)</option>
                                    <option value="annuel" {{ old('type_paiement') == 'annuel' ? 'selected' : '' }}>Annuel (1 an)</option>
                                </select>
                                @if(auth()->user()->isComptable() || auth()->user()->isGestionnaire())
                                    <input type="hidden" name="type_paiement" value="{{ old('type_paiement', 'mensuel') }}">
                                @else
                                    <input type="hidden" name="type_paiement" value="mensuel" id="type_paiement_hidden">
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-black text-slate-700 mb-2">Loyer Fixe Attendu</label>
                            <input type="text" id="loyer_attendu" value="" readonly
                                   class="w-full bg-slate-100 border border-slate-200 text-slate-400 py-3 px-4 rounded-xl focus:outline-none cursor-not-allowed font-black text-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-black text-emerald-700 mb-2">Montant à verser (GNF)</label>
                            <input type="number" name="montant" id="montant" value="{{ old('montant') }}" required
                                   @if(auth()->user()->isComptable() || auth()->user()->isGestionnaire()) readonly @endif
                                   placeholder="Combien payez-vous ?"
                                   class="w-full bg-slate-50 border border-emerald-300 text-emerald-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all font-black text-lg @if(auth()->user()->isComptable() || auth()->user()->isGestionnaire()) bg-slate-200 text-emerald-900 cursor-not-allowed @endif">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Date encaissé</label>
                        <input type="date" name="date_paiement" value="{{ old('date_paiement', date('Y-m-d')) }}" required
                               @if(auth()->user()->isComptable() || auth()->user()->isGestionnaire()) readonly @endif
                               class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all font-semibold @if(auth()->user()->isComptable() || auth()->user()->isGestionnaire()) bg-slate-200 cursor-not-allowed @endif">
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
                            <select name="{{ (auth()->user()->isComptable() || auth()->user()->isGestionnaire()) ? 'mode_reglement_display' : 'mode_reglement' }}" required @if(auth()->user()->isComptable() || auth()->user()->isGestionnaire()) disabled @endif
                                    class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all font-semibold @if(auth()->user()->isComptable() || auth()->user()->isGestionnaire()) bg-slate-200 cursor-not-allowed @endif">
                                <option value="">-- Choisir --</option>
                                @foreach(['especes' => 'Espèces', 'virement' => 'Virement', 'mobile_money' => 'Mobile Money', 'cheque' => 'Chèque', 'autre' => 'Autre'] as $val => $label)
                                <option value="{{ $val }}" {{ old('mode_reglement') == $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-wallet absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                            @if(auth()->user()->isComptable() || auth()->user()->isGestionnaire())
                                <input type="hidden" name="mode_reglement" value="{{ old('mode_reglement', 'especes') }}">
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Référence / N° de transaction</label>
                        <div class="relative">
                            <input type="text" name="reference" value="{{ old('reference') }}"
                                   @if(auth()->user()->isComptable() || auth()->user()->isGestionnaire()) readonly @endif
                                   placeholder="Ex: Orange Money ID, N° Chèque..."
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all @if(auth()->user()->isComptable() || auth()->user()->isGestionnaire()) bg-slate-200 cursor-not-allowed @endif">
                            <i class="fa-solid fa-hashtag absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Preuve de paiement (Image ou PDF)</label>
                        <div class="relative">
                            <input type="file" name="preuve_paiement" 
                                   @if(auth()->user()->isComptable() || auth()->user()->isGestionnaire()) disabled @endif
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-2.5 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all @if(auth()->user()->isComptable() || auth()->user()->isGestionnaire()) bg-slate-200 cursor-not-allowed @endif">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1 italic">Capture d'écran de votre virement ou reçu de dépôt.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Notes internes</label>
                        <textarea name="notes" rows="2"
                                  @if(auth()->user()->isComptable() || auth()->user()->isGestionnaire()) readonly @endif
                                  placeholder="Observations éventuelles..."
                                  class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all @if(auth()->user()->isComptable() || auth()->user()->isGestionnaire()) bg-slate-200 cursor-not-allowed @endif">{{ old('notes') }}</textarea>
                    </div>
                </div>

            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const selectContrat = document.getElementById('contrat_select');
                    
                    const contractsData = {
                        @foreach($contrats as $c)
                         "{{ $c->id }}": {
                            "loyer": "{{ $c->loyer }}",
                            "prochain_mois": "{{ $c->paiements()->where('statut', 'paye')->orderBy('mois_concerne', 'desc')->first() ? \Carbon\Carbon::parse($c->paiements()->where('statut', 'paye')->orderBy('mois_concerne', 'desc')->first()->mois_concerne)->addMonth()->format('Y-m-01') : \Carbon\Carbon::parse($c->date_debut)->format('Y-m-01') }}"
                        },
                        @endforeach
                    };

                    const inputLoyerAttendu = document.getElementById('loyer_attendu');
                    const inputMoisConcerne = document.getElementById('mois_concerne');
                    const inputMontant = document.getElementById('montant');
                    const selectType = document.querySelector('select[name="type_paiement_display"]');
                    const hiddenType = document.querySelector('input[name="type_paiement"]');

                    function updatePaymentInfo() {
                        let selectedId = selectContrat.value;
                        
                        if (!selectedId && selectContrat.options.length > 0) {
                            for (let i = 0; i < selectContrat.options.length; i++) {
                                if (selectContrat.options[i].value !== "") {
                                    selectContrat.selectedIndex = i;
                                    selectedId = selectContrat.value;
                                    break;
                                }
                            }
                        }

                        if (selectedId && contractsData[selectedId]) {
                            const data = contractsData[selectedId];
                            inputLoyerAttendu.value = new Intl.NumberFormat('fr-FR').format(data.loyer) + ' GNF';
                            
                            const typeValue = selectType.value;
                            if (hiddenType) hiddenType.value = typeValue;

                            const multiplicateur = typeValue === 'annuel' ? 12 : 1;
                            inputMontant.value = Math.round(data.loyer * multiplicateur);

                            @if(!auth()->user()->isAdmin())
                                inputMoisConcerne.value = data.prochain_mois;
                            @endif
                        } else {
                            inputLoyerAttendu.value = '';
                            inputMontant.value = '';
                        }
                    }

                    selectContrat.addEventListener('change', updatePaymentInfo);
                    selectType.addEventListener('change', updatePaymentInfo);
                    updatePaymentInfo();
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
