@extends('layouts.app')

@section('title', 'Relevé de Reversement - ' . $bilan->proprietaire->user->name)

@section('content')
<div class="max-w-4xl mx-auto py-10 px-6">
    
    {{-- Boutons d'action --}}
    <div class="flex justify-between items-center mb-8 no-print">
        <a href="{{ route('dashboard') }}" class="text-slate-500 hover:text-slate-800 font-bold flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Retour
        </a>
        <div class="flex gap-3">
            <button onclick="window.print()" class="px-5 py-2 bg-slate-800 text-white rounded-xl font-black text-sm hover:bg-slate-900 transition-all flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Imprimer / PDF
            </button>
            
            @if(in_array(auth()->user()->role, ['admin', 'comptable']) && $bilan->statut !== 'virement_effectue')
                <button onclick="document.getElementById('modal-virement').classList.remove('hidden')" class="px-5 py-2 bg-emerald-600 text-white rounded-xl font-black text-sm hover:bg-emerald-700 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-money-bill-transfer"></i> Enregistrer le Virement
                </button>
            @endif
        </div>
    </div>

    {{-- DOCUMENT --}}
    <div class="bg-white shadow-2xl rounded-[40px] overflow-hidden border border-slate-100 print:shadow-none print:border-none">
        
        {{-- En-tête --}}
        <div class="bg-slate-900 p-12 text-white flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-black mb-2 uppercase tracking-tighter">Relevé Mensuel</h1>
                <p class="text-slate-400 font-bold uppercase text-xs tracking-widest">Reversement Propriétaire</p>
                <div class="mt-8 bg-blue-600 px-4 py-2 rounded-lg inline-block">
                    <span class="font-black text-lg">{{ str_pad($bilan->mois, 2, '0', STR_PAD_LEFT) }} / {{ $bilan->annee }}</span>
                </div>
            </div>
            <div class="text-right">
                <p class="text-2xl font-black text-blue-500 mb-2">GESTLOYER</p>
                <p class="text-xs text-slate-400">Agence Immobilière Professionnelle</p>
                <p class="text-xs text-slate-400 mt-1">Conakry, République de Guinée</p>
            </div>
        </div>

        <div class="p-12">
            {{-- Infos Parties --}}
            <div class="grid grid-cols-2 gap-12 mb-16">
                <div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Destinataire</h3>
                    <p class="text-lg font-black text-slate-800">{{ $bilan->proprietaire->user->name }}</p>
                    <p class="text-sm text-slate-500 mt-1">{{ $bilan->proprietaire->adresse }}</p>
                    <p class="text-sm text-slate-500">{{ $bilan->proprietaire->telephone }}</p>
                </div>
                <div class="text-right">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Informations Bancaires</h3>
                    <p class="text-sm font-black text-slate-800 uppercase">{{ $bilan->proprietaire->nom_banque ?: 'Non renseigné' }}</p>
                    <p class="text-sm text-slate-500 mt-1">RIB : {{ $bilan->proprietaire->rib_bancaire ?: '---' }}</p>
                    <p class="text-sm text-slate-500">Titulaire : {{ $bilan->proprietaire->titulaire_compte ?: $bilan->proprietaire->user->name }}</p>
                </div>
            </div>

            {{-- DÉTAIL DES ENCAISSEMENTS --}}
            <div class="mb-12">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-6 border-b border-slate-100 pb-4">
                    1. Loyers Encaissés
                </h3>
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="py-4">Bien / Unité</th>
                            <th class="py-4">Locataire</th>
                            <th class="py-4 text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($detailsLoyers as $l)
                        <tr>
                            <td class="py-4">
                                <p class="font-bold text-slate-800">{{ $l->contrat->bien->libelle }}</p>
                                <p class="text-[10px] text-slate-400 uppercase">{{ $l->contrat->bien->ville }}</p>
                            </td>
                            <td class="py-4 text-sm text-slate-600">{{ $l->contrat->locataire->nom }}</td>
                            <td class="py-4 font-bold text-slate-800 text-right">{{ number_format($l->montant, 0, ',', ' ') }} GNF</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50">
                            <td colspan="2" class="py-4 px-4 font-black text-slate-800">Sous-total Revenus</td>
                            <td class="py-4 px-4 text-right font-black text-slate-900 text-lg">{{ number_format($bilan->total_revenus, 0, ',', ' ') }} GNF</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- DÉTAIL DES DÉDUCTIONS --}}
            <div class="mb-16">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-6 border-b border-slate-100 pb-4">
                    2. Déductions & Frais
                </h3>
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="py-4">Libellé / Nature</th>
                            <th class="py-4 text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        {{-- Commission Agence --}}
                        <tr>
                            <td class="py-4">
                                <p class="font-bold text-rose-600 italic">Frais de gestion agence ({{ $bilan->proprietaire->commission_rate }}%)</p>
                            </td>
                            <td class="py-4 font-bold text-rose-600 text-right">- {{ number_format($bilan->frais_gestion, 0, ',', ' ') }} GNF</td>
                        </tr>
                        {{-- Travaux --}}
                        @foreach($detailsDepenses as $d)
                        <tr>
                            <td class="py-4">
                                <p class="font-bold text-slate-800">Maintenance : {{ $d->titre }}</p>
                                <p class="text-[10px] text-slate-400 uppercase italic">{{ $d->contrat->bien->libelle }}</p>
                            </td>
                            <td class="py-4 font-bold text-slate-800 text-right">- {{ number_format($d->cout_reel, 0, ',', ' ') }} GNF</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-rose-50/50">
                            <td class="py-4 px-4 font-black text-rose-800 uppercase text-[10px]">Total Déductions</td>
                            <td class="py-4 px-4 text-right font-black text-rose-900">- {{ number_format($bilan->total_depenses + $bilan->frais_gestion, 0, ',', ' ') }} GNF</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- NET À REVERSER --}}
            <div class="bg-blue-600 rounded-3xl p-10 text-white flex justify-between items-center mb-16">
                <div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-70 text-white/100">Montant Net à Reverser</h3>
                    <p class="text-sm font-medium">
                        @if($bilan->statut === 'virement_effectue')
                            Par {{ str_replace('_', ' ', $bilan->mode_paiement) }}
                        @else
                            Par virement bancaire
                        @endif
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-5xl font-black">{{ number_format($bilan->montant_net, 0, ',', ' ') }} <span class="text-xl">GNF</span></p>
                </div>
            </div>

            {{-- Signature / Pied de page --}}
            <div class="grid grid-cols-2 gap-12 border-t border-slate-100 pt-12">
                <div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Le Comptable</h3>
                    <div class="h-20 flex items-end">
                        <p class="font-black text-slate-300 text-3xl opacity-20 uppercase tracking-tighter">GESTLOYER ADMIN</p>
                    </div>
                </div>
                <div class="text-right">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Status du virement</h3>
                    @if($bilan->statut === 'virement_effectue')
                        <div class="bg-emerald-50 text-emerald-700 px-4 py-2 rounded-xl inline-block border border-emerald-100">
                            <p class="text-xs font-black uppercase">Effectué le {{ \Carbon\Carbon::parse($bilan->date_virement)->format('d/m/Y') }}</p>
                            @if($bilan->ref_virement)
                                <p class="text-[10px] font-medium opacity-70 mt-1">Réf: {{ $bilan->ref_virement }}</p>
                            @endif
                        </div>
                    @else
                        <div class="bg-amber-50 text-amber-700 px-4 py-2 rounded-xl inline-block border border-amber-100 font-black text-[10px] uppercase">
                            Traitement en cours
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL ENREGISTREMENT VIREMENT --}}
    <div id="modal-virement" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-[30px] p-8 max-w-md w-full shadow-2xl">
            <h3 class="text-xl font-black text-slate-800 mb-6">Confirmer le Virement</h3>
            <form action="{{ route('reversements.markAsPaid', $bilan) }}" method="POST">
                @csrf
                <div class="space-y-4 mb-8">
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase mb-2">Date du virement</label>
                        <input type="date" name="date_virement" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-emerald-500 font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase mb-2">Mode de versement</label>
                        <select name="mode_paiement" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-emerald-500 font-bold">
                            <option value="virement">Virement Bancaire</option>
                            <option value="especes">Espèces (Remis en main propre)</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="cheque">Chèque</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase mb-2">Référence Bancaire / N° Reçu</label>
                        <input type="text" name="ref_virement" placeholder="Ex: VIR-{{ date('Ymd') }}-77" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('modal-virement').classList.add('hidden')" class="flex-1 py-3 bg-slate-100 text-slate-500 font-black rounded-xl hover:bg-slate-200 transition-all">Annuler</button>
                    <button type="submit" class="flex-1 py-3 bg-emerald-600 text-white font-black rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
    .max-w-4xl { max-width: 100% !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
}
</style>
@endsection
