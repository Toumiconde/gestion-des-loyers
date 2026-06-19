@extends('layouts.app')

@section('title', 'Nouveau Propriétaire')

@section('content')

<div class="max-w-4xl mx-auto py-8">
    {{-- Fil d'ariane --}}
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm font-medium">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-blue-600 transition-colors">
                    <i class="fa-solid fa-house mr-2"></i> Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-slate-300 mx-2 text-xs"></i>
                    <a href="{{ route('proprietaires.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">Propriétaires</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-slate-300 mx-2 text-xs"></i>
                    <span class="text-slate-600">Ajouter un nouveau</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        {{-- En-tête du formulaire --}}
        <div class="bg-[#02132D] p-8 text-white relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-500/10 rounded-full"></div>
            <div class="relative z-10">
                <h2 class="text-3xl font-black mb-2">Créer un propriétaire</h2>
                <p class="text-slate-400">Remplissez les informations pour enregistrer un nouveau bailleur dans le système.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('proprietaires.store') }}" class="p-8" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                {{-- Section 1 : Identité --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-id-card text-blue-600"></i>
                        <h3 class="font-black text-slate-800 uppercase tracking-widest text-sm">Identité & Accès</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Nom complet</label>
                        <div class="relative">
                            <input type="text" name="name" value="{{ old('name') }}"
                                   placeholder="Ex: Mamadou Diallo"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                   required>
                            <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                        @error('name') <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Adresse Email</label>
                        <div class="relative">
                            <input type="email" name="email" value="{{ old('email') }}"
                                   placeholder="Ex: mamadou@email.com"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                   required>
                            <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                        @error('email') <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Mot de passe provisoire</label>
                        <div class="relative">
                            <input type="password" name="password"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                   required>
                            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1 italic">Le propriétaire pourra changer ce mot de passe plus tard.</p>
                        @error('password') <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Section 2 : Coordonnées --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-location-dot text-emerald-600"></i>
                        <h3 class="font-black text-slate-800 uppercase tracking-widest text-sm">Coordonnées & Banque</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Téléphone</label>
                        <div class="relative">
                            <input type="text" name="telephone" value="{{ old('telephone') }}"
                                   placeholder="Ex: +224 622 00 00 00"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                            <i class="fa-solid fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Adresse de résidence</label>
                        <div class="relative">
                            <textarea name="adresse" rows="1"
                                      placeholder="Ex: Kaloum, Conakry"
                                      class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">{{ old('adresse') }}</textarea>
                            <i class="fa-solid fa-map-location-dot absolute left-4 top-4 text-slate-400"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Commission Agence (%)</label>
                        <div class="relative">
                            <input type="number" name="commission_rate" value="{{ old('commission_rate', 10) }}"
                                   step="0.01" min="0" max="100"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
                                   required>
                            <i class="fa-solid fa-percent absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1 italic">Pourcentage prélevé sur les revenus mensuels.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">RIB Bancaire (facultatif)</label>
                        <div class="relative">
                            <input type="text" name="rib_bancaire" value="{{ old('rib_bancaire') }}"
                                   placeholder="GN00 0000 0000 0000"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                            <i class="fa-solid fa-building-columns absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>
                </div>

                {{-- Signature Pad pour Création --}}
                <div class="col-span-full mt-8 bg-slate-50 p-8 rounded-3xl border border-slate-100" x-data="signaturePad()">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-200">
                                <i class="fa-solid fa-signature text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-800">Signature Digitale</h3>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-0.5">Pour authentifier les reçus dès maintenant</p>
                            </div>
                        </div>
                        <button type="button" @click="openPad = !openPad" 
                                class="px-6 py-3 bg-white text-blue-600 border-2 border-blue-600 font-black rounded-xl hover:bg-blue-600 hover:text-white transition-all flex items-center gap-2">
                            <i class="fa-solid fa-pen-nib"></i>
                            <span x-text="openPad ? 'Fermer le pad' : 'Signer sur l\'écran'"></span>
                        </button>
                    </div>

                    <div x-show="openPad" x-transition class="space-y-4">
                        <div class="bg-white border-2 border-dashed border-blue-200 rounded-3xl p-6 relative">
                            <canvas id="signature-canvas" class="w-full h-48 bg-slate-50 rounded-2xl cursor-crosshair border border-slate-100"></canvas>
                            <button type="button" @click="clearPad()" class="absolute top-10 right-10 w-10 h-10 rounded-xl bg-slate-100 text-slate-500 hover:bg-rose-50 hover:text-rose-600 transition-all flex items-center justify-center">
                                <i class="fa-solid fa-eraser"></i>
                            </button>
                        </div>
                        <p class="text-xs text-center text-slate-400 font-bold italic uppercase tracking-widest">Utilisez votre souris ou votre doigt pour signer dans le cadre ci-dessus</p>
                        <input type="hidden" name="signature_data" id="signature_data">
                    </div>

                    <div x-show="!openPad" class="py-10 border-2 border-dashed border-slate-200 rounded-3xl flex flex-col items-center justify-center bg-white/50">
                        <i class="fa-solid fa-cloud-arrow-up text-slate-300 text-4xl mb-4"></i>
                        <p class="text-slate-500 font-bold mb-4 italic">Ou téléchargez une image de signature/cachet</p>
                        <input type="file" name="signature" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>

            </div>

            <div class="flex items-center gap-4 mt-12 pt-8 border-t border-slate-100">
                <button type="submit" @click="saveSignature()"
                        class="px-10 py-4 bg-[#02132D] text-white font-black rounded-2xl hover:bg-black shadow-xl transition-all active:scale-95">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Créer et Enregistrer
                </button>
                <a href="{{ route('proprietaires.index') }}"
                   class="px-10 py-4 bg-slate-100 text-slate-600 font-bold rounded-2xl hover:bg-slate-200 transition-all text-center">
                    Annuler
                </a>
            </div>

        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    function signaturePad() {
        return {
            openPad: false,
            pad: null,
            init() {
                const canvas = document.getElementById('signature-canvas');
                this.pad = new SignaturePad(canvas, {
                    backgroundColor: 'rgba(255, 255, 255, 0)',
                    penColor: 'rgb(30, 58, 138)'
                });
                
                // On surveille le changement de visibilité
                this.$watch('openPad', value => {
                    if (value) {
                        setTimeout(() => this.resizeCanvas(canvas), 50);
                    }
                });

                window.addEventListener('resize', () => this.resizeCanvas(canvas));
            },
            resizeCanvas(canvas) {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                this.pad.clear();
            },
            clearPad() {
                this.pad.clear();
            },
            saveSignature() {
                if (this.openPad && !this.pad.isEmpty()) {
                    document.getElementById('signature_data').value = this.pad.toDataURL();
                }
            }
        }
    }
</script>

@endsection