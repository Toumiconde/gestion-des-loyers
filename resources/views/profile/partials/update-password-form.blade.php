<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-black text-slate-700 mb-2">Mot de passe actuel</label>
            <input id="update_password_current_password" name="current_password" type="password" 
                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" 
                   autocomplete="current-password">
            @error('current_password', 'updatePassword') <p class="mt-2 text-xs text-rose-500 font-bold">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-black text-slate-700 mb-2">Nouveau mot de passe</label>
            <input id="update_password_password" name="password" type="password" 
                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" 
                   autocomplete="new-password">
            @error('password', 'updatePassword') <p class="mt-2 text-xs text-rose-500 font-bold">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-black text-slate-700 mb-2">Confirmer le mot de passe</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" 
                   class="w-full bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" 
                   autocomplete="new-password">
            @error('password_confirmation', 'updatePassword') <p class="mt-2 text-xs text-rose-500 font-bold">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-8 py-3 bg-amber-500 text-white font-black rounded-xl hover:bg-amber-600 shadow-lg shadow-amber-200 transition-all">
                Changer le mot de passe
            </button>
        </div>
    </form>
</section>
