@extends('layouts.app')

@section('title', 'Gestion des Charges')

@section('content')

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Registre des Dépenses</h2>
        <p class="text-slate-500 font-medium">Suivez toutes les sorties d'argent de l'agence</p>
    </div>
    
    <a href="{{ route('depenses.create') }}" 
       class="inline-flex items-center justify-center px-6 py-3.5 bg-rose-600 text-white font-black rounded-2xl hover:bg-rose-700 shadow-xl shadow-rose-200 transition-all active:scale-95 group">
        <i class="fa-solid fa-file-invoice-dollar mr-2 group-hover:scale-110 transition-transform"></i>
        Déclarer une dépense
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-10">
    <div class="lg:col-span-1 bg-slate-900 rounded-[40px] p-8 text-white relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-10">
            <i class="fa-solid fa-money-bill-transfer text-7xl -mt-4 -mr-4"></i>
        </div>
        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Total Dépenses</p>
        <h3 class="text-3xl font-black mb-6">{{ number_format($total, 0, ',', ' ') }} <span class="text-xs text-slate-500">GNF</span></h3>
        
        <div class="space-y-4">
            @foreach($parCategorie as $cat)
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase text-slate-500">{{ $cat->categorie }}</span>
                <span class="text-xs font-bold">{{ number_format($cat->total, 0, ',', ' ') }} GNF</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="lg:col-span-3 bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase tracking-widest font-black">
                        <th class="px-8 py-5">Date & Libellé</th>
                        <th class="px-8 py-5">Catégorie</th>
                        <th class="px-8 py-5">Montant</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($depenses as $d)
                    <tr class="hover:bg-rose-50/30 transition-colors group">
                        <td class="px-8 py-5">
                            <p class="font-black text-slate-800">{{ $d->libelle }}</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">{{ $d->date_depense->format('d/m/Y') }}</p>
                        </td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-widest">{{ $d->categorie }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="font-black text-rose-600">{{ number_format($d->montant, 0, ',', ' ') }} GNF</span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <form action="{{ route('depenses.destroy', $d) }}" method="POST" onsubmit="return confirm('Supprimer cette dépense ?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 rounded-xl bg-white text-slate-400 border border-slate-100 flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-sm">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center text-slate-400 italic">Aucune dépense enregistrée.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
