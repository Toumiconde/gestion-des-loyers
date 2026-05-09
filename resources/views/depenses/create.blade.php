@extends('layouts.app')

@section('title', 'Déclarer une Dépense')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('depenses.index') }}" class="text-xs font-black text-slate-400 uppercase tracking-widest hover:text-rose-600 transition-colors flex items-center gap-2 mb-4">
            <i class="fa-solid fa-arrow-left"></i> Retour au registre
        </a>
        <h2 class="text-3xl font-black text-slate-800">Nouvelle Sortie d'Argent</h2>
        <p class="text-slate-500 font-medium">Enregistrez une charge pour le bilan financier</p>
    </div>

    <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 p-10">
        <form action="{{ route('depenses.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Libellé de la dépense</label>
                <input type="text" name="libelle" required value="{{ old('libelle') }}"
                       placeholder="Ex: Facture électricité agence - Mars"
                       class="w-full bg-slate-50 border-none h-14 px-6 rounded-2xl text-slate-700 font-bold focus:ring-4 focus:ring-rose-100 transition-all outline-none">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Catégorie</label>
                    <select name="categorie" required class="w-full bg-slate-50 border-none h-14 px-6 rounded-2xl text-slate-700 font-bold focus:ring-4 focus:ring-rose-100 transition-all outline-none appearance-none">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Date</label>
                    <input type="date" name="date_depense" required value="{{ date('Y-m-d') }}"
                           class="w-full bg-slate-50 border-none h-14 px-6 rounded-2xl text-slate-700 font-bold focus:ring-4 focus:ring-rose-100 transition-all outline-none">
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Montant (GNF)</label>
                <input type="number" name="montant" required value="{{ old('montant') }}"
                       placeholder="0"
                       class="w-full bg-slate-50 border-none h-14 px-6 rounded-2xl text-slate-700 font-black text-2xl focus:ring-4 focus:ring-rose-100 transition-all outline-none">
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Notes complémentaires</label>
                <textarea name="notes" rows="3" class="w-full bg-slate-50 border-none p-6 rounded-2xl text-slate-700 font-bold focus:ring-4 focus:ring-rose-100 transition-all outline-none"></textarea>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full h-16 bg-slate-900 text-white rounded-2xl font-black uppercase tracking-widest hover:bg-rose-600 transition-all shadow-xl shadow-slate-200 group">
                    Enregistrer la dépense
                    <i class="fa-solid fa-receipt ml-2 group-hover:rotate-12 transition-transform"></i>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
