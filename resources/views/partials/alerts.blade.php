@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
         class="fixed top-8 right-8 z-[100] flex items-center gap-4 bg-emerald-600 text-white px-8 py-4 rounded-2xl shadow-2xl shadow-emerald-200 border border-emerald-500 animate-bounce-in">
        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-xl">
            <i class="fa-solid fa-check-double"></i>
        </div>
        <div>
            <p class="font-black text-xs uppercase tracking-widest mb-0.5">Succès</p>
            <p class="text-sm font-bold opacity-90">{{ session('success') }}</p>
        </div>
        <button @click="show = false" class="ml-4 opacity-50 hover:opacity-100 transition-opacity">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
@endif

@if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
         class="fixed top-8 right-8 z-[100] flex items-center gap-4 bg-rose-600 text-white px-8 py-4 rounded-2xl shadow-2xl shadow-rose-200 border border-rose-500 animate-bounce-in">
        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-xl">
            <i class="fa-solid fa-circle-exclamation"></i>
        </div>
        <div>
            <p class="font-black text-xs uppercase tracking-widest mb-0.5">Erreur</p>
            <p class="text-sm font-bold opacity-90">{{ session('error') }}</p>
        </div>
        <button @click="show = false" class="ml-4 opacity-50 hover:opacity-100 transition-opacity">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
@endif

<style>
    .animate-bounce-in {
        animation: bounce-in 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }
    @keyframes bounce-in {
        0% { transform: scale(0.3) translateY(-100px); opacity: 0; }
        100% { transform: scale(1) translateY(0); opacity: 1; }
    }
</style>
