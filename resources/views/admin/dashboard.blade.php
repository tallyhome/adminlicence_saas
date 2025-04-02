@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('header', 'Tableau de bord')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <!-- Statistiques des projets -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total des projets</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['total_projects'] }}</dd>
            </dl>
        </div>
    </div>

    <!-- Statistiques des clés actives -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Clés actives</dt>
                <dd class="mt-1 text-3xl font-semibold text-green-600">{{ $stats['active_keys'] }}</dd>
            </dl>
        </div>
    </div>

    <!-- Statistiques des clés révoquées -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Clés révoquées</dt>
                <dd class="mt-1 text-3xl font-semibold text-red-600">{{ $stats['revoked_keys'] }}</dd>
            </dl>
        </div>
    </div>

    <!-- Statistiques des clés expirées -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Clés expirées</dt>
                <dd class="mt-1 text-3xl font-semibold text-yellow-600">{{ $stats['expired_keys'] }}</dd>
            </dl>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Projets récents -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Projets récents</h3>
            <a href="{{ route('admin.projects.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Voir tous</a>
        </div>
        <div class="border-t border-gray-200">
            <ul class="divide-y divide-gray-200">
                @forelse ($recentProjects as $project)
                <li>
                    <a href="{{ route('admin.projects.show', $project) }}" class="block hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-indigo-600 truncate">{{ $project->name }}</p>
                                <div class="ml-2 flex-shrink-0 flex">
                                    <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $project->activeKeysCount() }} clés actives
                                    </p>
                                </div>
                            </div>
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <p class="flex items-center text-sm text-gray-500">
                                        {{ $project->description ?? 'Aucune description' }}
                                    </p>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                    <p>Créé le {{ $project->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
                @empty
                <li class="px-4 py-4 sm:px-6 text-gray-500 text-center">
                    Aucun projet créé pour le moment.
                </li>
                @endforelse
            </ul>
        </div>
    </div>

    <!-- Clés récentes -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Clés récentes</h3>
            <a href="{{ route('admin.serial-keys.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Voir toutes</a>
        </div>
        <div class="border-t border-gray-200">
            <ul class="divide-y divide-gray-200">
                @forelse ($recentKeys as $key)
                <li>
                    <a href="{{ route('admin.serial-keys.show', $key) }}" class="block hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-indigo-600 truncate">{{ $key->serial_key }}</p>
                                <div class="ml-2 flex-shrink-0 flex">
                                    @if($key->status === 'active')
                                    <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </p>
                                    @elseif($key->status === 'revoked')
                                    <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Révoquée
                                    </p>
                                    @elseif($key->status === 'expired')
                                    <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Expirée
                                    </p>
                                    @else
                                    <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Suspendue
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <p class="flex items-center text-sm text-gray-500">
                                        Projet: {{ $key->project->name }}
                                    </p>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                    <p>{{ $key->expires_at ? 'Expire le ' . $key->expires_at->format('d/m/Y') : 'Sans expiration' }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
                @empty
                <li class="px-4 py-4 sm:px-6 text-gray-500 text-center">
                    Aucune clé créée pour le moment.
                </li>
                @endforelse
            </ul>
         </div>
    </div>
</div>
@endsection