@extends('layouts.app')

@section('title', 'Historique des Quittances')

@section('content')

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Journal des Quittances</h2>
        <p class="text-slate-500 font-medium">Archive complète des reçus de loyers @if(auth()->user()->role === 'admin') de l'agence @else de vos locataires @endif</p>
    </div>
</div>

{{-- Filtres --}}
<div class="mb-8 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
    <form action="{{ route('quittances.index') }}" method="GET" class="flex flex-wrap items-center gap-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <i class="fa-solid fa-filter"></i>
            </div>
            <span class="font-bold text-slate-700">Filtrer par période :</span>
        </div>

        <select name="year" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-slate-700 py-2.5 px-4 rounded-xl focus:ring-2 focus:ring-blue-500 font-semibold outline-none">
            <option value="">Année (Toutes)</option>
            @foreach($years as $year)
                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
            @endforeach
        </select>

        <select name="month" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-slate-700 py-2.5 px-4 rounded-xl focus:ring-2 focus:ring-blue-500 font-semibold outline-none">
            <option value="">Mois (Tous)</option>
            @foreach($months as $num => $name)
                <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>

        @if(request('year') || request('month'))
        <a href="{{ route('quittances.index') }}" class="text-xs font-black text-rose-500 uppercase tracking-widest hover:text-rose-700 transition-colors">Réinitialiser</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase tracking-widest font-black">
                    <th class="px-8 py-6">Numéro & Date</th>
                    <th class="px-8 py-6">Locataire & Bien</th>
                    <th class="px-8 py-6">Mois de Loyer</th>
                    <th class="px-8 py-6 text-right">Montant Encaissé</th>
                    <th class="px-8 py-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($quittances as $q)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-8 py-6">
                        <p class="font-black text-slate-800">{{ $q->numero_quittance }}</p>
                        <p class="text-[10px] text-slate-400 font-bold">Générée le {{ $q->created_at->format('d/m/Y') }}</p>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-700">{{ $q->paiement->contrat->locataire->prenom }} {{ $q->paiement->contrat->locataire->nom }}</span>
                            <span class="text-[10px] text-slate-400 italic">{{ $q->paiement->contrat->bien->libelle }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-3 py-1 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-widest">
                            {{ \Carbon\Carbon::parse($q->paiement->mois_concerne)->locale('fr')->isoFormat('MMMM YYYY') }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <span class="font-black text-emerald-600 text-lg">{{ number_format($q->paiement->montant, 0, ',', ' ') }} GNF</span>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('quittances.show', $q) }}" class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Voir / Imprimer">
                                <i class="fa-solid fa-file-invoice"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-20 text-center text-slate-400">
                        <i class="fa-solid fa-folder-open text-4xl mb-4 block opacity-20"></i>
                        <p class="font-bold">Aucune quittance trouvée pour cette sélection.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($quittances->hasPages())
    <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/50">
        {{ $quittances->links() }}
    </div>
    @endif
</div>

@endsection