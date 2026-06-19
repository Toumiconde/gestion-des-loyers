<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GestLoyer - @yield('title', 'Dashboard')</title>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f1f5f9; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #1e3a8a; border-radius: 20px; }
        [x-cloak] { display: none !important; }
    </style>

    <script>
        // Mémoriser la position du scroll de la sidebar
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.querySelector('.sidebar-scroll');
            if (sidebar) {
                // Restaurer la position
                const scrollPos = localStorage.getItem('sidebar-scroll-pos');
                if (scrollPos) {
                    sidebar.scrollTop = scrollPos;
                }

                // Sauvegarder la position lors du clic ou du scroll
                sidebar.addEventListener('scroll', () => {
                    localStorage.setItem('sidebar-scroll-pos', sidebar.scrollTop);
                });
                
                // Sauvegarder aussi juste avant de quitter la page
                window.addEventListener('beforeunload', () => {
                    localStorage.setItem('sidebar-scroll-pos', sidebar.scrollTop);
                });
            }
        });
    </script>
</head>

<body class="overflow-hidden bg-slate-50">
<div class="flex h-screen">

    {{-- SIDEBAR --}}
    <aside class="w-[280px] bg-[#02132D] text-white flex flex-col shrink-0">

        {{-- LOGO --}}
        <div class="p-8 border-b border-white/10">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-white flex items-center justify-center shadow-xl overflow-hidden">
                    @php $logo = \App\Models\Parametre::where('cle', 'logo')->value('valeur'); @endphp
                    @if($logo)
                        <img src="{{ Storage::url($logo) }}" class="w-full h-full object-contain p-2">
                    @else
                        <i class="fa-solid fa-building text-3xl text-[#D6A85F]"></i>
                    @endif
                </div>
                <div>
                    <h1 class="text-xl font-black tracking-wider leading-tight">
                        {{ \App\Models\Parametre::where('cle', 'nom_agence')->value('valeur') ?? 'GESTLOYER' }}
                    </h1>
                    <p class="text-slate-400 text-[10px] uppercase font-black tracking-tighter mt-1">Gestion Immobilière</p>
                </div>
            </div>
        </div>

        {{-- MENU --}}
        <div class="flex-1 overflow-y-auto sidebar-scroll px-5 py-6">

            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-4 px-5 py-4 rounded-2xl transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-blue-700 shadow-lg' : 'hover:bg-white/10' }}">
                <i class="fa-solid fa-table-columns text-lg {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-300' }}"></i>
                <span class="font-semibold {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-200' }}">
                    Tableau de bord
                </span>
            </a>
            
            {{-- Recherche de Logement (Locataire) --}}
            @if(auth()->user()->role === 'locataire')
            <a href="{{ route('recherche.index') }}"
               class="flex items-center gap-4 px-5 py-4 mt-2 rounded-2xl transition-all duration-300 {{ request()->routeIs('recherche.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-white/10' }}">
                <i class="fa-solid fa-search text-lg {{ request()->routeIs('recherche.*') ? 'text-white' : 'text-slate-300' }}"></i>
                <span class="font-semibold {{ request()->routeIs('recherche.*') ? 'text-white' : 'text-slate-200' }}">
                    Rechercher un logement
                </span>
            </a>
            @endif

            {{-- Diffusion de Masse (Admin/Gestionnaire seulement) --}}
            @if(in_array(auth()->user()->role, ['admin', 'gestionnaire']))
            <a href="{{ route('broadcast.index') }}"
               class="flex items-center gap-4 px-5 py-4 mt-2 rounded-2xl transition-all duration-300 {{ request()->routeIs('broadcast.*') ? 'bg-indigo-600 shadow-lg' : 'hover:bg-white/10' }}">
                <i class="fa-solid fa-tower-broadcast text-lg {{ request()->routeIs('broadcast.*') ? 'text-white' : 'text-slate-300' }}"></i>
                <span class="font-semibold {{ request()->routeIs('broadcast.*') ? 'text-white' : 'text-slate-200' }}">
                    Diffusion de masse
                </span>
            </a>
            @endif

            {{-- Support (Admin Only) --}}
            @if(auth()->user()->role === 'admin')
            @php
                $supportUnread = \App\Models\Message::where('is_support', true)
                    ->where('is_read', false)
                    ->where('receiver_id', auth()->id())
                    ->count();
            @endphp
            <a href="{{ route('messages.index', ['filter' => 'support']) }}"
               class="flex items-center justify-between px-5 py-3 mt-2 rounded-2xl transition-all duration-300 {{ request('filter') === 'support' ? 'bg-indigo-600 shadow-lg' : 'hover:bg-white/10' }}">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <i class="fa-solid fa-headset text-base {{ request('filter') === 'support' ? 'text-white' : 'text-indigo-400' }}"></i>
                        @if($supportUnread > 0)
                            <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-rose-500 border-2 border-[#02132D] rounded-full"></span>
                        @endif
                    </div>
                    <span class="text-sm {{ request('filter') === 'support' ? 'text-white font-semibold' : 'text-slate-300' }}">Support Technique</span>
                </div>
            </a>
            @endif

            @php
                $unreadMessages = \App\Models\Message::where('receiver_id', auth()->id())
                    ->where('is_read', false)
                    ->where('is_support', false)
                    ->count();
            @endphp
            <a href="{{ route('messages.index') }}"
               class="flex items-center justify-between px-5 py-3 rounded-2xl transition-all duration-300 {{ request()->routeIs('messages.*') && request('filter') !== 'support' ? 'bg-blue-700 shadow-lg' : 'hover:bg-white/10' }}">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <i class="fa-solid fa-comment-dots text-base {{ request()->routeIs('messages.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        @if($unreadMessages > 0)
                            <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-rose-500 border-2 border-[#02132D] rounded-full"></span>
                        @endif
                    </div>
                    <span class="text-sm {{ request()->routeIs('messages.*') ? 'text-white font-semibold' : 'text-slate-300' }}">Messages</span>
                </div>
            </a>

            {{-- ===== GESTION ===== --}}
            <div class="mt-6 mb-2 px-2">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">
                    ── Gestion
                </p>
            </div>

            @if(in_array(auth()->user()->role, ['admin', 'gestionnaire']))
            <a href="{{ route('archives.index') }}"
               class="flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 {{ request()->routeIs('archives.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-white/10' }}">
                <i class="fa-solid fa-box-archive text-base {{ request()->routeIs('archives.*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span class="text-sm {{ request()->routeIs('archives.*') ? 'text-white font-semibold' : 'text-slate-300' }}">Centre d'Archives</span>
            </a>
            @endif

            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'gestionnaire', 'comptable']))
            <a href="{{ route('proprietaires.index') }}"
               class="flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 {{ request()->routeIs('proprietaires.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-white/10' }}">
                <i class="fa-solid fa-user-tie text-base {{ request()->routeIs('proprietaires.*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span class="text-sm {{ request()->routeIs('proprietaires.*') ? 'text-white font-semibold' : 'text-slate-300' }}">Propriétaires</span>
            </a>
            @endif

            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'gestionnaire', 'proprietaire', 'comptable']))
            <a href="{{ route('biens.index') }}"
               class="flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 {{ request()->routeIs('biens.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-white/10' }}">
                <i class="fa-solid fa-building text-base {{ request()->routeIs('biens.*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span class="text-sm {{ request()->routeIs('biens.*') ? 'text-white font-semibold' : 'text-slate-300' }}">Biens immobiliers</span>
            </a>

            <a href="{{ route('locataires.index') }}"
               class="flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 {{ request()->routeIs('locataires.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-white/10' }}">
                <i class="fa-solid fa-users text-base {{ request()->routeIs('locataires.*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span class="text-sm {{ request()->routeIs('locataires.*') ? 'text-white font-semibold' : 'text-slate-300' }}">Locataires</span>
            </a>
            @endif

            @if(in_array(auth()->user()->role, ['admin', 'gestionnaire', 'comptable']))
            <a href="{{ route('contrats.index') }}"
               class="flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 {{ request()->routeIs('contrats.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-white/10' }}">
                <i class="fa-solid fa-file-signature text-base {{ request()->routeIs('contrats.*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span class="text-sm {{ request()->routeIs('contrats.*') ? 'text-white font-semibold' : 'text-slate-300' }}">Contrats</span>
            </a>
            @endif

            {{-- Demandes de location (Admin/Gestionnaire ou Locataire pour ses propres demandes) --}}
            @if(in_array(auth()->user()->role, ['admin', 'gestionnaire', 'locataire', 'proprietaire']))
            @php
                $demandesUnread = 0;
                if(in_array(auth()->user()->role, ['admin', 'gestionnaire'])) {
                    $demandesUnread = \App\Models\DemandeLocation::where('is_new', true)->count();
                } elseif(auth()->user()->role === 'proprietaire') {
                    $demandesUnread = \App\Models\DemandeLocation::where('is_new', true)
                        ->whereHas('uniteLocative.bien', function($q) {
                            $q->where('proprietaire_id', auth()->user()->proprietaire->id);
                        })->count();
                }
            @endphp
            <a href="{{ route('demandes-location.index') }}"
               class="flex items-center justify-between px-5 py-3 mt-2 rounded-2xl transition-all duration-300 {{ request()->routeIs('demandes-location.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-white/10' }}">
                <div class="flex items-center gap-4 relative">
                    <i class="fa-solid fa-paper-plane text-base {{ request()->routeIs('demandes-location.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    @if($demandesUnread > 0)
                        <span class="absolute -top-1 -left-1 w-2.5 h-2.5 bg-rose-500 border-2 border-[#02132D] rounded-full"></span>
                    @endif
                    <span class="text-sm {{ request()->routeIs('demandes-location.*') ? 'text-white font-semibold' : 'text-slate-300' }}">
                        @if(auth()->user()->role === 'locataire') Mes Demandes @else Demandes de location @endif
                    </span>
                </div>
                @if($demandesUnread > 0)
                    <span class="bg-rose-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full">{{ $demandesUnread }}</span>
                @endif
            </a>
            @endif

            @if(in_array(auth()->user()->role, ['admin', 'gestionnaire', 'proprietaire', 'comptable', 'locataire']))
            {{-- ===== FINANCE ===== --}}
            <div class="mt-6 mb-2 px-2">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">
                    ── Finance
                </p>
            </div>
            @endif

            @if(in_array(auth()->user()->role, ['admin', 'gestionnaire', 'proprietaire', 'comptable', 'locataire']))
            <a href="{{ route('paiements.index') }}"
               class="flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 {{ request()->routeIs('paiements.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-white/10' }}">
                <i class="fa-solid fa-money-bill-wave text-base {{ request()->routeIs('paiements.*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span class="text-sm {{ request()->routeIs('paiements.*') ? 'text-white font-semibold' : 'text-slate-300' }}">
                    @if(auth()->user()->role === 'locataire') Mes Paiements @else Paiements @endif
                </span>
            </a>

            <a href="{{ route('quittances.index') }}"
               class="flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 {{ request()->routeIs('quittances.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-white/10' }}">
                <i class="fa-solid fa-receipt text-base {{ request()->routeIs('quittances.*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span class="text-sm {{ request()->routeIs('quittances.*') ? 'text-white font-semibold' : 'text-slate-300' }}">
                    @if(auth()->user()->role === 'locataire') Reçus de mes paiements @else Quittances @endif
                </span>
            </a>

            @if(in_array(auth()->user()->role, ['admin', 'comptable', 'proprietaire']))
            <a href="{{ route('reversements.index') }}"
               class="flex items-center gap-4 px-5 py-4 mt-1 rounded-2xl transition-all duration-300 {{ request()->routeIs('reversements.*') ? 'bg-emerald-600 shadow-lg' : 'hover:bg-white/10' }}">
                <i class="fa-solid fa-hand-holding-dollar text-lg {{ request()->routeIs('reversements.*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span class="text-sm {{ request()->routeIs('reversements.*') ? 'text-white font-semibold' : 'text-slate-300' }}">
                    @if(auth()->user()->role === 'proprietaire') Mes Reversements @else Reversements Proprio @endif
                </span>
            </a>
            @endif
            @endif

            @if(in_array(auth()->user()->role, ['admin', 'gestionnaire', 'comptable']))
            <a href="{{ route('relances.index') }}"
               class="flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 {{ request()->routeIs('relances.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-white/10' }}">
                <i class="fa-solid fa-bell text-base {{ request()->routeIs('relances.*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span class="text-sm {{ request()->routeIs('relances.*') ? 'text-white font-semibold' : 'text-slate-300' }}">Relances</span>
            </a>

            <a href="{{ route('depenses.index') }}"
               class="flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 {{ request()->routeIs('depenses.*') ? 'bg-rose-600 shadow-lg' : 'hover:bg-white/10' }}">
                <i class="fa-solid fa-wallet text-base {{ request()->routeIs('depenses.*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span class="text-sm {{ request()->routeIs('depenses.*') ? 'text-white font-semibold' : 'text-slate-300' }}">Charges & Dépenses</span>
            </a>
            @endif

            {{-- ===== EVOLUTION (FEEDBACK) ===== --}}
            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'proprietaire']))
            <div class="mt-6 mb-2 px-2">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">
                    ── Évolution
                </p>
            </div>

            <a href="{{ route('feedbacks.index') }}"
               class="flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 {{ request()->routeIs('feedbacks.*') ? 'bg-amber-600 shadow-lg shadow-amber-900/20' : 'hover:bg-white/10' }}">
                <i class="fa-solid fa-flask-vial text-base {{ request()->routeIs('feedbacks.*') ? 'text-white' : 'text-amber-400' }}"></i>
                <span class="text-sm {{ request()->routeIs('feedbacks.*') ? 'text-white font-semibold' : 'text-slate-300' }}">
                    @if(auth()->user()->role === 'admin') Laboratoire d'Évolution @else Critiques & Évolutions @endif
                </span>
            </a>
            @endif

            {{-- ===== SUIVI ===== --}}
            <div class="mt-6 mb-2 px-2">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">
                    ── Suivi
                </p>
            </div>

            @if(auth()->user()->role !== 'comptable')
            @php
                $newIncidentsCount = (auth()->user()->isAdmin() || auth()->user()->isGestionnaire())
                    ? \App\Models\Incident::where('is_new', true)->count()
                    : 0;
            @endphp
            <a href="{{ route('incidents.index') }}"
               class="flex items-center justify-between px-5 py-3 rounded-2xl transition-all duration-300 {{ request()->routeIs('incidents.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-white/10' }}">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <i class="fa-solid fa-triangle-exclamation text-base {{ request()->routeIs('incidents.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        @if($newIncidentsCount > 0)
                            <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-rose-500 border-2 border-[#02132D] rounded-full animate-pulse"></span>
                        @endif
                    </div>
                    <span class="text-sm {{ request()->routeIs('incidents.*') ? 'text-white font-semibold' : 'text-slate-300' }}">Incidents</span>
                </div>
                @if($newIncidentsCount > 0)
                    <span class="bg-rose-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full">{{ $newIncidentsCount }}</span>
                @endif
            </a>
            @endif

            <a href="{{ route('documents.index') }}"
               class="flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 {{ request()->routeIs('documents.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-white/10' }}">
                <i class="fa-solid fa-folder text-base {{ request()->routeIs('documents.*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span class="text-sm {{ request()->routeIs('documents.*') ? 'text-white font-semibold' : 'text-slate-300' }}">Documents</span>
            </a>

            {{-- ===== ADMINISTRATION ===== --}}
            @if(auth()->check() && auth()->user()->role === 'admin')
            <div class="mt-6 mb-2 px-2">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">
                    ── Administration
                </p>
            </div>

            <a href="{{ route('parametres.index') }}"
               class="flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 {{ request()->routeIs('parametres.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-white/10' }}">
                <i class="fa-solid fa-gear text-base {{ request()->routeIs('parametres.*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span class="text-sm {{ request()->routeIs('parametres.*') ? 'text-white font-semibold' : 'text-slate-300' }}">Paramètres</span>
            </a>

            <a href="{{ route('activity-logs.index') }}"
               class="flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 {{ request()->routeIs('activity-logs.*') ? 'bg-blue-700 shadow-lg' : 'hover:bg-white/10' }}">
                <i class="fa-solid fa-list text-base {{ request()->routeIs('activity-logs.*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span class="text-sm {{ request()->routeIs('activity-logs.*') ? 'text-white font-semibold' : 'text-slate-300' }}">Logs d'activité</span>
            </a>

            <a href="{{ route('staff.index') }}"
               class="flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 {{ request()->routeIs('staff.*') ? 'bg-indigo-600 shadow-lg' : 'hover:bg-white/10' }}">
                <i class="fa-solid fa-users-gear text-base {{ request()->routeIs('staff.*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span class="text-sm {{ request()->routeIs('staff.*') ? 'text-white font-semibold' : 'text-slate-300' }}">Collaborateurs</span>
            </a>

@endif

        </div>

        {{-- FOOTER SIDEBAR --}}
        @if(!in_array(auth()->user()->role, ['proprietaire', 'locataire']))
        <div class="p-5 border-t border-white/10">
            <a href="{{ route('help.index') }}" class="block bg-white/5 rounded-2xl p-5 hover:bg-white/10 transition-all group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg shadow-blue-500/20">
                        <i class="fa-solid fa-headset text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-white">Besoin d'aide ?</h3>
                        <p class="text-slate-300 text-sm">Consulter le centre d'aide</p>
                    </div>
                </div>
            </a>
        </div>
        @endif

    </aside>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 flex flex-col overflow-hidden bg-[#f1f5f9]">

        {{-- HEADER --}}
        <header class="flex-none px-8 py-6 flex items-center justify-between bg-[#f1f5f9] z-10">
            <div>
                <h2 class="text-4xl font-black text-slate-800">
                    @yield('title', 'Dashboard')
                </h2>
                @if(request()->routeIs('dashboard'))
                    <p class="text-slate-500 mt-2">
                        Bienvenue, {{ Auth::user()->name ?? 'Utilisateur' }}
                    </p>
                @endif
            </div>

            {{-- RIGHT HEADER --}}
            <div class="flex items-center gap-5">

                {{-- NOTIF --}}
                <div x-data="{ 
                    open: false,
                    notifCount: {{ auth()->user()->unreadNotifications->count() }},
                    markAsRead() {
                        if (this.notifCount > 0) {
                            fetch('{{ route('notifications.markAsRead') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            }).then(() => {
                                this.notifCount = 0;
                            });
                        }
                    }
                }" class="relative">
                    <button @click="open = !open; if(open) markAsRead()" class="w-14 h-14 rounded-2xl bg-white shadow-sm relative flex items-center justify-center focus:outline-none hover:shadow-md transition-shadow">
                        <i class="fa-regular fa-bell text-lg text-slate-600"></i>
                        <template x-if="notifCount > 0">
                            <div class="absolute top-2 right-2 w-5 h-5 rounded-full bg-rose-500 text-white text-[10px] flex items-center justify-center font-black animate-bounce shadow-lg shadow-rose-200">
                                <span x-text="notifCount"></span>
                            </div>
                        </template>
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak
                         class="absolute right-0 mt-2 w-80 bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden z-50">
                        <div class="p-5 border-b border-slate-50 bg-slate-50/50 flex items-center justify-between">
                            <h4 class="font-black text-slate-800 text-sm">Notifications</h4>
                            <template x-if="notifCount > 0">
                                <button @click="markAsRead()" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline cursor-pointer">
                                    Tout marquer comme lu
                                </button>
                            </template>
                            <template x-if="notifCount == 0">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Aucune nouvelle</span>
                            </template>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            @forelse(auth()->user()->unreadNotifications as $notification)
                                <div class="p-4 border-b border-slate-50 hover:bg-slate-50 transition-colors">
                                    <p class="text-xs text-slate-700 leading-snug">
                                        {!! $notification->data['message'] ?? 'Nouvelle notification' !!}
                                    </p>
                                    <span class="text-[10px] text-slate-400 font-bold mt-2 block">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                            @empty
                                <div class="p-8 text-center">
                                    <i class="fa-solid fa-bell-slash text-slate-200 text-2xl mb-2"></i>
                                    <p class="text-xs text-slate-400">Aucune nouvelle notification</p>
                                </div>
                            @endforelse
                        </div>
                        <a href="{{ route('notifications.index') }}" class="block p-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest hover:bg-slate-50">Tout voir</a>
                    </div>
                </div>

                {{-- PROFILE DROPDOWN --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="bg-white px-4 py-3 rounded-2xl flex items-center gap-4 shadow-sm focus:outline-none hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xl">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                        <div class="text-left hidden sm:block">
                            <h3 class="font-bold text-slate-800">{{ Auth::user()->name ?? 'Utilisateur' }}</h3>
                            <p class="text-sm text-slate-500 capitalize">{{ Auth::user()->role ?? 'Utilisateur' }}</p>
                        </div>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-sm ml-2"></i>
                    </button>

                    {{-- DROPDOWN --}}
                    <div x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50" x-cloak>

                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors">
                            <i class="fa-solid fa-user mr-2"></i> Mon Profil
                        </a>

                        <div class="border-t border-slate-100 my-1"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 transition-colors">
                                <i class="fa-solid fa-right-from-bracket mr-2"></i> Se déconnecter
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <div class="flex-1 overflow-y-auto px-8 pb-8">
            @include('partials.alerts')

            @yield('content')
        </div>

    </main>

</div>
    {{-- ================================================================= --}}
    {{-- ASSISTANCE IA GLOBALE : ONBOARDING & COMPLÉTION                 --}}
    {{-- ================================================================= --}}
    @auth
        @php
            $user = auth()->user();
            $missingInfo = false;
            if($user->role === 'proprietaire') {
                $p = $user->proprietaire;
                $missingInfo = (!$p || empty($p->telephone) || empty($p->adresse));
            } elseif($user->role === 'locataire') {
                $l = $user->locataire;
                $missingInfo = (!$l || empty($l->telephone) || empty($l->adresse));
            }
        @endphp

        @if($missingInfo)
        <div x-data="{ 
                show: false, 
                init() {
                    setTimeout(() => {
                        this.show = true;
                    }, 10000); // 10 secondes pour laisser l'utilisateur découvrir la page
                }
            }" 
            x-show="show"
            x-cloak
            x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0 scale-90 blur-lg"
            x-transition:enter-end="opacity-100 scale-100 blur-0"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/80 backdrop-blur-md p-4">
            
            <div class="bg-white rounded-[50px] shadow-2xl border border-white w-full max-w-2xl overflow-hidden animate-fade-in">
                <div class="grid grid-cols-1 md:grid-cols-5 h-full">
                    <!-- Sidebar IA -->
                    <div class="md:col-span-2 bg-slate-900 p-10 text-white flex flex-col justify-between relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-full opacity-10">
                            <i class="fa-solid fa-robot text-[150px] -ml-10 -mt-10"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center mb-6 shadow-lg shadow-blue-500/20">
                                <i class="fa-solid fa-robot text-2xl"></i>
                            </div>
                            <h3 class="text-2xl font-black mb-4">Finalisation <br><span class="text-blue-400">du Profil</span></h3>
                            <p class="text-[11px] text-slate-400 leading-relaxed font-medium">
                                Bonjour {{ explode(' ', $user->name)[0] }}, je suis votre assistant. Votre inscription est presque terminée. Pour activer toutes les fonctionnalités de GESTLOYER, j'ai besoin de vos informations complètes.
                            </p>
                        </div>
                        <div class="relative z-10 pt-10">
                            <div class="p-4 bg-white/5 rounded-2xl border border-white/10">
                                <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-2">Sécurité</p>
                                <p class="text-[9px] text-slate-400 leading-tight">Vos données sont protégées par le protocole AES-256 et ne sont jamais partagées.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire Total -->
                    <div class="md:col-span-3 p-10 bg-white overflow-y-auto max-h-[85vh]">
                        <form action="{{ route('profile.complete') }}" method="POST" class="space-y-6">
                            @csrf
                            <div class="space-y-4">
                                <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">Informations Personnelles</h4>
                                
                                <div class="grid grid-cols-1 gap-4" x-data="{ 
                                    countryCode: '',
                                    updateCode(e) {
                                        this.countryCode = e.target.value;
                                    }
                                }">
                                    <div>
                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Numéro de Téléphone</label>
                                        <div class="flex gap-2">
                                            <select @change="updateCode($event)" class="w-32 h-12 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all cursor-pointer">
                                                <option value="">Pays</option>
                                                <option value="+224">🇬🇳 Guinée (+224)</option>
                                                <option value="+221">🇸🇳 Sénégal (+221)</option>
                                                <option value="+223">🇲🇱 Mali (+223)</option>
                                                <option value="+225">🇨🇮 Côte d'I. (+225)</option>
                                                <option value="+33">🇫🇷 France (+33)</option>
                                            </select>
                                            <div class="relative flex-1">
                                                <i class="fa-solid fa-phone absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                                                <input type="text" name="telephone" required x-model="countryCode" placeholder="6XX XX XX XX" 
                                                    class="w-full h-12 pl-12 pr-6 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Adresse Complète</label>
                                        <input type="text" name="adresse" required placeholder="Quartier, Commune, Ville" 
                                            class="w-full h-12 px-5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                                    </div>

                                    @if($user->role === 'proprietaire')
                                    <div>
                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">RIB Bancaire (Facultatif)</label>
                                        <div class="relative">
                                            <i class="fa-solid fa-building-columns absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                                            <input type="text" name="rib_bancaire" placeholder="FR76 XXXX XXXX XXXX XXXX XXXX" 
                                                class="w-full h-12 pl-12 pr-6 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-4 pt-4" x-data="{ openTerms: false }">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider">Engagement & CGU</h4>
                                    <button type="button" @click="openTerms = !openTerms" class="text-[10px] font-black text-blue-600 flex items-center gap-2 hover:bg-blue-50 px-3 py-1 rounded-full transition-all">
                                        Lire les conditions
                                        <i class="fa-solid fa-chevron-down transition-transform duration-300" :class="openTerms ? 'rotate-180' : ''"></i>
                                    </button>
                                </div>

                                <div x-show="openTerms" x-collapse class="bg-slate-50 rounded-2xl p-6 border border-slate-100 text-[10px] text-slate-500 leading-relaxed max-h-40 overflow-y-auto font-medium">
                                    <p class="font-bold text-slate-700 mb-2 underline">Conditions Générales d'Utilisation (CGU)</p>
                                    <p>1. Exactitude des données : L'utilisateur s'engage à fournir des informations véridiques.</p>
                                    <p class="mt-2">2. Confidentialité : GESTLOYER protège vos données personnelles.</p>
                                    <p class="mt-2">3. Responsabilité : L'agence se réserve le droit de suspendre tout compte frauduleux.</p>
                                    <p class="mt-2">4. Suppression : Le refus des conditions entraîne la suppression définitive du compte.</p>
                                </div>

                                <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-2xl border border-blue-100">
                                    <input type="checkbox" required class="mt-1 rounded text-blue-600 focus:ring-blue-500">
                                    <p class="text-[10px] text-blue-800 leading-tight font-medium">
                                        Je certifie l'exactitude de ces informations et j'accepte les CGU.
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-col gap-3 pt-6">
                                <button type="submit" class="w-full h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-xs uppercase tracking-widest transition-all">
                                    Valider mon inscription
                                </button>
                                <button type="button" @click="if(confirm('🚨 ATTENTION : En quittant, votre compte sera immédiatement supprimé. Continuer ?')) { document.getElementById('abort-form').submit(); }" 
                                    class="w-full h-14 bg-white border-2 border-slate-100 text-slate-400 hover:text-rose-600 hover:border-rose-100 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all">
                                    Quitter et supprimer mon compte
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <form id="abort-form" action="{{ route('profile.abort') }}" method="POST" style="display: none;">
            @csrf
        </form>
        @endif
    @endauth

    @stack('scripts')
</body>
</html>