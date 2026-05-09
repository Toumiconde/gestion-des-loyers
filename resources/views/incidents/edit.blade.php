@extends('layouts.app')

@section('title', 'Gestion du Chantier de Maintenance')

@section('content')

<div class="max-w-4xl mx-auto py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <a href="{{ route('incidents.show', $incident) }}" class="text-xs font-black text-slate-400 uppercase tracking-widest hover:text-blue-600 transition-colors flex items-center gap-2 mb-2">
                <i class="fa-solid fa-arrow-left"></i> Retour aux détails
            </a>
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Pilotage du chantier</h2>
            <p class="text-slate-500 font-medium">Mettez à jour le suivi technique et financier de l'incident</p>
        </div>
    </div>

    <form method="POST" action="{{ route('incidents.update', $incident) }}" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    
                    {{-- État & Intervenant --}}
                    <div class="space-y-6">
                        <div class="flex items-center gap-2 mb-4">
                            <i class="fa-solid fa-list-check text-blue-600"></i>
                            <h3 class="font-black text-slate-800 uppercase tracking-widest text-sm">Suivi Technique</h3>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">État d'avancement</label>
                            <select name="statut" required
                                    class="w-full bg-slate-50 border-none h-14 px-6 rounded-2xl text-slate-700 font-bold focus:ring-4 focus:ring-blue-100 transition-all outline-none appearance-none cursor-pointer">
                                @foreach(['ouvert' => 'Signalé', 'en_devis' => 'En Devis', 'en_travaux' => 'En Travaux', 'resolu' => 'Résolu (Travaux finis)', 'paye' => 'Payé (Clôture)'] as $val => $label)
                                <option value="{{ $val }}" {{ old('statut', $incident->statut) == $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Choisir un Maintenancier (Optionnel)</label>
                            <select name="maintenancier_id" id="maintenancier_select"
                                    class="w-full bg-slate-50 border-none h-14 px-6 rounded-2xl text-slate-700 font-bold focus:ring-4 focus:ring-blue-100 transition-all outline-none appearance-none cursor-pointer">
                                <option value="">Saisie manuelle ci-dessous...</option>
                                @foreach($maintenanciers as $m)
                                    <option value="{{ $m->id }}" data-nom="{{ $m->nom }}" data-tel="{{ $m->telephone }}" {{ old('maintenancier_id', $incident->maintenancier_id) == $m->id ? 'selected' : '' }}>
                                        {{ $m->nom }} ({{ $m->specialite }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nom du Technicien / Entreprise</label>
                            <input type="text" name="technicien_nom" id="technicien_nom" value="{{ old('technicien_nom', $incident->technicien_nom) }}"
                                   placeholder="Ex: Plomberie Moderne"
                                   class="w-full bg-slate-50 border-none h-14 px-6 rounded-2xl text-slate-700 font-bold focus:ring-4 focus:ring-blue-100 transition-all outline-none">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Téléphone Technicien</label>
                            <input type="text" name="technicien_tel" id="technicien_tel" value="{{ old('technicien_tel', $incident->technicien_tel) }}"
                                   placeholder="+224 ..."
                                   class="w-full bg-slate-50 border-none h-14 px-6 rounded-2xl text-slate-700 font-bold focus:ring-4 focus:ring-blue-100 transition-all outline-none">
                        </div>
                    </div>

                    <script>
                        document.getElementById('maintenancier_select').addEventListener('change', function() {
                            const selectedOption = this.options[this.selectedIndex];
                            if (selectedOption.value) {
                                document.getElementById('technicien_nom').value = selectedOption.getAttribute('data-nom');
                                document.getElementById('technicien_tel').value = selectedOption.getAttribute('data-tel');
                            }
                        });
                    </script>

                    {{-- Budget & Finances --}}
                    <div class="space-y-6">
                        <div class="flex items-center gap-2 mb-4">
                            <i class="fa-solid fa-coins text-emerald-600"></i>
                            <h3 class="font-black text-slate-800 uppercase tracking-widest text-sm">Budget & Paiement</h3>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Montant du Devis (GNF)</label>
                            <input type="number" name="devis_montant" value="{{ old('devis_montant', $incident->devis_montant) }}"
                                   class="w-full bg-slate-50 border-none h-14 px-6 rounded-2xl text-slate-700 font-bold focus:ring-4 focus:ring-emerald-100 transition-all outline-none">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Notes sur le devis (Optionnel)</label>
                            <textarea name="devis_note" rows="2"
                                   class="w-full bg-slate-50 border-none px-6 py-4 rounded-2xl text-slate-700 font-bold focus:ring-4 focus:ring-emerald-100 transition-all outline-none">{{ old('devis_note', $incident->devis_note) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Coût Réel (Facturé)</label>
                            <input type="number" name="cout_reel" value="{{ old('cout_reel', $incident->cout_reel) }}"
                                   class="w-full bg-slate-50 border-none h-14 px-6 rounded-2xl text-rose-600 font-black text-xl focus:ring-4 focus:ring-rose-100 transition-all outline-none">
                            <p class="mt-2 text-[10px] text-slate-400 font-medium italic">Note : Le coût réel sera automatiquement déduit de votre bénéfice net dès que l'incident passera en statut "Payé".</p>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Date de résolution</label>
                            <input type="date" name="date_resolution" value="{{ old('date_resolution', $incident->date_resolution ? $incident->date_resolution->format('Y-m-d') : '') }}"
                                   class="w-full bg-slate-50 border-none h-14 px-6 rounded-2xl text-slate-700 font-bold focus:ring-4 focus:ring-blue-100 transition-all outline-none">
                        </div>
                    </div>

                </div>

                <div class="mt-12 flex items-center gap-4">
                    <button type="submit"
                            class="flex-1 h-16 bg-slate-900 text-white font-black rounded-2xl hover:bg-blue-600 shadow-xl shadow-slate-200 transition-all active:scale-95 uppercase tracking-widest text-xs">
                        Enregistrer les modifications
                        <i class="fa-solid fa-check-circle ml-2"></i>
                    </button>
                    <a href="{{ route('incidents.show', $incident) }}"
                       class="px-8 h-16 bg-slate-100 text-slate-600 font-bold rounded-2xl hover:bg-slate-200 transition-all flex items-center justify-center uppercase tracking-widest text-xs">
                        Annuler
                    </a>
                </div>
            </div>
        </div>

        <div class="p-8 bg-rose-50 rounded-[30px] border border-rose-100 flex gap-6">
            <div class="w-12 h-12 shrink-0 rounded-2xl bg-white flex items-center justify-center text-rose-600 shadow-sm">
                <i class="fa-solid fa-circle-exclamation text-xl"></i>
            </div>
            <div>
                <h4 class="font-black text-rose-900 mb-1">Attention Automatique</h4>
                <p class="text-xs text-rose-700/70 leading-relaxed">Le passage au statut <strong>"Payé"</strong> est irréversible en termes comptables. Il générera une dépense de catégorie 'maintenance' qui sera déduite de votre chiffre d'affaires mensuel.</p>
            </div>
        </div>
    </form>
</div>

@endsection