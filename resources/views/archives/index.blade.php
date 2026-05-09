@extends('layouts.app')

@section('title', 'Centre d\'Archives & Historique')

@section('content')

<div class="mb-10">
    <h2 class="text-3xl font-black text-slate-800">Centre d'Archives</h2>
    <p class="text-slate-500 font-medium">Gestion centralisée des éléments supprimés et de l'historique du système</p>
</div>

<div x-data="{ tab: 'biens' }" class="space-y-8">
    
    {{-- ONGLETS --}}
    <div class="flex items-center gap-2 p-1 bg-slate-200/50 rounded-2xl w-fit">
        <button @click="tab = 'biens'" :class="tab === 'biens' ? 'bg-white shadow-sm text-blue-600' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition-all">Biens ({{ $biens->count() }})</button>
        <button @click="tab = 'locataires'" :class="tab === 'locataires' ? 'bg-white shadow-sm text-blue-600' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition-all">Locataires ({{ $locataires->count() }})</button>
        <button @click="tab = 'messages'" :class="tab === 'messages' ? 'bg-white shadow-sm text-blue-600' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition-all">Messages ({{ $messages->count() }})</button>
        <button @click="tab = 'contrats'" :class="tab === 'contrats' ? 'bg-white shadow-sm text-blue-600' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition-all">Contrats ({{ $contrats->count() }})</button>
        @if(auth()->user()->isAdmin())
            <button @click="tab = 'proprietaires'" :class="tab === 'proprietaires' ? 'bg-white shadow-sm text-blue-600' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition-all">Bailleurs ({{ $proprietaires->count() }})</button>
        @endif
    </div>

    {{-- CONTENU BIENS --}}
    <div x-show="tab === 'biens'" class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] uppercase font-black text-slate-400 tracking-widest">
                <tr>
                    <th class="px-8 py-5">Bien</th>
                    <th class="px-8 py-5">Supprimé le</th>
                    <th class="px-8 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($biens as $b)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-8 py-5 font-bold text-slate-700">{{ $b->libelle }}</td>
                    <td class="px-8 py-5 text-sm text-slate-400">{{ $b->deleted_at->format('d/m/Y H:i') }}</td>
                    <td class="px-8 py-5 text-right">
                        <form action="{{ route('archives.restore', ['type' => 'bien', 'id' => $b->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-blue-600 font-black text-xs uppercase hover:underline">Restaurer</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-8 py-10 text-center italic text-slate-400 text-sm">Aucun bien archivé</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- CONTENU LOCATAIRES --}}
    <div x-show="tab === 'locataires'" class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] uppercase font-black text-slate-400 tracking-widest">
                <tr>
                    <th class="px-8 py-5">Nom Complet</th>
                    <th class="px-8 py-5">Supprimé le</th>
                    <th class="px-8 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($locataires as $l)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-8 py-5 font-bold text-slate-700">{{ $l->prenom }} {{ $l->nom }}</td>
                    <td class="px-8 py-5 text-sm text-slate-400">{{ $l->deleted_at->format('d/m/Y H:i') }}</td>
                    <td class="px-8 py-5 text-right">
                        <form action="{{ route('archives.restore', ['type' => 'locataire', 'id' => $l->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-blue-600 font-black text-xs uppercase hover:underline">Restaurer</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-8 py-10 text-center italic text-slate-400 text-sm">Aucun locataire archivé</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- CONTENU MESSAGES --}}
    <div x-show="tab === 'messages'" class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] uppercase font-black text-slate-400 tracking-widest">
                <tr>
                    <th class="px-8 py-5">Message</th>
                    <th class="px-8 py-5">Supprimé le</th>
                    <th class="px-8 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($messages as $m)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-8 py-5">
                        <p class="text-sm text-slate-700 italic">"{{ Str::limit($m->content, 60) }}"</p>
                    </td>
                    <td class="px-8 py-5 text-sm text-slate-400">{{ $m->deleted_at->format('d/m/Y H:i') }}</td>
                    <td class="px-8 py-5 text-right">
                        <form action="{{ route('archives.restore', ['type' => 'message', 'id' => $m->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-blue-600 font-black text-xs uppercase hover:underline">Restaurer</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-8 py-10 text-center italic text-slate-400 text-sm">Aucun message archivé</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- CONTENU CONTRATS --}}
    <div x-show="tab === 'contrats'" class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] uppercase font-black text-slate-400 tracking-widest">
                <tr>
                    <th class="px-8 py-5">Référence Contrat</th>
                    <th class="px-8 py-5">Supprimé le</th>
                    <th class="px-8 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($contrats as $c)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-8 py-5 font-bold text-slate-700">{{ $c->numero_contrat }}</td>
                    <td class="px-8 py-5 text-sm text-slate-400">{{ $c->deleted_at->format('d/m/Y H:i') }}</td>
                    <td class="px-8 py-5 text-right">
                        <form action="{{ route('archives.restore', ['type' => 'contrat', 'id' => $c->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-blue-600 font-black text-xs uppercase hover:underline">Restaurer</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-8 py-10 text-center italic text-slate-400 text-sm">Aucun contrat archivé</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection
