<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GESTLOYER - Gestion Immobilière Intelligente</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Outfit', sans-serif; scroll-behavior: smooth; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .text-gradient { background: linear-gradient(135deg, #0f172a 0%, #3b82f6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero-shape { position: absolute; top: -10%; right: -5%; width: 50%; height: 80%; background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.05) 100%); border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; z-index: -1; animation: morph 15s ease-in-out infinite; }
        @keyframes morph { 0% { border-radius: 40% 60% 70% 30% / 40% 40% 60% 50%; } 50% { border-radius: 70% 30% 50% 70% / 50% 60% 30% 60%; } 100% { border-radius: 40% 60% 70% 30% / 40% 40% 60% 50%; } }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 overflow-x-hidden">

    {{-- NAVIGATION --}}
    <nav class="fixed top-0 left-0 w-full z-50 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center glass rounded-[30px] px-8 py-4 shadow-xl shadow-slate-200/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                    <i class="fa-solid fa-house-chimney-window"></i>
                </div>
                <span class="text-xl font-black tracking-tighter text-slate-800">GEST<span class="text-blue-600">LOYER</span></span>
            </div>
            
            <div class="flex items-center gap-6">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-black uppercase tracking-widest text-slate-500 hover:text-blue-600 transition-colors">Tableau de bord</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-black uppercase tracking-widest text-slate-500 hover:text-blue-600 transition-colors">Connexion</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-8 py-3 bg-slate-900 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-300">S'inscrire</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <section class="relative pt-40 pb-24 px-6 overflow-hidden">
        <div class="hero-shape"></div>
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div>
                <span class="inline-block px-4 py-2 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-[3px] mb-6 animate-pulse">Plateforme Immobilière v2.0</span>
                <h1 class="text-6xl lg:text-8xl font-black text-slate-900 leading-[1.1] tracking-tight mb-8">
                    La gestion locative <span class="text-gradient">réinventée.</span>
                </h1>
                <p class="text-xl text-slate-500 leading-relaxed mb-12 max-w-xl">
                    Simplifiez vos encaissements, automatisez vos quittances et suivez vos performances financières en temps réel avec la solution la plus intuitive du marché.
                </p>
                <div class="flex flex-col sm:flex-row gap-6">
                    <a href="{{ route('register') }}" class="px-10 py-5 bg-blue-600 text-white rounded-[25px] font-black text-sm uppercase tracking-widest hover:bg-slate-900 transition-all shadow-2xl shadow-blue-200 text-center">
                        Démarrer l'aventure
                    </a>
                    <a href="#about" class="px-10 py-5 bg-white text-slate-900 border border-slate-200 rounded-[25px] font-black text-sm uppercase tracking-widest hover:border-blue-600 transition-all text-center">
                        En savoir plus
                    </a>
                </div>
                <div class="mt-12 flex items-center gap-4 text-slate-400">
                    <div class="flex -space-x-3">
                        <img src="https://ui-avatars.com/api/?name=JD&background=3b82f6&color=fff" class="w-10 h-10 rounded-full border-4 border-white" alt="">
                        <img src="https://ui-avatars.com/api/?name=SM&background=0f172a&color=fff" class="w-10 h-10 rounded-full border-4 border-white" alt="">
                        <img src="https://ui-avatars.com/api/?name=AL&background=10b981&color=fff" class="w-10 h-10 rounded-full border-4 border-white" alt="">
                    </div>
                    <p class="text-xs font-medium">+500 gestionnaires nous font déjà confiance</p>
                </div>
            </div>
            <div class="relative hidden lg:block">
                <div class="relative z-10 bg-white p-4 rounded-[50px] shadow-[0_50px_100px_-20px_rgba(0,0,0,0.15)] border border-slate-100">
                    <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1073&q=80" class="rounded-[40px] w-full h-[600px] object-cover" alt="Property Management">
                    {{-- Badge flottant --}}
                    <div class="absolute -left-10 bottom-20 glass p-6 rounded-[30px] shadow-2xl animate-bounce">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center text-white text-xl">
                                <i class="fa-solid fa-chart-line"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase">Revenus Encaissés</p>
                                <p class="text-xl font-black text-slate-800">+85% ce mois</p>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Déco --}}
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-blue-600/10 rounded-full blur-3xl"></div>
            </div>
        </div>
    </section>

    {{-- FEATURES SECTION --}}
    <section id="about" class="py-24 px-6 bg-white">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-4xl lg:text-5xl font-black text-slate-900 mb-6">Tout ce dont vous avez besoin <br>pour réussir votre gestion.</h2>
            <p class="text-slate-500 max-w-2xl mx-auto mb-20 leading-relaxed">Une suite d'outils puissants conçus pour les propriétaires exigeants et les locataires qui cherchent la simplicité au quotidien.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- Feature 1 --}}
                <div class="p-12 bg-slate-50 rounded-[50px] hover:bg-blue-600 hover:text-white transition-all group border border-slate-100">
                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-3xl flex items-center justify-center text-2xl mb-8 group-hover:bg-white/20 group-hover:text-white transition-all">
                        <i class="fa-solid fa-bolt-lightning"></i>
                    </div>
                    <h3 class="text-2xl font-black mb-4 text-left">Automatisation Totale</h3>
                    <p class="text-slate-500 group-hover:text-blue-100 leading-relaxed text-left">Générez vos quittances et factures en un clic. Plus besoin de paperasse manuelle interminable.</p>
                </div>

                {{-- Feature 2 --}}
                <div class="p-12 bg-slate-50 rounded-[50px] hover:bg-slate-900 hover:text-white transition-all group border border-slate-100">
                    <div class="w-16 h-16 bg-slate-200 text-slate-800 rounded-3xl flex items-center justify-center text-2xl mb-8 group-hover:bg-white/20 group-hover:text-white transition-all">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="text-2xl font-black mb-4 text-left">Sécurité Bancaire</h3>
                    <p class="text-slate-500 group-hover:text-slate-300 leading-relaxed text-left">Vos données financières et vos transactions sont protégées par les protocoles de sécurité les plus stricts du secteur.</p>
                </div>

                {{-- Feature 3 --}}
                <div class="p-12 bg-slate-50 rounded-[50px] hover:bg-emerald-600 hover:text-white transition-all group border border-slate-100">
                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-3xl flex items-center justify-center text-2xl mb-8 group-hover:bg-white/20 group-hover:text-white transition-all">
                        <i class="fa-solid fa-magnifying-glass-chart"></i>
                    </div>
                    <h3 class="text-2xl font-black mb-4 text-left">Transparence Absolue</h3>
                    <p class="text-slate-500 group-hover:text-emerald-100 leading-relaxed text-left">Suivez l'état de vos biens, les paiements en attente et vos commissions via un dashboard ultra-clair et précis.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- IMPORTANCE SECTION --}}
    <section class="py-24 px-6 bg-slate-900 text-white overflow-hidden relative">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="relative">
                <div class="absolute -left-20 -top-20 w-64 h-64 bg-blue-600/20 rounded-full blur-3xl"></div>
                <h2 class="text-4xl lg:text-6xl font-black leading-tight mb-8">Pourquoi GESTLOYER change la donne ?</h2>
                <div class="space-y-8">
                    <div class="flex gap-6">
                        <div class="text-blue-500 text-3xl shrink-0 mt-1"><i class="fa-solid fa-circle-check"></i></div>
                        <div>
                            <h4 class="text-xl font-bold mb-2">Gain de Temps Massif</h4>
                            <p class="text-slate-400">Réduisez de 70% le temps passé sur la gestion administrative grâce à nos algorithmes d'automatisation intelligente.</p>
                        </div>
                    </div>
                    <div class="flex gap-6">
                        <div class="text-blue-500 text-3xl shrink-0 mt-1"><i class="fa-solid fa-circle-check"></i></div>
                        <div>
                            <h4 class="text-xl font-bold mb-2">Zéro Retard de Paiement</h4>
                            <p class="text-slate-400">Notre système de relance automatique et de suivi en temps réel garantit que chaque franc est collecté à temps.</p>
                        </div>
                    </div>
                    <div class="flex gap-6">
                        <div class="text-blue-500 text-3xl shrink-0 mt-1"><i class="fa-solid fa-circle-check"></i></div>
                        <div>
                            <h4 class="text-xl font-bold mb-2">Valorisation du Patrimoine</h4>
                            <p class="text-slate-400">Une meilleure gestion financière augmente la valeur perçue de vos biens et fidélise vos locataires.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-12 glass rounded-[60px] border-white/5">
                <h3 class="text-3xl font-black mb-8">Prêt à passer au niveau supérieur ?</h3>
                <p class="text-slate-400 mb-10 leading-relaxed italic">"Depuis que j'utilise GESTLOYER, ma gestion locative n'est plus une corvée mais une source de sérénité." - Un utilisateur satisfait.</p>
                <a href="{{ route('register') }}" class="block w-full py-6 bg-white text-slate-900 text-center rounded-[30px] font-black uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all shadow-2xl">
                    Rejoindre GESTLOYER maintenant
                </a>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="py-12 px-6 border-t border-slate-100">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8 text-slate-400">
            <div class="flex items-center gap-3 grayscale opacity-50">
                <div class="w-8 h-8 bg-slate-400 rounded-xl flex items-center justify-center text-white">
                    <i class="fa-solid fa-house-chimney-window"></i>
                </div>
                <span class="text-lg font-black tracking-tighter">GESTLOYER</span>
            </div>
            <p class="text-xs font-bold uppercase tracking-widest">&copy; 2026 GESTLOYER. Conçu pour l'excellence immobilière.</p>
            <div class="flex gap-6">
                <a href="#" class="hover:text-blue-600 transition-colors"><i class="fa-brands fa-twitter text-lg"></i></a>
                <a href="#" class="hover:text-blue-600 transition-colors"><i class="fa-brands fa-linkedin text-lg"></i></a>
            </div>
        </div>
    </footer>

</body>
</html>
