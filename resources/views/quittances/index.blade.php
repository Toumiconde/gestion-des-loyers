@extends('layouts.app')

@section('title', 'Historique des Quittances')

@section('content')

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Journal des Quittances</h2>
        <p class="text-slate-500 font-medium">Archive complète des reçus de loyers @if(auth()->user()->role === 'admin') de l'agence @else de vos locataires @endif</p>
    </div>
</div>

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