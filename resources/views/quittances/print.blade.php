<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quittance - {{ $quittance->numero_quittance }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: white;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            margin: 0;
            padding: 0;
        }
        @media print {
            .no-print { display: none !important; }
            #quittance-premium {
                border: none !important;
                border-radius: 0 !important;
                width: 210mm !important;
                height: 297mm !important;
                margin: 0 !important;
                padding: 15mm !important;
                overflow: hidden !important;
            }
        }
        #quittance-premium {
            background: white;
            border-radius: 40px;
            padding: 40px;
            position: relative;
            overflow: hidden;
            border: 1px solid #f1f5f9;
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            box-sizing: border-box;
        }
    </style>
</head>
<body class="bg-white">

    {{-- Boutons flottants --}}
    <div class="fixed top-6 right-6 z-50 flex gap-4 no-print">
        <button onclick="window.close()" class="px-5 py-2.5 bg-white text-slate-700 rounded-xl font-bold text-xs uppercase shadow-xl hover:bg-slate-50 transition-all border border-slate-200">Retour</button>
        <button onclick="window.print()" class="px-8 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-xs uppercase shadow-xl hover:bg-blue-600 transition-all">Imprimer le PDF Premium</button>
    </div>

    @php 
        $settings = \App\Models\Parametre::all()->pluck('valeur', 'cle');
        $charges = $quittance->paiement->contrat->bien->charges ?? 0;
        $loyerBase = $quittance->paiement->montant - $charges - ($quittance->paiement->penalite ?? 0);
        $proprioSignature = $quittance->paiement->contrat->bien->proprietaire->signature;
        $agencySignature = $settings['signature'] ?? null;
    @endphp

    <div id="quittance-premium">
        {{-- Filigrane PAYÉ --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-[0.03] pointer-events-none rotate-[-35deg] select-none">
            <p class="text-[200px] font-black tracking-tighter">PAYÉ</p>
        </div>

        {{-- Entête --}}
        <div class="flex justify-between items-start mb-8 relative z-10">
            <div class="flex items-center gap-6">
                @if(!empty($settings['logo']))
                    <img src="{{ Storage::url($settings['logo']) }}" class="h-20 w-auto object-contain">
                @else
                    <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-blue-100">G</div>
                @endif
                <div>
                    <h1 class="text-2xl font-black text-slate-900">{{ $settings['nom_agence'] ?? 'GESTLOYER IMMOBILIER' }}</h1>
                    <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em]">{{ $settings['adresse'] ?? 'Agence Immobilière Agréée' }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">{{ $settings['telephone'] ?? '' }}</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-4xl font-black text-slate-900 mb-2">QUITTANCE</h2>
                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Référence : <span class="text-slate-900">{{ $quittance->numero_quittance }}</span></p>
                <p class="text-[10px] font-bold text-slate-400 mt-1 italic">Document officiel de règlement</p>
            </div>
        </div>

        {{-- Bloc Parties --}}
        <div class="grid grid-cols-2 gap-12 mb-8 relative z-10">
            <div class="p-8 bg-slate-50 rounded-[30px] border border-slate-100">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Bailleur (Propriétaire)</h3>
                <p class="text-lg font-black text-slate-900 mb-1">{{ $quittance->paiement->contrat->bien->proprietaire->user->name }}</p>
                <p class="text-xs text-slate-500 leading-relaxed">{{ $quittance->paiement->contrat->bien->proprietaire->adresse_professionnelle ?: $quittance->paiement->contrat->bien->proprietaire->adresse }}</p>
            </div>
            <div class="p-8 bg-blue-50 rounded-[30px] border border-blue-100/50">
                <h3 class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-4">Locataire</h3>
                <p class="text-lg font-black text-slate-900 mb-1">{{ $quittance->paiement->contrat->locataire->nom_complet }}</p>
                <p class="text-xs text-slate-500 leading-relaxed">{{ $quittance->paiement->contrat->bien->libelle }} — {{ $quittance->paiement->contrat->bien->adresse }}</p>
            </div>
        </div>

        {{-- Description du Mois --}}
        <div class="mb-8 text-center relative z-10">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Période concernée</p>
            <div class="inline-block px-10 py-4 bg-slate-900 text-white rounded-2xl font-black text-xl shadow-xl shadow-slate-200">
                {{ \Carbon\Carbon::parse($quittance->paiement->mois_concerne)->locale('fr')->isoFormat('MMMM YYYY') }}
            </div>
        </div>

        {{-- Tableau des Montants --}}
        <div class="mb-8 relative z-10">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                        <th class="py-4 px-2">Désignation</th>
                        <th class="py-4 px-2 text-right">Montant (GNF)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="py-4 px-2">
                            <p class="font-black text-slate-800">Loyer Principal</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Occupation mensuelle du logement</p>
                        </td>
                        <td class="py-4 px-2 text-right font-black text-slate-800">{{ number_format($loyerBase, 0, ',', ' ') }}</td>
                    </tr>
                    @if($charges > 0)
                    <tr>
                        <td class="py-4 px-2">
                            <p class="font-black text-slate-800">Charges Locatives</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Provisions pour charges</p>
                        </td>
                        <td class="py-4 px-2 text-right font-black text-slate-800">{{ number_format($charges, 0, ',', ' ') }}</td>
                    </tr>
                    @endif
                    @if($quittance->paiement->penalite > 0)
                    <tr>
                        <td class="py-4 px-2">
                            <p class="font-black text-rose-600">Pénalités de retard</p>
                        </td>
                        <td class="py-4 px-2 text-right font-black text-rose-600">{{ number_format($quittance->paiement->penalite, 0, ',', ' ') }}</td>
                    </tr>
                    @endif
                    <tr class="bg-slate-50/50">
                        <td class="py-8 px-6 rounded-l-[20px]">
                            <p class="text-xl font-black text-slate-900 uppercase tracking-tighter">Total Net Payé</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Règlement reçu le {{ $quittance->paiement->date_paiement->format('d/m/Y') }} par {{ $quittance->paiement->mode_reglement }}</p>
                        </td>
                        <td class="py-8 px-6 text-right rounded-r-[20px]">
                            <p class="text-3xl font-black text-blue-600 tracking-tight">{{ number_format($quittance->paiement->montant, 0, ',', ' ') }} <span class="text-xs">GNF</span></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Mentions Légales & Signature --}}
        <div class="grid grid-cols-2 gap-12 mt-8 relative z-10">
            <div class="text-xs text-slate-400 leading-relaxed font-medium">
                <p class="mb-4">Cette quittance annule tout reçu à valoir précédemment délivré pour la même période.</p>
                <p>En cas de paiement par chèque, la quittance n'est valable que sous réserve de l'encaissement définitif.</p>
                <div class="mt-8">
                    <p class="font-black text-slate-900 uppercase text-[8px] tracking-widest">Tampon de l'agence</p>
                    <div class="mt-2 w-24 h-24 border-2 border-blue-600/20 rounded-full flex items-center justify-center rotate-12">
                        <p class="text-blue-600 font-black text-[10px] text-center uppercase tracking-tighter">Payé<br>Directement</p>
                    </div>
                </div>
            </div>
            
            <div class="text-right">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Signature & Cachet Officiel</p>
                <div class="inline-block relative">
                    <div class="w-64 h-32 flex items-center justify-end">
                        @if(!empty($proprioSignature))
                            <img src="{{ Storage::url($proprioSignature) }}" class="max-h-full max-w-full object-contain grayscale hover:grayscale-0 transition-all duration-500">
                        @elseif(!empty($agencySignature))
                            <img src="{{ Storage::url($agencySignature) }}" class="max-h-full max-w-full object-contain grayscale hover:grayscale-0 transition-all duration-500">
                        @else
                            <div class="w-48 border-b-2 border-slate-200 h-20"></div>
                        @endif
                    </div>
                    <p class="mt-4 font-black text-slate-900 uppercase tracking-widest text-[10px]">{{ $quittance->paiement->contrat->bien->proprietaire->user->name }}</p>
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Le Bailleur / Mandataire</p>
                </div>
            </div>
        </div>

        <div class="absolute bottom-8 left-0 w-full text-center no-print">
            <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.3em]">Édité par GESTLOYER Pro</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 1000);
        }
    </script>
</body>
</html>
