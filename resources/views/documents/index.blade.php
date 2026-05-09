@extends('layouts.app')

@section('title', 'Gestion Documentaire')

@section('content')

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Coffre-fort Numérique</h2>
        <p class="text-slate-500 font-medium">Gérez vos contrats, quittances et pièces d'identité</p>
    </div>
    
    <a href="{{ route('documents.create') }}" 
       class="inline-flex items-center justify-center px-6 py-3.5 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 shadow-xl shadow-blue-200 transition-all active:scale-95 group">
        <i class="fa-solid fa-cloud-arrow-up mr-2 group-hover:-translate-y-1 transition-transform"></i>
        Importer un document
    </a>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase tracking-widest font-black">
                    <th class="px-8 py-5">Document</th>
                    <th class="px-8 py-5">Catégorie & Propriétaire</th>
                    <th class="px-8 py-5">Date d'Ajout</th>
                    <th class="px-8 py-5">Taille</th>
                    <th class="px-8 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($documents as $d)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                                @if(Str::endsWith($d->path, ['.pdf'])) <i class="fa-solid fa-file-pdf"></i>
                                @elseif(Str::endsWith($d->path, ['.jpg', '.png', '.jpeg'])) <i class="fa-solid fa-file-image"></i>
                                @else <i class="fa-solid fa-file-lines"></i> @endif
                            </div>
                            <div>
                                <p class="font-black text-slate-800">{{ $d->nom }}</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $d->type }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-sm text-slate-600 font-medium capitalize">{{ $d->categorie ?: 'Divers' }}</span>
                    </td>
                    <td class="px-8 py-5 text-sm text-slate-500">
                        {{ $d->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-[10px] font-black text-slate-400 uppercase">{{ number_format($d->taille / 1024, 1) }} KB</span>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('documents.show', $d) }}" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all">
                                <i class="fa-solid fa-eye text-sm"></i>
                            </a>
                            <a href="{{ Storage::url($d->path) }}" target="_blank" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all">
                                <i class="fa-solid fa-download text-sm"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-20 text-center text-slate-400 italic">
                        <i class="fa-solid fa-folder-open text-4xl mb-4 block opacity-10"></i>
                        Aucun document disponible.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection