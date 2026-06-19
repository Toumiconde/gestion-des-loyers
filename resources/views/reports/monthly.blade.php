<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Mensuel - {{ $monthNum }}/{{ $selectedYear }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .report-card { border: 1px solid #e2e8f0; shadow: none; }
        }
    </style>
</head>
<body class="bg-slate-50 p-10">

    <div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-[40px] overflow-hidden report-card">
        {{-- Header du Rapport --}}
        <div class="bg-slate-900 p-12 text-white flex justify-between items-start">
            <div>
                <h1 class="text-4xl font-black mb-2 tracking-tighter">BILAN MENSUEL</h1>
                <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">Période : {{ $monthNum }}/{{ $selectedYear }}</p>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-black">GESTLOYER Pro</h2>
                <p class="text-xs text-slate-500">Généré le {{ date('d/m/Y à H:i') }}</p>
            </div>
        </div>

        <div class="p-12">
            {{-- Résumé Financier --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-12">
                <div class="p-6 bg-emerald-50 rounded-3xl border border-emerald-100">
                    <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1">Revenus</p>
                    <p class="text-2xl font-black text-slate-800">+ {{ number_format($revenus->sum('montant'), 0, ',', ' ') }}</p>
                </div>
                <div class="p-6 bg-rose-50 rounded-3xl border border-rose-100">
                    <p class="text-[10px] font-black text-rose-600 uppercase tracking-widest mb-1">Dépenses</p>
                    <p class="text-2xl font-black text-slate-800">- {{ number_format($depenses->sum('montant'), 0, ',', ' ') }}</p>
                </div>
                <div class="p-6 bg-slate-100 rounded-3xl border border-slate-200">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Commission Agence</p>
                    <p class="text-2xl font-black text-slate-800">- {{ number_format($fraisGestion, 0, ',', ' ') }}</p>
                </div>
                <div class="p-6 bg-slate-900 rounded-3xl text-white">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Bilan Net (À verser)</p>
                    <p class="text-2xl font-black text-white">{{ number_format($revenus->sum('montant') - $depenses->sum('montant') - $fraisGestion, 0, ',', ' ') }}</p>
                </div>
            </div>

            {{-- Détail des Revenus --}}
            <div class="mb-12">
                <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <div class="w-1 h-4 bg-emerald-500 rounded-full"></div> Détail des encaissements
                </h3>
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-100">
                            <th class="py-3 text-left">Locataire / Bien</th>
                            <th class="py-3 text-right">Montant (GNF)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($revenus as $r)
                        <tr>
                            <td class="py-3">
                                <span class="font-bold text-slate-700">{{ $r->contrat->locataire->prenom }} {{ $r->contrat->locataire->nom }}</span>
                                <span class="text-slate-400 block">{{ $r->contrat->bien->libelle }}</span>
                            </td>
                            <td class="py-3 text-right font-black text-slate-800">{{ number_format($r->montant, 0, ',', ' ') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Détail des Dépenses --}}
            <div>
                <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <div class="w-1 h-4 bg-rose-500 rounded-full"></div> Détail des charges
                </h3>
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-100">
                            <th class="py-3 text-left">Nature de la dépense</th>
                            <th class="py-3 text-right">Montant (GNF)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($depenses as $d)
                        <tr>
                            <td class="py-3">
                                <span class="font-bold text-slate-700">{{ $d->libelle }}</span>
                                <span class="text-[10px] text-slate-400 block uppercase">{{ $d->categorie }}</span>
                            </td>
                            <td class="py-3 text-right font-black text-rose-600">- {{ number_format($d->montant, 0, ',', ' ') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Footer du Rapport --}}
        <div class="bg-slate-50 p-8 text-center border-t border-slate-100">
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-4">Ce document sert de pièce comptable pour l'agence</p>
            <button onclick="window.print()" class="no-print px-8 py-3 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:scale-105 transition-all shadow-xl">
                Imprimer le rapport / Sauvegarder en PDF
            </button>
        </div>
    </div>

    <p class="text-center mt-8 text-slate-300 text-xs no-print">© {{ date('Y') }} GESTLOYER - Système de gestion locative premium</p>

</body>
</html>
