@extends('layouts.app')

@section('title', 'Gestion du Personnel')

@section('content')

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Collaborateurs</h2>
        <p class="text-slate-500 font-medium">Gérez votre équipe de gestionnaires (Limite: 5)</p>
    </div>
    
    @if($staff->count() < 5)
    <a href="{{ route('staff.create') }}" 
       class="inline-flex items-center justify-center px-6 py-3.5 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 shadow-xl shadow-indigo-200 transition-all active:scale-95 group">
        <i class="fa-solid fa-user-plus mr-2 group-hover:rotate-12 transition-transform"></i>
        Nouveau gestionnaire
    </a>
    @else
    <div class="px-6 py-3.5 bg-slate-100 text-slate-400 font-black rounded-2xl border border-slate-200 cursor-not-allowed">
        <i class="fa-solid fa-lock mr-2"></i> Limite de 5 atteinte
    </div>
    @endif
</div>

@include('partials.password-reset-alert')

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($staff as $member)
    <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 p-8 hover:shadow-xl hover:shadow-indigo-500/5 transition-all group relative overflow-hidden">
        <div class="absolute -right-4 -top-4 opacity-5 group-hover:opacity-10 transition-opacity">
            <i class="fa-solid fa-user-shield text-8xl"></i>
        </div>
        
        <div class="flex items-center gap-5 mb-6">
            <div class="w-16 h-16 rounded-3xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-2xl">
                {{ substr($member->name, 0, 1) }}
            </div>
            <div>
                <h3 class="font-black text-slate-800 text-lg">{{ $member->name }}</h3>
                <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">Gestionnaire Actif</p>
            </div>
        </div>

        <div class="space-y-3 mb-8">
            <div class="flex items-center gap-3 text-sm text-slate-500">
                <i class="fa-solid fa-envelope text-slate-300 w-4"></i>
                {{ $member->email }}
            </div>
            <div class="flex items-center gap-3 text-sm text-slate-500">
                <i class="fa-solid fa-calendar text-slate-300 w-4"></i>
                Recruté le {{ $member->created_at->format('d/m/Y') }}
            </div>
        </div>

        <div class="flex items-center justify-between pt-6 border-t border-slate-50">
            <div class="flex items-center gap-2">
                <form action="{{ route('admin.users.reset-password', $member) }}" method="POST" onsubmit="return confirm('Réinitialiser le mot de passe de {{ $member->name }} ?')">
                    @csrf
                    <button type="submit" class="text-[10px] font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-widest flex items-center gap-2 group/btn">
                        <i class="fa-solid fa-key group-hover/btn:rotate-45 transition-transform"></i>
                        Reset Pass
                    </button>
                </form>
            </div>
            
            <form action="{{ route('staff.destroy', $member) }}" method="POST" onsubmit="return confirm('Révoquer l\'accès de ce gestionnaire ?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-[10px] font-black text-rose-500 hover:text-rose-700 uppercase tracking-widest hover:underline">
                    Révoquer
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full py-20 text-center bg-white rounded-[40px] border-2 border-dashed border-slate-100">
        <i class="fa-solid fa-users-slash text-5xl text-slate-200 mb-4 block"></i>
        <p class="text-slate-400 font-medium">Vous n'avez pas encore recruté de gestionnaire.</p>
        <p class="text-xs text-slate-300 mt-2">Ils vous aideront à gérer les loyers et les locataires.</p>
    </div>
    @endforelse
</div>

@endsection
