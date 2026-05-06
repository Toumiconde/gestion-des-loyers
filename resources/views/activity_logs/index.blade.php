@extends('layouts.app')

@section('title', 'Journal d\'activité')

@section('content')
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 text-left">Utilisateur</th>
                <th class="px-6 py-3 text-left">Action</th>
                <th class="px-6 py-3 text-left">Élément concerné</th>
                <th class="px-6 py-3 text-left">Adresse IP</th>
                <th class="px-6 py-3 text-left">Date</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($logs as $log)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">
                    {{ $log->user?->name ?? 'Système' }}
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded text-xs font-mono
                        {{ str_contains($log->action, 'crée') ? 'bg-green-100 text-green-700' :
                           (str_contains($log->action, 'modifié') ? 'bg-yellow-100 text-yellow-700' :
                           (str_contains($log->action, 'supprimé') ? 'bg-red-100 text-red-700' :
                           'bg-blue-100 text-blue-700')) }}">
                        {{ $log->action }}
                    </span>
                </td>
                <td class="px-6 py-4 text-gray-500">
                    @if($log->model_type)
                        {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                    @else
                        —
                    @endif
                </td>
                <td class="px-6 py-4 font-mono text-gray-500">
                    {{ $log->ip_address ?? '—' }}
                </td>
                <td class="px-6 py-4">
                    {{ $log->created_at->format('d/m/Y à H:i') }}
                </td>
                <td class="px-6 py-4">
                    <a href="{{ route('activity-logs.show', $log) }}"
                       class="text-blue-600 hover:underline">Voir</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                    Aucune activité enregistrée.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $logs->links() }}</div>
</div>
@endsection