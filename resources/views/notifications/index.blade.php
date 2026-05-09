@extends('layouts.app')

@section('title', 'Toutes les notifications')

@section('content')
<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-8 border-b border-slate-100 flex justify-between items-center">
        <h2 class="text-xl font-black text-slate-800">Historique des notifications</h2>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <form action="{{ route('notifications.markAsRead') }}" method="POST">
                @csrf
                <button type="submit" class="bg-blue-50 text-blue-600 px-4 py-2 rounded-xl text-sm font-bold hover:bg-blue-100 transition-colors">
                    Tout marquer comme lu
                </button>
            </form>
        @endif
    </div>
    
    <div class="p-0">
        <div class="divide-y divide-slate-50">
            @forelse($notifications as $notification)
                <div class="p-6 flex items-start gap-4 hover:bg-slate-50 transition-colors {{ $notification->read_at ? 'opacity-60' : 'bg-blue-50/30' }}">
                    <div class="w-12 h-12 rounded-2xl {{ $notification->read_at ? 'bg-slate-100 text-slate-400' : 'bg-blue-100 text-blue-600' }} flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <p class="text-slate-700 font-medium">
                                {!! $notification->data['message'] ?? 'Notification' !!}
                            </p>
                            <span class="text-xs text-slate-400 font-bold">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        @if(!$notification->read_at)
                            <span class="mt-2 inline-block px-2 py-0.5 rounded-full bg-blue-600 text-white text-[10px] font-black uppercase">Nouveau</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-20 text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-bell-slash text-3xl text-slate-200"></i>
                    </div>
                    <p class="text-slate-400 font-bold">Aucune notification pour le moment.</p>
                </div>
            @endforelse
        </div>
    </div>
    
    @if($notifications->hasPages())
        <div class="p-8 border-t border-slate-100">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
