@extends('layouts.app')

@section('title', 'Journal d\'Activité')

@section('content')

<div class="mb-8">
    <h2 class="text-3xl font-black text-slate-800">Logs d'Activité</h2>
    <p class="text-slate-500 font-medium">Suivi en temps réel des actions effectuées sur la plateforme</p>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-8 border-b border-slate-100 bg-slate-50/30 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <h3 class="text-xl font-black text-slate-800">Audit Trail</h3>
        </div>
        <span class="px-4 py-1.5 rounded-xl bg-slate-100 text-slate-500 text-xs font-black uppercase tracking-widest">Dernières 24h</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase tracking-widest font-black">
                    <th class="px-8 py-5">Date & Heure</th>
                    <th class="px-8 py-5">Utilisateur</th>
                    <th class="px-8 py-5">Action</th>
                    <th class="px-8 py-5">Détails</th>
                    <th class="px-8 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($logs as $log)
                <tr class="hover:bg-slate-50/30 transition-colors">
                    <td class="px-8 py-5">
                        <p class="font-black text-slate-700">{{ $log->created_at->format('d/m/Y') }}</p>
                        <p class="text-[10px] text-slate-400 font-bold">{{ $log->created_at->format('H:i:s') }}</p>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-[10px] font-black border border-slate-200">
                                {{ substr($log->user->name ?? '?', 0, 1) }}
                            </div>
                            <span class="text-sm font-bold text-slate-700">{{ $log->user->name ?? 'Utilisateur supprimé' }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        @php
                            $actionClasses = [
                                'create' => 'bg-emerald-100 text-emerald-700',
                                'creation' => 'bg-emerald-100 text-emerald-700',
                                'update' => 'bg-blue-100 text-blue-700',
                                'modification' => 'bg-blue-100 text-blue-700',
                                'delete' => 'bg-rose-100 text-rose-700',
                                'login'  => 'bg-purple-100 text-purple-700',
                                'connexion' => 'bg-purple-100 text-purple-700',
                                'paiement' => 'bg-emerald-100 text-emerald-700',
                                'profile_updated' => 'bg-amber-100 text-amber-700',
                            ];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase {{ $actionClasses[$log->action] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ str_replace('_', ' ', $log->action) }}
                        </span>
                    </td>
                    <td class="px-8 py-5">
                        <p class="text-xs text-slate-500 truncate max-w-xs" title="{{ json_encode($log->details) }}">
                            {{ $log->details['message'] ?? 'Action système' }}
                        </p>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <a href="{{ route('activity-logs.show', $log) }}" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all ml-auto">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-20 text-center text-slate-400 italic">
                        Aucun log d'activité enregistré.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/50">
        {{ $logs->links() }}
    </div>
    @endif
</div>

@endsection