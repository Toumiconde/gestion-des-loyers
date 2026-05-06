@extends('layouts.app')

@section('title', 'Détail activité')

@section('content')
<div class="bg-white rounded-xl shadow p-6 max-w-2xl">

    <dl class="space-y-4 text-sm">

        <div>
            <dt class="text-gray-400 mb-1">Utilisateur</dt>
            <dd class="font-medium text-gray-800">
                {{ $activityLog->user?->name ?? 'Système' }}
            </dd>
        </div>

        <div>
            <dt class="text-gray-400 mb-1">Action</dt>
            <dd>
                <span class="px-2 py-1 rounded text-xs font-mono
                    {{ str_contains($activityLog->action, 'crée') ? 'bg-green-100 text-green-700' :
                       (str_contains($activityLog->action, 'modifié') ? 'bg-yellow-100 text-yellow-700' :
                       (str_contains($activityLog->action, 'supprimé') ? 'bg-red-100 text-red-700' :
                       'bg-blue-100 text-blue-700')) }}">
                    {{ $activityLog->action }}
                </span>
            </dd>
        </div>

        <div>
            <dt class="text-gray-400 mb-1">Élément concerné</dt>
            <dd class="font-mono text-gray-700">
                @if($activityLog->model_type)
                    {{ class_basename($activityLog->model_type) }} #{{ $activityLog->model_id }}
                @else
                    —
                @endif
            </dd>
        </div>

        <div>
            <dt class="text-gray-400 mb-1">Adresse IP</dt>
            <dd class="font-mono">{{ $activityLog->ip_address ?? '—' }}</dd>
        </div>

        <div>
            <dt class="text-gray-400 mb-1">Date et heure</dt>
            <dd>{{ $activityLog->created_at->format('d/m/Y à H:i:s') }}</dd>
        </div>

        @if($activityLog->details)
        <div>
            <dt class="text-gray-400 mb-1">Détails</dt>
            <dd>
                <div class="bg-gray-50 rounded-lg p-4 font-mono text-xs text-gray-700 overflow-auto">
                    <pre>{{ json_encode($activityLog->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </dd>
        </div>
        @endif

    </dl>

    <div class="mt-6">
        <a href="{{ route('activity-logs.index') }}"
           class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 text-sm">
            Retour aux logs
        </a>
    </div>

</div>
@endsection