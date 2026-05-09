<section>
    <form method="post" action="{{ route('profile.update') }}" class="space-y-8" enctype="multipart/form-data">
        @csrf
        @method('patch')

        {{-- Upload de photo --}}
        <div class="flex items-center gap-6 p-6 bg-slate-50 rounded-2xl border border-slate-100">
            <div class="shrink-0">
                @if(auth()->user()->photo)
                    <img id="preview" src="{{ asset('storage/' . auth()->user()->photo) }}" class="h-20 w-20 object-cover rounded-2xl shadow-sm">
                @else
                    <div id="placeholder" class="h-20 w-20 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600">
                        <i class="fa-solid fa-user text-2xl"></i>
                    </div>
                    <img id="preview" src="" class="h-20 w-20 object-cover rounded-2xl shadow-sm hidden">
                @endif
            </div>
            <div class="flex-1">
                <label class="block text-sm font-black text-slate-700 mb-2">Photo de profil</label>
                <input type="file" name="photo" id="photo-input" 
                       class="block w-full text-sm text-slate-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-full file:border-0
                              file:text-sm file:font-bold
                              file:bg-blue-50 file:text-blue-700
                              hover:file:bg-blue-100 transition-all cursor-pointer"
                       onchange="previewImage(event)">
                <p class="mt-1 text-xs text-slate-400">JPG, PNG ou WebP. Max 2Mo.</p>
                @error('photo') <p class="mt-1 text-xs text-rose-500 font-bold">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Nom --}}
        <div>
            <label for="name" class="block text-sm font-black text-slate-700 mb-2">Nom complet</label>
            <input id="name" name="name" type="text" 
                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" 
                   value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name') <p class="mt-2 text-xs text-rose-500 font-bold">{{ $message }}</p> @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-black text-slate-700 mb-2">Adresse Email</label>
            <input id="email" name="email" type="email" 
                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" 
                   value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email') <p class="mt-2 text-xs text-rose-500 font-bold">{{ $message }}</p> @enderror
        </div>

        {{-- Téléphone & Adresse (Pour Proprio et Locataire) --}}
        @php
            $phone = '';
            $address = '';
            if($user->role === 'proprietaire' && $user->proprietaire) {
                $phone = $user->proprietaire->telephone;
                $address = $user->proprietaire->adresse;
            } elseif($user->role === 'locataire' && $user->locataire) {
                $phone = $user->locataire->telephone;
                $address = $user->locataire->adresse;
            }
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="telephone" class="block text-sm font-black text-slate-700 mb-2">Téléphone</label>
                <input id="telephone" name="telephone" type="text" 
                       class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" 
                       value="{{ old('telephone', $phone) }}" placeholder="+224 ...">
                @error('telephone') <p class="mt-2 text-xs text-rose-500 font-bold">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="adresse" class="block text-sm font-black text-slate-700 mb-2">Adresse de résidence</label>
                <input id="adresse" name="adresse" type="text" 
                       class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" 
                       value="{{ old('adresse', $address) }}" placeholder="Quartier, Ville...">
                @error('adresse') <p class="mt-2 text-xs text-rose-500 font-bold">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all">
                Mettre à jour le profil
            </button>
        </div>
    </form>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function(){
                const output = document.getElementById('preview');
                const placeholder = document.getElementById('placeholder');
                output.src = reader.result;
                output.classList.remove('hidden');
                if(placeholder) placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</section>
