@extends('layouts.app')

@section('title', 'Gestion des Reversements')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight mb-2">Reversements Propriétaires</h1>
            <p class="text-slate-500 font-medium">Suivez et validez les virements des loyers collectés vers les propriétaires.</p>
        </div>
        
        @if(in_array(auth()->user()->role, ['admin', 'comptable', 'gestionnaire']))
        <div class="flex gap-3">
            <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-2xl font-bold text-sm hover:bg-slate-50 transition-all shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Retour Dashboard
            </a>
        </div>
        @endif
    </div>

    @if(session('success'))
    <div class="mb-8 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 text-emerald-700 font-bold animate-fade-in-down">
        <i class="fa-solid fa-circle-check text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-[32px] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="py-6 px-8 text-[11px] font-black text-slate-400 uppercase tracking-widest">Période</th>
                        <th class="py-6 px-8 text-[11px] font-black text-slate-400 uppercase tracking-widest">Propriétaire</th>
                        <th class="py-6 px-8 text-[11px] font-black text-slate-400 uppercase tracking-widest text-right">Loyers Collectés</th>
                        <th class="py-6 px-8 text-[11px] font-black text-slate-400 uppercase tracking-widest text-right">Commission</th>
                        <th class="py-6 px-8 text-[11px] font-black text-slate-400 uppercase tracking-widest text-right">Montant Net</th>
                        <th class="py-6 px-8 text-[11px] font-black text-slate-400 uppercase tracking-widest">Statut</th>
                        <th class="py-6 px-8 text-[11px] font-black text-slate-400 uppercase tracking-widest text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($reversements as $rev)
                    <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                        <td class="py-6 px-8">
                            <div class="flex flex-col">
                                <span class="font-black text-slate-900">{{ \Carbon\Carbon::create(null, $rev->mois)->translatedFormat('F') }} {{ $rev->annee }}</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter mt-0.5">Clôturé le {{ $rev->updated_at->format('d/m/Y') }}</span>
                            </div>
                        </td>
                        <td class="py-6 px-8">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-black text-xs uppercase">
                                    {{ substr($rev->proprietaire->user->name, 0, 2) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-800">{{ $rev->proprietaire->user->name }}</span>
                                    <span class="text-[10px] text-slate-400 font-medium italic">{{ $rev->proprietaire->telephone }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-8 text-right">
                            <span class="font-black text-slate-900">{{ number_format($rev->total_revenus, 0, ',', ' ') }} <span class="text-[10px]">GNF</span></span>
                        </td>
                        <td class="py-6 px-8 text-right">
                            <span class="font-bold text-rose-600">-{{ number_format($rev->frais_gestion, 0, ',', ' ') }} <span class="text-[10px]">GNF</span></span>
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">{{ $rev->proprietaire->commission_rate ?? 10 }}% commission</p>
                        </td>
                        <td class="py-6 px-8 text-right">
                            <div class="inline-block bg-emerald-50 px-4 py-2 rounded-xl border border-emerald-100">
                                <span class="font-black text-emerald-700 text-lg">{{ number_format($rev->montant_net, 0, ',', ' ') }} <span class="text-xs">GNF</span></span>
                            </div>
                        </td>
                        <td class="py-6 px-8">
                            @if($rev->statut === 'virement_effectue')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-widest border border-emerald-200">
                                    <i class="fa-solid fa-check-double animate-bounce"></i> Payé
                                </span>
                                <p class="text-[9px] text-slate-400 font-medium mt-1 italic">Le {{ \Carbon\Carbon::parse($rev->date_virement)->format('d/m/Y') }}</p>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-black uppercase tracking-widest border border-amber-200">
                                    <i class="fa-solid fa-clock animate-pulse"></i> En attente
                                </span>
                            @endif
                        </td>
                        <td class="py-6 px-8">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('reversements.show', $rev->id) }}" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Voir le Bordereau">
                                    <i class="fa-solid fa-file-invoice-dollar"></i>
                                </a>
                                
                                @if($rev->statut !== 'virement_effectue' && in_array(auth()->user()->role, ['admin', 'comptable']))
                                <button onclick="openPaymentModal({{ $rev->id }}, '{{ $rev->proprietaire->user->name }}', {{ $rev->montant_net }})" class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="Marquer comme Payé">
                                    <i class="fa-solid fa-hand-holding-dollar"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-200 text-4xl mb-4">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>
                                <h3 class="text-xl font-bold text-slate-400 uppercase tracking-widest">Aucun reversement trouvé</h3>
                                <p class="text-slate-400 mt-2">Clôturez un mois dans le dashboard pour générer des reversements.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-8 py-6 bg-slate-50 border-t border-slate-100">
            {{ $reversements->links() }}
        </div>
    </div>
</div>

{{-- Modal de Paiement --}}
<div id="paymentModal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[9999] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[32px] w-full max-w-lg overflow-hidden shadow-2xl animate-modal-up">
        <div class="bg-emerald-600 p-8 text-white relative">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full"></div>
            <h3 class="text-2xl font-black relative z-10">Enregistrer un versement</h3>
            <p class="text-emerald-100 font-medium relative z-10" id="modalOwnerName"></p>
        </div>
        
        <form id="paymentForm" method="POST" class="p-8 space-y-6">
            @csrf
            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                <span class="text-slate-500 font-black uppercase tracking-widest text-xs">Montant à verser :</span>
                <span class="text-3xl font-black text-slate-900" id="modalAmount">0 GNF</span>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Date du virement / Paiement</label>
                    <div class="relative">
                        <input type="date" name="date_virement" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 pl-12 pr-6 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold text-slate-700">
                        <i class="fa-solid fa-calendar absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Mode de versement</label>
                    <div class="relative">
                        <select name="mode_paiement" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 pl-12 pr-6 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold text-slate-700 appearance-none">
                            <option value="virement">Virement Bancaire</option>
                            <option value="especes">Espèces (Remis en main propre)</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="cheque">Chèque</option>
                        </select>
                        <i class="fa-solid fa-money-bill-transfer absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Référence (N° Chèque, Ref Virement...)</label>
                    <div class="relative">
                        <input type="text" name="ref_virement" placeholder="Ex: VIR-87654321" class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 pl-12 pr-6 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold text-slate-700 placeholder:text-slate-300">
                        <i class="fa-solid fa-hashtag absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="button" onclick="closePaymentModal()" class="flex-1 py-4 px-6 bg-slate-100 text-slate-500 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-slate-200 transition-all">
                    Annuler
                </button>
                <button type="submit" class="flex-[2] py-4 px-6 bg-emerald-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-emerald-700 shadow-xl shadow-emerald-200 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check-double"></i> Confirmer le paiement
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPaymentModal(id, name, amount) {
        const modal = document.getElementById('paymentModal');
        const form = document.getElementById('paymentForm');
        const nameEl = document.getElementById('modalOwnerName');
        const amountEl = document.getElementById('modalAmount');
        
        form.action = `/reversements/${id}/payer`;
        nameEl.innerText = `Propriétaire : ${name}`;
        amountEl.innerText = new Intl.NumberFormat('fr-FR').format(amount) + ' GNF';
        
        modal.classList.remove('hidden');
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
    }
</script>

<style>
    @keyframes modal-up {
        from { opacity: 0; transform: translateY(20px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .animate-modal-up { animation: modal-up 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
</style>
@endsection
