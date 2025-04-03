@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Historique de la clé de série</h1>
        <a href="{{ route('admin.serial-keys.show', $serialKey) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
            Retour aux détails
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4 border-b">
            <h2 class="text-lg font-medium">Clé : {{ $serialKey->serial_key }}</h2>
            <p class="text-gray-600">Projet : {{ $serialKey->project->name }}</p>
        </div>

        <div class="p-4">
            <div class="space-y-4">
                @forelse ($history as $entry)
                    <div class="border-l-4 border-blue-500 pl-4 py-2">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium">
                                    @switch($entry->action)
                                        @case('created')
                                            Création de la clé
                                            @break
                                        @case('updated')
                                            Mise à jour
                                            @break
                                        @case('revoked')
                                            Révocation
                                            @break
                                        @case('deleted')
                                            Suppression
                                            @break
                                        @default
                                            {{ ucfirst($entry->action) }}
                                    @endswitch
                                </p>
                                <p class="text-sm text-gray-600">
                                    Par : {{ $entry->user ? $entry->user->name : 'Système' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">
                                    {{ $entry->created_at->format('d/m/Y H:i') }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    IP : {{ $entry->ip_address }}
                                </p>
                            </div>
                        </div>

                        @if($entry->details)
                            <div class="mt-2 text-sm text-gray-700">
                                @if($entry->action === 'updated')
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <h4 class="font-medium">Anciennes valeurs :</h4>
                                            <ul class="list-disc list-inside">
                                                @foreach($entry->details['old_data'] as $key => $value)
                                                    @if(in_array($key, ['project_id', 'domain', 'ip_address', 'expires_at', 'status']))
                                                        <li>{{ $key }}: {{ $value ?? 'Non défini' }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div>
                                            <h4 class="font-medium">Nouvelles valeurs :</h4>
                                            <ul class="list-disc list-inside">
                                                @foreach($entry->details['new_data'] as $key => $value)
                                                    @if(in_array($key, ['project_id', 'domain', 'ip_address', 'expires_at', 'status']))
                                                        <li>{{ $key }}: {{ $value ?? 'Non défini' }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @else
                                    <pre class="bg-gray-100 p-2 rounded">{{ json_encode($entry->details, JSON_PRETTY_PRINT) }}</pre>
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-600 text-center py-4">Aucun historique disponible pour cette clé.</p>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $history->links() }}
            </div>
        </div>
    </div>
</div>
@endsection