@extends('layouts.app')

@section('title', 'Détails de l\'activité')

@section('content')

<div class="max-w-4xl mx-auto py-8">
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm font-medium">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-blue-600 transition-colors">
                    <i class="fa-solid fa-house mr-2"></i> Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-slate-300 mx-2 text-xs"></i>
                    <a href="{{ route('activity-logs.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">Logs</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-slate-300 mx-2 text-xs"></i>
                    <span class="text-slate-600">Détails Log</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-slate-900 p-8 text-white flex items-center justify-between">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center text-3xl">
                    <i class="fa-solid fa-file-code"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black mb-1">Détails de l'Action</h2>
                    <p class="text-slate-400 text-sm">ID de transaction: #{{ $activityLog->id }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest mb-1">Timestamp</p>
                <p class="font-mono text-sm text-blue-400">{{ $activityLog->created_at->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-10">
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400"><i class="fa-solid fa-user"></i></div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Utilisateur</p>
                            <p class="font-bold text-slate-800">{{ $activityLog->user?->name ?? 'Système' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400"><i class="fa-solid fa-bolt"></i></div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Action effectuée</p>
                            <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-[10px] font-black uppercase tracking-widest">
                                {{ $activityLog->action }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400"><i class="fa-solid fa-network-wired"></i></div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Adresse IP</p>
                            <p class="font-mono text-slate-700">{{ $activityLog->ip_address ?: '127.0.0.1' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400"><i class="fa-solid fa-cubes"></i></div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Modèle concerné</p>
                            <p class="font-bold text-slate-800">{{ $activityLog->model_type ? class_basename($activityLog->model_type) . ' #' . $activityLog->model_id : 'Global' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 rounded-3xl p-8 border border-slate-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Détails techniques (JSON)</h3>
                    <div class="flex gap-2">
                        <div class="w-2 h-2 rounded-full bg-rose-400"></div>
                        <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                        <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                    </div>
                </div>
                <div class="bg-[#02132D] rounded-2xl p-6 overflow-x-auto">
                    <pre class="text-blue-300 font-mono text-xs leading-relaxed">@json($activityLog->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</pre>
                </div>
            </div>
        </div>

        <div class="p-8 border-t border-slate-100 flex justify-center">
            <a href="{{ route('activity-logs.index') }}" class="px-8 py-3 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-all flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Retour au journal
            </a>
        </div>
    </div>
</div>

@endsection