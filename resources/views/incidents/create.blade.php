@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#F8FAFC] p-8">
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-12">
            <div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight mb-2">Signaler un <span class="text-blue-600">Incident</span></h1>
                <p class="text-slate-500 font-medium">Décrivez le problème rencontré dans votre logement.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm hover:shadow-md transition-all">
                <i class="fa-solid fa-xmark text-slate-400"></i>
            </a>
        </div>

        <form action="{{ route('incidents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            
            <div class="bg-white rounded-[40px] p-10 border border-slate-100 shadow-sm space-y-8">
                {{-- Type & Objet --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Nature du problème</label>
                        <select name="type" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-[20px] focus:ring-2 focus:ring-blue-500 font-bold text-slate-700">
                            <option value="plomberie">Plomberie</option>
                            <option value="electricite">Électricité</option>
                            <option value="maconnerie">Maçonnerie / Peinture</option>
                            <option value="climatisation">Climatisation / Ventilation</option>
                            <option value="serrurerie">Serrurerie / Portes</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Objet résumé</label>
                        <input type="text" name="objet" required placeholder="Ex: Fuite sous l'évier" 
                               class="w-full px-6 py-4 bg-slate-50 border-none rounded-[20px] focus:ring-2 focus:ring-blue-500 font-bold text-slate-700">
                    </div>
                </div>

                {{-- Description --}}
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Description détaillée</label>
                    <textarea name="description" rows="5" required placeholder="Expliquez-nous en détail le problème..."
                              class="w-full px-6 py-4 bg-slate-50 border-none rounded-[25px] focus:ring-2 focus:ring-blue-500 font-bold text-slate-700"></textarea>
                </div>

                {{-- Urgence & Photo --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Degré d'urgence</label>
                        <div class="flex gap-4">
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="priorite" value="basse" class="hidden peer">
                                <div class="p-4 text-center rounded-2xl bg-slate-50 border-2 border-transparent peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition-all">
                                    <span class="text-[10px] font-black text-slate-400 uppercase peer-checked:text-emerald-600">Normal</span>
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="priorite" value="moyenne" checked class="hidden peer">
                                <div class="p-4 text-center rounded-2xl bg-slate-50 border-2 border-transparent peer-checked:border-amber-500 peer-checked:bg-amber-50 transition-all">
                                    <span class="text-[10px] font-black text-slate-400 uppercase peer-checked:text-amber-600">Urgent</span>
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="priorite" value="haute" class="hidden peer">
                                <div class="p-4 text-center rounded-2xl bg-slate-50 border-2 border-transparent peer-checked:border-rose-500 peer-checked:bg-rose-50 transition-all">
                                    <span class="text-[10px] font-black text-slate-400 uppercase peer-checked:text-rose-600">Critique</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Photo (Optionnel)</label>
                        <div class="relative group">
                            <input type="file" name="photo" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="w-full px-6 py-4 bg-slate-50 border-2 border-dashed border-slate-200 rounded-[20px] flex items-center justify-center gap-3 group-hover:border-blue-400 transition-all">
                                <i class="fa-solid fa-camera text-slate-400"></i>
                                <span class="text-sm font-bold text-slate-500">Ajouter une photo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-6">
                <a href="{{ route('dashboard') }}" class="text-sm font-black text-slate-400 uppercase tracking-widest hover:text-slate-600">Annuler</a>
                <button type="submit" class="px-12 py-5 bg-blue-600 text-white rounded-[25px] font-black text-sm uppercase tracking-widest hover:bg-slate-900 transition-all shadow-xl shadow-blue-100 flex items-center gap-3">
                    <i class="fa-solid fa-paper-plane"></i>
                    Envoyer le rapport
                </button>
            </div>
        </form>
    </div>
</div>
@endsection