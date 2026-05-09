@extends('layouts.app')

@section('title', 'Modifier Propriétaire')

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
                    <span class="text-slate-600">Modifier {{ $proprietaire->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        {{-- En-tête --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-8 text-white relative overflow-hidden">
            <div class="absolute right-0 bottom-0 opacity-10">
                <i class="fa-solid fa-user-pen text-9xl -mb-8 -mr-8"></i>
            </div>
            <div class="relative z-10">
                <h2 class="text-3xl font-black mb-2">Modifier le profil</h2>
                <p class="text-blue-100 italic">Mise à jour des informations de {{ $proprietaire->name }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('proprietaires.update', $proprietaire) }}" class="p-8" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                {{-- Identité --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-id-card text-blue-600"></i>
                        <h3 class="font-black text-slate-800 uppercase tracking-widest text-sm">Identité</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Nom complet</label>
                        <div class="relative">
                            <input type="text" name="name" value="{{ old('name', $proprietaire->name) }}"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                   required>
                            <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                        @error('name') <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Adresse Email</label>
                        <div class="relative">
                            <input type="email" name="email" value="{{ old('email', $proprietaire->user->email ?? '') }}"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                   required>
                            <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                        @error('email') <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Nouveau mot de passe (laisser vide)</label>
                        <div class="relative">
                            <input type="password" name="password"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            <i class="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                        @error('password') <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Coordonnées --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-location-dot text-emerald-600"></i>
                        <h3 class="font-black text-slate-800 uppercase tracking-widest text-sm">Coordonnées</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Téléphone</label>
                        <div class="relative">
                            <input type="text" name="telephone" value="{{ old('telephone', $proprietaire->telephone) }}"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                            <i class="fa-solid fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Adresse de résidence</label>
                        <div class="relative">
                            <textarea name="adresse" rows="1"
                                      class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">{{ old('adresse', $proprietaire->adresse) }}</textarea>
                            <i class="fa-solid fa-map-location-dot absolute left-4 top-4 text-slate-400"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">RIB Bancaire (N° de compte)</label>
                        <div class="relative">
                            <input type="text" name="rib_bancaire" value="{{ old('rib_bancaire', $proprietaire->rib_bancaire) }}"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                            <i class="fa-solid fa-building-columns absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Nom de la Banque</label>
                        <div class="relative">
                            <input type="text" name="nom_banque" value="{{ old('nom_banque', $proprietaire->nom_banque) }}"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                            <i class="fa-solid fa-university absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 mb-2">Titulaire du compte</label>
                        <div class="relative">
                            <input type="text" name="titulaire_compte" value="{{ old('titulaire_compte', $proprietaire->titulaire_compte) }}"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                            <i class="fa-solid fa-user-tag absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>
                </div>

                {{-- Signature & Cachet --}}
                <div class="space-y-6" x-data="signaturePad()">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-signature text-blue-600"></i>
                        <h3 class="font-black text-slate-800 uppercase tracking-widest text-sm">Signature & Cachet Officiel</h3>
                    </div>

                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                        <div class="flex items-center justify-between mb-6">
                            <label class="block text-sm font-black text-slate-700">Votre signature officielle</label>
                            <button type="button" @click="openPad = !openPad" 
                                    class="text-[10px] font-black uppercase tracking-widest px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2 shadow-lg shadow-blue-100">
                                <i class="fa-solid fa-pen-nib"></i>
                                <span x-text="openPad ? 'Annuler le dessin' : 'Signer maintenant'"></span>
                            </button>
                        </div>
                        
                        <div class="flex items-start gap-8">
                            {{-- Aperçu Actuel --}}
                            <div class="flex flex-col items-center gap-2">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Signature actuelle</p>
                                <div class="w-40 h-28 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-center overflow-hidden">
                                    @if($proprietaire->signature)
                                        <img src="{{ Storage::url($proprietaire->signature) }}" class="max-w-full max-h-full object-contain p-2">
                                    @else
                                        <i class="fa-solid fa-file-signature text-slate-100 text-5xl"></i>
                                    @endif
                                </div>
                            </div>

                            {{-- Pad de signature (Masqué par défaut) --}}
                            <div x-show="openPad" x-transition class="flex-1 space-y-4">
                                <div class="bg-white border-2 border-dashed border-blue-200 rounded-2xl p-4 relative">
                                    <canvas id="signature-canvas" class="w-full h-40 bg-slate-50 rounded-xl cursor-crosshair"></canvas>
                                    <button type="button" @click="clearPad()" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-rose-50 hover:text-rose-600 transition-all">
                                        <i class="fa-solid fa-eraser text-xs"></i>
                                    </button>
                                </div>
                                <p class="text-[10px] text-slate-500 italic">Signez à l'aide de votre souris ou de votre doigt ci-dessus.</p>
                                <input type="hidden" name="signature_data" id="signature_data">
                            </div>

                            {{-- Upload Classique --}}
                            <div x-show="!openPad" class="flex-1 flex flex-col justify-center">
                                <p class="text-xs text-slate-500 mb-4">Vous pouvez aussi téléverser une image de votre cachet :</p>
                                <input type="file" name="signature" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex items-center gap-4 mt-12 pt-8 border-t border-slate-100">
                <button type="submit" @click="saveSignature()"
                        class="px-10 py-4 bg-emerald-600 text-white font-black rounded-2xl hover:bg-emerald-700 shadow-xl shadow-emerald-200 transition-all active:scale-95">
                    <i class="fa-solid fa-check-double mr-2"></i> Mettre à jour les informations
                </button>
                <a href="{{ route('proprietaires.index') }}"
                   class="px-10 py-4 bg-slate-100 text-slate-600 font-bold rounded-2xl hover:bg-slate-200 transition-all">
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
