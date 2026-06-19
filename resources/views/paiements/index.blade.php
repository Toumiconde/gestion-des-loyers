@extends('layouts.app')

@section('title', 'Gestion des Flux Financiers')

@section('content')

@if(auth()->user()->role !== 'locataire')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Journal des Paiements</h2>
        <p class="text-slate-500 font-medium">Historique complet des encaissements de loyers</p>
    </div>
    
    <div class="flex gap-3">
        @if(auth()->user()->isAdmin() || auth()->user()->isProprietaire() || auth()->user()->isGestionnaire() || auth()->user()->isComptable())
        <a href="{{ route('export.paiements', ['year' => $selectedYear, 'month' => $selectedMonth]) }}" 
           class="inline-flex items-center justify-center px-6 py-3.5 bg-emerald-50 text-emerald-600 font-black rounded-2xl hover:bg-emerald-600 hover:text-white transition-all active:scale-95 group border border-emerald-100 shadow-sm">
            <i class="fa-solid fa-file-excel mr-2 group-hover:bounce transition-transform"></i>
            Exporter Excel
        </a>
        @endif

        @if(in_array(auth()->user()->role, ['admin', 'gestionnaire', 'comptable']))
        <a href="{{ route('paiements.create') }}" 
           class="inline-flex items-center justify-center px-6 py-3.5 bg-emerald-600 text-white font-black rounded-2xl hover:bg-emerald-700 shadow-xl shadow-emerald-200 transition-all active:scale-95 group">
            <i class="fa-solid fa-hand-holding-dollar mr-2 group-hover:scale-110 transition-transform"></i>
            Encaisser un loyer
        </a>
        @endif
    </div>
</div>
@else
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Mon Historique de Paiements</h2>
        <p class="text-slate-500 font-medium">Retrouvez vos quittances et vos déclarations de loyers</p>
    </div>
    
    <a href="{{ route('paiements.create') }}" 
       class="inline-flex items-center justify-center px-6 py-3.5 bg-blue-600 text-white font-black rounded-2xl hover:bg-slate-900 shadow-xl shadow-blue-200 transition-all active:scale-95 group">
        <i class="fa-solid fa-paper-plane mr-2 group-hover:scale-110 transition-transform"></i>
        Déclarer un paiement
    </a>
</div>
@endif

<div class="mb-8 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
    <form action="{{ route('paiements.index') }}" method="GET" class="flex flex-wrap items-end gap-6">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Mois de loyer</label>
            <select name="month" class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-sm">
                <option value="">Tous les mois</option>
                @foreach($months as $num => $name)
                    <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-32">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Année</label>
            <select name="year" class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-sm">
                @for($y = 2024; $y <= 2030; $y++)
                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="bg-slate-900 text-white px-8 py-3 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-blue-600 transition-all">
            Filtrer
        </button>
        @if($selectedMonth || $selectedYear != date('Y'))
            <a href="{{ route('paiements.index') }}" class="text-xs font-black text-slate-400 uppercase hover:text-rose-500 transition-colors py-3">Réinitialiser</a>
        @endif
    </form>
</div>

{{-- BANDEAU PAIEMENTS EN ATTENTE (Admin/Comptable/Gestionnaire) --}}
@if(isset($paiementsEnAttente) && $paiementsEnAttente->count() > 0)
<div class="mb-8 bg-amber-50 border border-amber-200 rounded-3xl overflow-hidden">
    <div class="px-8 py-5 border-b border-amber-200 flex items-center justify-between bg-amber-100/50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center animate-pulse">
                <i class="fa-solid fa-clock-rotate-left text-sm"></i>
            </div>
            <div>
                <h3 class="font-black text-amber-900 text-sm">{{ $paiementsEnAttente->count() }} paiement(s) en attente de validation</h3>
                <p class="text-amber-700 text-[10px] font-bold uppercase tracking-widest">Action requise — vérifiez les preuves et validez</p>
            </div>
        </div>
    </div>
    <div class="divide-y divide-amber-100">
        @foreach($paiementsEnAttente as $pa)
        <div class="px-8 py-4 flex items-center justify-between hover:bg-amber-100/30 transition-colors">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-white border border-amber-200 flex items-center justify-center text-amber-600 font-black text-sm shadow-sm">
                    {{ substr($pa->contrat->locataire->prenom ?? 'L', 0, 1) }}
                </div>
                <div>
                    <p class="font-black text-slate-800">{{ $pa->contrat->locataire->prenom }} {{ $pa->contrat->locataire->nom }}</p>
                    <p class="text-[10px] text-slate-400 font-bold">{{ $pa->contrat->bien->libelle }} — <span class="capitalize">{{ \Carbon\Carbon::parse($pa->mois_concerne)->locale('fr')->isoFormat('MMMM YYYY') }}</span></p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="font-black text-slate-800">{{ number_format($pa->montant, 0, ',', ' ') }} GNF</p>
                    <p class="text-[10px] text-slate-400 font-bold">Déclaré le {{ $pa->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                <a href="{{ route('paiements.show', $pa) }}" class="px-4 py-2 bg-amber-500 text-white rounded-xl font-black text-xs hover:bg-amber-600 transition-all flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-eye"></i> Vérifier & Valider
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase tracking-widest font-black">
                    <th class="px-8 py-5">Mois Concerné</th>
                    @if(auth()->user()->role !== 'locataire')
                    <th class="px-8 py-5">Locataire & Bien</th>
                    @else
                    <th class="px-8 py-5">Logement</th>
                    @endif
                    <th class="px-8 py-5">Montant</th>
                    <th class="px-8 py-5">Statut</th>
                    <th class="px-8 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($paiements as $p)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-8 py-5">
                        <p class="font-black text-slate-800 capitalize">{{ \Carbon\Carbon::parse($p->mois_concerne)->locale('fr')->isoFormat('MMMM YYYY') }}</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase">Payé le {{ $p->date_paiement->format('d/m/Y') }}</p>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            @if(auth()->user()->role !== 'locataire')
                            <span class="font-bold text-slate-700">{{ $p->contrat->locataire->nom_complet }}</span>
                            @endif
                            <span class="text-[10px] text-slate-400 italic font-medium">{{ $p->contrat->bien->libelle }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <span class="font-black text-slate-700">{{ number_format($p->montant, 0, ',', ' ') }} GNF</span>
                    </td>
                    <td class="px-8 py-5">
                        @if($p->statut === 'paye')
                            <span class="px-4 py-1.5 bg-emerald-100 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest">Validé</span>
                        @elseif($p->statut === 'en_attente')
                            <span class="px-4 py-1.5 bg-amber-100 text-amber-600 rounded-full text-[10px] font-black uppercase tracking-widest">En vérification</span>
                        @elseif($p->statut === 'partiel')
                            <span class="px-4 py-1.5 bg-blue-100 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest">Partiel</span>
                        @else
                            <span class="px-4 py-1.5 bg-rose-100 text-rose-600 rounded-full text-[10px] font-black uppercase tracking-widest">{{ $p->statut }}</span>
                        @endif
                    </td>
                    <td class="px-8 py-5 text-right">
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('paiements.show', $p) }}" class="w-10 h-10 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all shadow-sm" title="Détails">
                                <i class="fa-solid fa-eye text-sm"></i>
                            </a>
                            
                            @if($p->statut === 'paye')
                            <a href="{{ route('quittances.generate', $p->id) }}" class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Télécharger la Quittance">
                                <i class="fa-solid fa-file-pdf text-sm"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-20 text-center text-slate-400 italic font-bold">
                        Aucun paiement enregistré pour le moment.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
