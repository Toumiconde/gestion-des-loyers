@if(session('password_reset_success'))
@php $reset = session('password_reset_success'); @endphp
<div class="mb-8 p-6 {{ $reset['email_sent'] ? 'bg-emerald-50 border-emerald-200' : 'bg-amber-50 border-amber-200' }} border rounded-[30px] shadow-sm relative overflow-hidden">
    <div class="absolute -right-6 -bottom-6 opacity-10">
        <i class="fa-solid fa-{{ $reset['email_sent'] ? 'envelope-circle-check' : 'key' }} text-7xl {{ $reset['email_sent'] ? 'text-emerald-600' : 'text-amber-600' }}"></i>
    </div>
    <div class="flex flex-col md:flex-row items-center gap-6 relative z-10">
        <div class="w-16 h-16 rounded-2xl {{ $reset['email_sent'] ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }} flex items-center justify-center text-3xl shrink-0">
            <i class="fa-solid fa-{{ $reset['email_sent'] ? 'envelope-circle-check' : 'shield-halved' }}"></i>
        </div>
        <div class="flex-1 text-center md:text-left">
            <h4 class="text-lg font-black {{ $reset['email_sent'] ? 'text-emerald-900' : 'text-amber-900' }} mb-1">
                @if($reset['email_sent'])
                    ✅ Mot de passe réinitialisé & email envoyé !
                @else
                    🔑 Mot de passe réinitialisé
                @endif
            </h4>
            <p class="{{ $reset['email_sent'] ? 'text-emerald-800' : 'text-amber-800' }} text-sm font-medium mb-3">
                @if($reset['email_sent'])
                    Le nouveau mot de passe a été envoyé directement à <span class="font-black underline">{{ $reset['email'] }}</span>.
                @else
                    Aucun email configuré pour <span class="font-black">{{ $reset['name'] }}</span>. Transmettez ce code manuellement :
                @endif
            </p>

            {{-- Toujours afficher le mot de passe pour que l'admin puisse le noter si besoin --}}
            <div class="inline-flex items-center gap-4 bg-white px-5 py-3 rounded-2xl border {{ $reset['email_sent'] ? 'border-emerald-200' : 'border-amber-200' }} shadow-sm">
                <span class="text-[10px] font-black uppercase tracking-widest {{ $reset['email_sent'] ? 'text-emerald-600' : 'text-amber-600' }}">Pass :</span>
                <code class="text-lg font-black text-slate-900 tracking-wider">{{ $reset['temp_password'] }}</code>
                <button 
                    onclick="navigator.clipboard.writeText('{{ $reset['temp_password'] }}'); this.innerHTML='<i class=\'fa-solid fa-check text-emerald-500\'></i>'"
                    class="{{ $reset['email_sent'] ? 'text-emerald-600 hover:text-emerald-700' : 'text-amber-600 hover:text-amber-700' }} p-1 transition-colors"
                    title="Copier">
                    <i class="fa-solid fa-copy"></i>
                </button>
            </div>
        </div>

        @if(!$reset['email_sent'])
        <div class="p-4 bg-amber-100/50 rounded-2xl text-[10px] text-amber-800 font-bold leading-relaxed max-w-[220px] text-center">
            ⚠️ Cet utilisateur n'a pas d'email configuré. Transmettez ce code de vive voix ou par SMS.
        </div>
        @else
        <div class="p-4 bg-emerald-100/50 rounded-2xl text-[10px] text-emerald-800 font-bold leading-relaxed max-w-[220px] text-center">
            📧 Un email professionnel a été automatiquement envoyé à l'adresse de l'utilisateur.
        </div>
        @endif
    </div>
</div>
@endif
