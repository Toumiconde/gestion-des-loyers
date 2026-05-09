@extends('layouts.app')

@section('title', 'Détails du Document')

@section('content')

<div class="max-w-6xl mx-auto py-8">
    {{-- Fil d'ariane --}}
    <div class="flex items-center justify-between mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm font-medium">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-blue-600 transition-colors">
                        <i class="fa-solid fa-house mr-2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-slate-300 mx-2 text-xs"></i>
                        <a href="{{ route('documents.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">Documents</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-slate-300 mx-2 text-xs"></i>
                        <span class="text-slate-600">Visualisation</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="flex gap-3">
            <a href="{{ Storage::url($document->chemin ?? $document->path) }}" 
               download="{{ $document->nom }}"
               class="px-5 py-2.5 bg-emerald-50 text-emerald-600 font-bold rounded-xl hover:bg-emerald-600 hover:text-white transition-all flex items-center gap-2 shadow-sm shadow-emerald-100">
                <i class="fa-solid fa-download"></i> Télécharger
            </a>
            <form action="{{ route('documents.destroy', $document) }}" method="POST" onsubmit="return confirm('Supprimer ce document ?')" class="inline">
                @csrf @method('DELETE')
                <button type="submit" class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all">
                    <i class="fa-solid fa-trash-can text-sm"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Détails --}}
        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                <div class="w-20 h-20 rounded-3xl bg-blue-50 text-blue-600 flex items-center justify-center text-3xl mb-6 shadow-sm border border-blue-100/50">
                    @php $ext = pathinfo($document->chemin ?? $document->path, PATHINFO_EXTENSION); @endphp
                    @if(strtolower($ext) === 'pdf') <i class="fa-solid fa-file-pdf"></i>
                    @elseif(in_array(strtolower($ext), ['jpg', 'jpeg', 'png'])) <i class="fa-solid fa-file-image"></i>
                    @else <i class="fa-solid fa-file-lines"></i> @endif
                </div>
                <h2 class="text-2xl font-black text-slate-800 break-words mb-2">{{ $document->nom }}</h2>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">{{ $document->type }}</p>
                
                <div class="mt-8 space-y-6 pt-8 border-t border-slate-50">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-400">Taille</span>
                        <span class="font-bold text-slate-700">{{ number_format(($document->taille_ko ?? $document->taille / 1024), 1) }} KB</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-400">Ajouté par</span>
                        <span class="font-bold text-slate-700">{{ $document->uploadedBy?->name ?? 'Système' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-400">Le</span>
                        <span class="font-bold text-slate-700">{{ $document->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-blue-900 rounded-3xl p-8 text-white relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/5 rounded-full"></div>
                <h3 class="text-lg font-black mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-blue-400"></i> Stockage Sécurisé
                </h3>
                <p class="text-blue-300 text-sm leading-relaxed">Ce document est chiffré et stocké de manière sécurisée. Seuls les administrateurs et les parties autorisées peuvent y accéder.</p>
            </div>
        </div>

        {{-- Aperçu --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden min-h-[700px] flex flex-col">
                <div class="p-4 border-b border-slate-50 bg-slate-50/50 flex items-center justify-between">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Aperçu du document</span>
                    @if($document->viewed_at)
                        <span class="flex items-center gap-1.5 text-[10px] text-emerald-600 font-bold">
                            <i class="fa-solid fa-check-double"></i> Déjà consulté le {{ $document->viewed_at->format('d/m/Y') }}
                        </span>
                    @endif
                </div>
                
                <div class="flex-grow flex items-center justify-center p-4">
                    @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp']))
                        <img src="{{ Storage::url($document->chemin ?? $document->path) }}" class="max-w-full rounded-2xl shadow-lg border border-slate-100">
                    @elseif(in_array(strtolower($ext), ['pdf', 'xls', 'xlsx', 'html', 'txt']))
                        <iframe src="{{ Storage::url($document->chemin ?? $document->path) }}" class="w-full h-[800px] rounded-xl border-0 bg-slate-50"></iframe>
                    @else
                        <div class="text-center p-20">
                            <div class="w-24 h-24 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-6">
                                <i class="fa-solid fa-eye-slash text-slate-200 text-4xl"></i>
                            </div>
                            <h4 class="text-xl font-black text-slate-800 mb-2">Format de lecture spéciale</h4>
                            <p class="text-slate-400 text-sm max-w-xs mx-auto">Ce document ({{ strtoupper($ext) }}) est archivé dans votre coffre-fort. Vous pouvez le consulter à tout moment en le téléchargeant sur votre appareil.</p>
                            <a href="{{ Storage::url($document->chemin ?? $document->path) }}" download class="mt-8 inline-flex items-center gap-3 px-8 py-4 bg-slate-900 text-white font-black rounded-2xl hover:bg-blue-600 transition-all shadow-xl shadow-slate-200 active:scale-95">
                                <i class="fa-solid fa-download"></i> Ouvrir sur mon appareil
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection