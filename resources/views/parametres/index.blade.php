@extends('layouts.app')

@section('title', 'Paramètres & Centre de Recherche')

@section('content')

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Paramètres & Recherche</h2>
        <p class="text-slate-500 font-medium">Gérez votre agence et effectuez des recherches croisées</p>
    </div>
</div>

{{-- MODULE DE RECHERCHE ET TRI ALPHABÉTIQUE --}}
<div class="mb-10 space-y-6">
    <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 p-8">
        <form action="{{ route('parametres.index') }}" method="GET" class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 text-xl"></i>
            <input type="text" name="search" value="{{ $search }}" 
                   placeholder="Rechercher un locataire, un bien, un document par mot-clé..." 
                   class="w-full bg-slate-50 border-none h-16 pl-16 pr-8 rounded-3xl text-slate-700 font-bold focus:ring-4 focus:ring-blue-100 transition-all outline-none">
            <button type="submit" class="absolute right-3 top-3 h-10 px-6 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all">
                Lancer la recherche
            </button>
        </form>

        {{-- Barre de tri A-Z --}}
        <div class="mt-6 flex flex-wrap items-center justify-center gap-1">
            @foreach(range('A', 'Z') as $char)
                <a href="{{ route('parametres.index', ['letter' => $char]) }}" 
                   class="w-9 h-9 flex items-center justify-center rounded-xl font-black text-xs transition-all 
                   {{ $letter == $char ? 'bg-blue-600 text-white shadow-lg shadow-blue-200 scale-110' : 'bg-slate-50 text-slate-400 hover:bg-slate-200 hover:text-slate-700' }}">
                    {{ $char }}
                </a>
            @endforeach
            <a href="{{ route('parametres.index') }}" class="ml-4 px-4 py-2 bg-slate-100 text-slate-500 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-50 hover:text-rose-600 transition-all">
                Effacer
            </a>
        </div>
    </div>

    @if($search || $letter)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        {{-- Locataires --}}
        <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 p-6">
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-3">
                <i class="fa-solid fa-users text-blue-500"></i> Locataires ({{ count($results['locataires'] ?? []) }})
            </h4>
            <div class="space-y-3">
                @forelse($results['locataires'] ?? [] as $loc)
                    <a href="{{ route('locataires.show', $loc) }}" class="flex items-center gap-3 p-2 hover:bg-slate-50 rounded-2xl transition-all group">
                        <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-black text-[10px]">
                            {{ substr($loc->nom, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 text-xs truncate">{{ $loc->prenom }} {{ $loc->nom }}</p>
                            <p class="text-[9px] text-slate-400">{{ $loc->telephone }}</p>
                        </div>
                    </a>
                @empty
                    <p class="text-[10px] text-slate-400 italic">Aucun trouvé.</p>
                @endforelse
            </div>
        </div>

        {{-- Propriétaires --}}
        <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 p-6">
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-3">
                <i class="fa-solid fa-user-tie text-indigo-500"></i> Propriétaires ({{ count($results['proprietaires'] ?? []) }})
            </h4>
            <div class="space-y-3">
                @forelse($results['proprietaires'] ?? [] as $prop)
                    <a href="{{ route('proprietaires.show', $prop) }}" class="flex items-center gap-3 p-2 hover:bg-slate-50 rounded-2xl transition-all group">
                        <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-black text-[10px]">
                            {{ substr($prop->user->name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 text-xs truncate">{{ $prop->user->name }}</p>
                            <p class="text-[9px] text-slate-400">{{ $prop->adresse_professionnelle ?: 'Professionnel' }}</p>
                        </div>
                    </a>
                @empty
                    <p class="text-[10px] text-slate-400 italic">Aucun trouvé.</p>
                @endforelse
            </div>
        </div>

        {{-- Biens --}}
        <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 p-6">
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-3">
                <i class="fa-solid fa-building text-emerald-500"></i> Biens ({{ count($results['biens'] ?? []) }})
            </h4>
            <div class="space-y-3">
                @forelse($results['biens'] ?? [] as $bien)
                    <a href="{{ route('biens.show', $bien) }}" class="flex items-center gap-3 p-2 hover:bg-slate-50 rounded-2xl transition-all group">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <i class="fa-solid fa-house text-xs"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 text-xs truncate">{{ $bien->libelle }}</p>
                            <p class="text-[9px] text-slate-400 truncate">{{ $bien->ville }}</p>
                        </div>
                    </a>
                @empty
                    <p class="text-[10px] text-slate-400 italic">Aucun trouvé.</p>
                @endforelse
            </div>
        </div>

        {{-- Documents --}}
        <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 p-6">
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-3">
                <i class="fa-solid fa-folder text-amber-500"></i> Documents ({{ count($results['documents'] ?? []) }})
            </h4>
            <div class="space-y-3">
                @forelse($results['documents'] ?? [] as $doc)
                    <a href="{{ route('documents.index') }}" class="flex items-center gap-3 p-2 hover:bg-slate-50 rounded-2xl transition-all group">
                        <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                            <i class="fa-solid fa-file-lines text-xs"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 text-xs truncate">{{ $doc->nom }}</p>
                            <p class="text-[9px] text-slate-400">{{ $doc->created_at->format('d/m/Y') }}</p>
                        </div>
                    </a>
                @empty
                    <p class="text-[10px] text-slate-400 italic">Aucun trouvé.</p>
                @endforelse
            </div>
        </div>

    </div>
    @endif
</div>

<div x-data="{ tab: 'agence' }" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    {{-- Menu latéral paramètres --}}
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 p-6">
            <nav class="space-y-2">
                <button @click="tab = 'agence'" :class="tab === 'agence' ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50'" class="w-full flex items-center gap-3 p-4 rounded-2xl font-black transition-all text-left">
                    <i class="fa-solid fa-building-circle-check"></i> Informations Agence
                </button>
                <button @click="tab = 'notifications'" :class="tab === 'notifications' ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50'" class="w-full flex items-center gap-3 p-4 rounded-2xl font-black transition-all text-left">
                    <i class="fa-solid fa-bell-concierge"></i> Notifications & Alertes
                </button>
                <button @click="tab = 'fiscalite'" :class="tab === 'fiscalite' ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50'" class="w-full flex items-center gap-3 p-4 rounded-2xl font-black transition-all text-left">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Fiscalité & Facturation
                </button>
                <button @click="tab = 'securite'" :class="tab === 'securite' ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50'" class="w-full flex items-center gap-3 p-4 rounded-2xl font-black transition-all text-left">
                    <i class="fa-solid fa-shield-halved"></i> Sécurité & Accès
                </button>
            </nav>
        </div>
    </div>

    {{-- Contenu principal --}}
    <div class="lg:col-span-2">
        
        {{-- ONGLET AGENCE --}}
        <div x-show="tab === 'agence'" class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-xl font-black text-slate-800">Informations Agence</h3>
                <a href="{{ route('parametres.edit') }}" class="px-5 py-2.5 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                    Modifier
                </a>
            </div>
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Nom de l'Agence</p>
                        <p class="font-bold text-slate-800">{{ $settings['nom_agence'] ?? 'GESTLOYER Immobilier' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Email de contact</p>
                        <p class="font-bold text-slate-800">{{ $settings['email_contact'] ?? 'contact@gestloyer.com' }}</p>
                    </div>
                </div>
                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Adresse</p>
                        <p class="font-bold text-slate-800">{{ $settings['adresse'] ?? 'Conakry, Guinée' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Téléphone</p>
                        <p class="font-bold text-slate-800">{{ $settings['telephone'] ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- (Autres onglets simplifiés ici pour brièveté, ils restent fonctionnels avec le x-show) --}}
        <div x-show="tab === 'notifications'" class="bg-white rounded-[40px] shadow-sm border border-slate-100 p-8">
            <h3 class="text-xl font-black text-slate-800 mb-6">Alertes & Rappels</h3>
            <p class="text-slate-500">Gérez vos seuils de retard et vos notifications automatiques dans l'onglet édition.</p>
        </div>
        
        <div x-show="tab === 'fiscalite'" class="bg-white rounded-[40px] shadow-sm border border-slate-100 p-8">
            <h3 class="text-xl font-black text-slate-800 mb-6">Fiscalité & Facturation</h3>
            <p class="text-slate-500">Configurez vos taux de TVA et formats de quittance.</p>
        </div>

        <div x-show="tab === 'securite'" class="bg-white rounded-[40px] shadow-sm border border-slate-100 p-8">
            <h3 class="text-xl font-black text-slate-800 mb-6">Sécurité & Accès</h3>
            <p class="text-slate-500">Paramétrez les délais d'expiration de session et les accès restreints.</p>
        </div>

    </div>
</div>

@endsection