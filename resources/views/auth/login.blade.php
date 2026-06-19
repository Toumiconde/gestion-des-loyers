<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GestLoyer - Connexion Professionnelle</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: #f8fafc;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .input-focus:focus-within {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .gradient-text {
            background: linear-gradient(135deg, #f8fafc 0%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .feature-icon {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }
    </style>
</head>
<body class="antialiased overflow-hidden">

<div class="min-h-screen flex items-center justify-center p-4 lg:p-0">

    <div class="w-full max-w-full lg:h-screen bg-white overflow-hidden grid grid-cols-1 lg:grid-cols-12">

        <!-- LEFT SIDE: FORM (5 cols) -->
        <div class="lg:col-span-5 bg-white px-8 md:px-16 py-10 flex flex-col justify-center relative">
            
            <div class="max-w-md mx-auto w-full animate-fade-in">
                <!-- LOGO & HEADER -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center shadow-lg">
                            <i class="fa-solid fa-building-shield text-blue-400 text-lg"></i>
                        </div>
                        <h1 class="text-xl font-black tracking-tighter text-slate-900">
                            GEST<span class="text-blue-600">LOYER</span>
                        </h1>
                    </div>
                    
                    <h2 class="text-3xl font-black text-slate-900 mb-2 tracking-tight">Ravi de vous revoir</h2>
                    <p class="text-slate-500 text-sm font-medium">Gérez votre patrimoine en toute simplicité.</p>
                </div>


                <!-- LOGIN FORM -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div class="space-y-3">
                        <!-- EMAIL FIELD -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-1">Adresse Email</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-regular fa-envelope text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                                </div>
                                <input type="email" name="email" required
                                    class="block w-full h-12 pl-11 pr-5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all text-sm font-medium"
                                    placeholder="nom@exemple.com">
                            </div>
                            @error('email')
                                <p class="mt-1.5 text-[10px] text-rose-500 font-bold ml-1 italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- PASSWORD FIELD -->
                        <div x-data="{ show: false }">
                            <div class="mb-1.5 ml-1">
                                <label class="text-xs font-bold text-slate-700">Mot de Passe</label>
                            </div>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-lock text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                                </div>
                                <input :type="show ? 'text' : 'password'" name="password" required
                                    class="block w-full h-12 pl-11 pr-12 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all text-sm font-medium"
                                    placeholder="••••••••">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-blue-600 transition-colors">
                                    <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- REMEMBER ME -->
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 cursor-pointer">
                        <label for="remember_me" class="ml-2 text-xs font-semibold text-slate-600 cursor-pointer select-none">Rester connecté</label>
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <button type="submit" 
                        class="w-full h-12 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-base shadow-lg shadow-slate-200 transition-all duration-300 active:scale-95 flex items-center justify-center gap-3">
                        Accéder au Dashboard
                        <i class="fa-solid fa-arrow-right-long text-xs"></i>
                    </button>

                    <!-- OR DIVIDER -->
                    <div class="flex items-center gap-4 py-2">
                        <div class="flex-1 h-px bg-slate-100"></div>
                        <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">OU</span>
                        <div class="flex-1 h-px bg-slate-100"></div>
                    </div>

                    <!-- GOOGLE LOGIN -->
                    <a href="{{ route('auth.google') }}" 
                        class="w-full h-12 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl font-bold transition-all duration-300 flex items-center justify-center gap-3 shadow-sm active:scale-95 group">
                        <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" class="w-5 h-5 group-hover:scale-110 transition-transform">
                        Continuer avec Google
                    </a>


                    <!-- NEW USER INFO -->
                    <div class="pt-3 border-t border-slate-100 mt-2 text-center space-y-2">
                        <p class="text-slate-500 text-[11px] font-medium leading-relaxed">
                            Nouveau sur GESTLOYER ? <br/>
                            <a href="{{ route('register') }}" class="text-blue-600 font-black hover:underline tracking-wide uppercase text-[10px]">
                                Créer un compte gratuitement
                            </a>
                        </p>
                        <p class="text-slate-400 text-[11px] font-medium leading-relaxed">
                            En cas d'oubli de mot de passe, veuillez appeler votre service :<br/>
                            <a href="tel:+224621508200" class="text-blue-600 font-black hover:underline tracking-wide">
                                <i class="fa-solid fa-phone text-[9px] mr-1"></i>+224 621 508 200
                            </a>
                        </p>
                    </div>
                </form>
            </div>

            <!-- FOOTER INFO -->
            <div class="absolute bottom-8 left-0 right-0 text-center">
                <p class="text-[10px] font-bold text-slate-300 uppercase tracking-[3px]">© 2026 GESTLOYER Excellence Immobilière</p>
            </div>
        </div>

        <!-- RIGHT SIDE: EXPERIENCE (7 cols) -->
        <div class="lg:col-span-7 relative hidden lg:flex items-center justify-center overflow-hidden bg-slate-900">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 bg-cover bg-center scale-110 blur-[2px]" 
                 style="background-image: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop');">
            </div>
            <div class="absolute inset-0 bg-gradient-to-br from-slate-950/90 via-slate-900/80 to-blue-900/40"></div>

            <!-- Content Container -->
            <div class="relative z-10 w-full max-w-2xl px-12">
                <div class="glass-card rounded-[50px] p-16 animate-fade-in">
                    <span class="inline-block px-5 py-2 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-[10px] font-black uppercase tracking-[2px] mb-8">
                        Expérience Intuitive
                    </span>
                    
                    <h2 class="text-6xl font-black text-white leading-tight mb-10 tracking-tighter">
                        La gestion locative <br/>
                        <span class="text-blue-400">réinventée.</span>
                    </h2>

                    <!-- Explanatory Features -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                        <div class="flex items-start gap-4 group">
                            <div class="w-12 h-12 rounded-2xl feature-icon flex items-center justify-center border border-white/10 group-hover:bg-blue-500/20 transition-all duration-500">
                                <i class="fa-solid fa-chart-line text-blue-400"></i>
                            </div>
                            <div>
                                <h3 class="text-white font-bold mb-1">Analyse de Performance</h3>
                                <p class="text-slate-400 text-sm leading-relaxed">Suivi des revenus et statistiques en temps réel.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 group">
                            <div class="w-12 h-12 rounded-2xl feature-icon flex items-center justify-center border border-white/10 group-hover:bg-blue-500/20 transition-all duration-500">
                                <i class="fa-solid fa-file-contract text-blue-400"></i>
                            </div>
                            <div>
                                <h3 class="text-white font-bold mb-1">Contrats Automatisés</h3>
                                <p class="text-slate-400 text-sm leading-relaxed">Génération de baux et quittances professionnelles.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 group">
                            <div class="w-12 h-12 rounded-2xl feature-icon flex items-center justify-center border border-white/10 group-hover:bg-blue-500/20 transition-all duration-500">
                                <i class="fa-solid fa-screwdriver-wrench text-blue-400"></i>
                            </div>
                            <div>
                                <h3 class="text-white font-bold mb-1">Suivi Maintenance</h3>
                                <p class="text-slate-400 text-sm leading-relaxed">Gestion centralisée des incidents et réparations.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 group">
                            <div class="w-12 h-12 rounded-2xl feature-icon flex items-center justify-center border border-white/10 group-hover:bg-blue-500/20 transition-all duration-500">
                                <i class="fa-solid fa-shield-halved text-blue-400"></i>
                            </div>
                            <div>
                                <h3 class="text-white font-bold mb-1">Sécurité Garantie</h3>
                                <p class="text-slate-400 text-sm leading-relaxed">Données protégées et accès rôles-basés certifiés.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Interactive Quote/Badge -->
                    <div class="p-6 bg-white/5 rounded-3xl border border-white/10 flex items-center gap-6">
                        <div class="flex -space-x-3">
                            <img class="w-10 h-10 rounded-full border-2 border-slate-900" src="https://i.pravatar.cc/100?u=1" alt="">
                            <img class="w-10 h-10 rounded-full border-2 border-slate-900" src="https://i.pravatar.cc/100?u=2" alt="">
                            <img class="w-10 h-10 rounded-full border-2 border-slate-900" src="https://i.pravatar.cc/100?u=3" alt="">
                        </div>
                        <p class="text-slate-400 text-xs font-medium">
                            Rejoignez plus de <span class="text-white font-bold">120 propriétaires</span> qui nous font confiance.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Decoration Elements -->
            <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-blue-600/20 rounded-full blur-[100px]"></div>
            <div class="absolute -top-20 -right-20 w-80 h-80 bg-indigo-600/20 rounded-full blur-[100px]"></div>
        </div>

    </div>

</div>

</body>
</html>
