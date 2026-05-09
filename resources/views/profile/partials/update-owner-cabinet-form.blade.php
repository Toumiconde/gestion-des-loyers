<section>
    <header>
        <p class="mt-1 text-sm text-slate-500 font-medium">
            Ces informations sont utilisées pour l'édition de vos quittances et le versement de vos loyers.
        </p>
    </header>

    <div class="mt-6 space-y-8">
        {{-- RIB & Infos --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">RIB / Coordonnées Bancaires</label>
                <input type="text" name="rib_bancaire" value="{{ old('rib_bancaire', $user->proprietaire->rib_bancaire) }}" class="w-full bg-slate-50 border border-slate-200 py-3 px-4 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold" placeholder="Ex: FR76 1234 5678...">
                <p class="mt-2 text-[10px] text-slate-400 font-bold italic italic">Le compte sur lequel l'agence vous reversera les loyers.</p>
            </div>
            <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Téléphone Professionnel</label>
                <input type="text" name="telephone" value="{{ old('telephone', $user->proprietaire->telephone) }}" class="w-full bg-slate-50 border border-slate-200 py-3 px-4 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold">
            </div>
        </div>

        <div>
            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Adresse de Correspondance / Siège</label>
            <textarea name="adresse_professionnelle" rows="2" class="w-full bg-slate-50 border border-slate-200 py-3 px-4 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold">{{ old('adresse_professionnelle', $user->proprietaire->adresse_professionnelle) }}</textarea>
        </div>

        {{-- Signature --}}
        <div class="p-8 bg-slate-50 rounded-3xl border border-slate-200">
            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-4">Signature Digitale Officielle</label>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div class="text-center">
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-4">Signature Actuelle</p>
                    <div class="w-full h-32 bg-white rounded-2xl border border-slate-200 flex items-center justify-center overflow-hidden">
                        @if($user->proprietaire->signature)
                            <img src="{{ Storage::url($user->proprietaire->signature) }}" class="max-h-full max-w-full object-contain">
                        @else
                            <span class="text-slate-300 italic text-xs">Aucune signature</span>
                        @endif
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-4">Nouvelle Signature (Pad de dessin)</p>
                    <canvas id="signature-pad" class="w-full h-32 bg-white rounded-2xl border-2 border-dashed border-slate-300 cursor-crosshair"></canvas>
                    <input type="hidden" name="signature_data" id="signature-data">
                    <div class="flex justify-between mt-2">
                        <button type="button" id="clear-signature" class="text-[10px] font-black text-rose-500 uppercase">Effacer</button>
                        <p class="text-[10px] text-slate-400 font-bold italic">Signez avec votre souris ou doigt</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-slate-200">
                <p class="text-[10px] font-black text-slate-400 uppercase mb-4 text-center">Ou uploader un fichier image</p>
                <input type="file" name="signature" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('signature-pad');
        const signaturePad = new SignaturePad(canvas);
        const clearButton = document.getElementById('clear-signature');
        const signatureInput = document.getElementById('signature-data');
        const form = canvas.closest('form');

        // Redimensionner le canvas pour la réactivité
        function resizeCanvas() {
            const ratio =  Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
        }

        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();

        clearButton.addEventListener('click', function () {
            signaturePad.clear();
            signatureInput.value = '';
        });

        form.addEventListener('submit', function () {
            if (!signaturePad.isEmpty()) {
                signatureInput.value = signaturePad.toDataURL();
            }
        });
    });
</script>
