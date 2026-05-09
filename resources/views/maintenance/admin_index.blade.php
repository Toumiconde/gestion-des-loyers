@extends('layouts.app')

@section('title', 'Gestion de la Maintenance & IA')

@section('content')

<div class="mb-10">
    <h2 class="text-3xl font-black text-slate-800">Support Technique Proprios</h2>
    <p class="text-slate-500 font-medium">Surveillez les requêtes assistées par le système et intervenez si nécessaire</p>
</div>

<div class="grid grid-cols-1 gap-8">
    @forelse($requests as $req)
    <div class="bg-white rounded-[40px] p-10 border border-slate-100 shadow-sm relative overflow-hidden group">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-800 flex items-center justify-center font-black text-xl">
                    {{ substr($req->user->name, 0, 1) }}
                </div>
                <div>
                    <h4 class="text-lg font-black text-slate-800">{{ $req->user->name }}</h4>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">{{ $req->created_at->diffForHumans() }}</p>
                </div>
            </div>
            <div class="flex flex-col items-end gap-2">
                <span class="px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest
                    {{ $req->status === 'resolved' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-200' : ($req->status === 'auto_replied' ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-200' : 'bg-amber-500 text-white shadow-lg shadow-amber-200') }}">
                    {{ $req->status }}
                </span>
            </div>
        </div>

        <div class="mb-8 p-6 bg-slate-50 rounded-3xl border border-slate-100">
            <h5 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Inquiétude du Propriétaire : {{ $req->subject }}</h5>
            <p class="text-slate-700 font-medium leading-relaxed">{{ $req->message }}</p>
        </div>

        @if($req->auto_response)
        <div class="mb-8 p-6 bg-indigo-50 rounded-3xl border border-indigo-100 border-l-8">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center">
                    <i class="fa-solid fa-robot text-xs"></i>
                </div>
                <h5 class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Réponse Automatisée par le Système</h5>
            </div>
            <p class="text-indigo-900 text-sm font-bold leading-relaxed">{{ $req->auto_response }}</p>
        </div>
        @endif

        @if($req->status !== 'resolved')
        <div class="flex justify-end gap-4">
            <a href="{{ route('messages.create', ['receiver_id' => $req->user_id]) }}" class="px-6 py-3 bg-white border border-slate-200 text-slate-700 font-black rounded-xl hover:bg-slate-900 hover:text-white transition-all text-xs uppercase tracking-widest">
                Contacter le propriétaire
            </a>
            <form action="{{ route('maintenance.resolve', $req) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-6 py-3 bg-emerald-600 text-white font-black rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200 text-xs uppercase tracking-widest">
                    Marquer comme résolu
                </button>
            </form>
        </div>
        @endif
    </div>
    @empty
    <div class="bg-white rounded-[40px] p-20 border border-dashed border-slate-200 text-center text-slate-400">
        <i class="fa-solid fa-screwdriver-wrench text-5xl mb-6 opacity-20"></i>
        <p class="font-bold">Aucune demande de maintenance en attente.</p>
    </div>
    @endforelse

    <div class="mt-6">
        {{ $requests->links() }}
    </div>
</div>

@endsection
