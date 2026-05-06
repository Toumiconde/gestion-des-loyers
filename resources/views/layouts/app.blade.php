<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Billy Condé Immo') }} — @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">

    {{-- NAVBAR --}}
    <nav class="bg-blue-800 text-white px-6 py-4 flex justify-between items-center shadow">
        <div class="font-bold text-xl">
            🏠 Billy Condé Immo
        </div>
        <div class="flex items-center gap-6 text-sm">
            <span>👤 {{ auth()->user()->name }}</span>
            <span class="bg-blue-600 px-2 py-1 rounded text-xs uppercase">
                {{ auth()->user()->role }}
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="hover:underline">Déconnexion</button>
            </form>
        </div>
    </nav>

    <div class="flex min-h-screen">

        {{-- SIDEBAR --}}
        <aside class="w-64 bg-white shadow-md p-4 space-y-1">

            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 text-gray-700 font-medium">
                📊 Dashboard
            </a>

            <div class="text-xs text-gray-400 uppercase px-3 pt-4 pb-1">Gestion</div>

            <a href="{{ route('proprietaires.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 text-gray-700">
                👥 Propriétaires
            </a>
            <a href="{{ route('biens.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 text-gray-700">
                🏘️ Biens
            </a>
            <a href="{{ route('locataires.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 text-gray-700">
                🧑 Locataires
            </a>
            <a href="{{ route('contrats.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 text-gray-700">
                📄 Contrats
            </a>

            <div class="text-xs text-gray-400 uppercase px-3 pt-4 pb-1">Finance</div>

            <a href="{{ route('paiements.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 text-gray-700">
                💰 Paiements
            </a>
            <a href="{{ route('quittances.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 text-gray-700">
                🧾 Quittances
            </a>
            <a href="{{ route('relances.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 text-gray-700">
                🔔 Relances
            </a>

            <div class="text-xs text-gray-400 uppercase px-3 pt-4 pb-1">Suivi</div>

            <a href="{{ route('incidents.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 text-gray-700">
                ⚠️ Incidents
            </a>
            <a href="{{ route('documents.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 text-gray-700">
                📁 Documents
            </a>

            <div class="text-xs text-gray-400 uppercase px-3 pt-4 pb-1">Admin</div>

            <a href="{{ route('parametres.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 text-gray-700">
                ⚙️ Paramètres
            </a>
            <a href="{{ route('activity-logs.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-50 text-gray-700">
                📋 Logs
            </a>

        </aside>

        {{-- CONTENU PRINCIPAL --}}
        <main class="flex-1 p-8">

            {{-- Messages de succès/erreur --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-6">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-6">
                    ❌ {{ session('error') }}
                </div>
            @endif

            {{-- Titre de la page --}}
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">@yield('title')</h1>
                @yield('actions')
            </div>

            {{-- Contenu de chaque page --}}
            @yield('content')

        </main>

    </div>

</body>
</html>