@extends('layouts.admin')

@section('title', 'Générer des clés de série')

@section('header', 'Générer des clés')

@section('content')
<div class="mb-4">
    <h2 class="text-xl font-semibold text-gray-800">Générer de nouvelles clés de série</h2>
</div>

<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Formulaire de génération</h3>
    </div>
    <div class="border-t border-gray-200 px-4 py-5">
        <form action="{{ route('admin.serial-keys.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Projet -->
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700">Projet</label>
                    <select id="project_id" name="project_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" required>
                        <option value="">Sélectionnez un projet</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id', request('project_id')) == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                        @endforeach
                    </select>
                    @error('project_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Quantité -->
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700">Nombre de clés à générer</label>
                    <input type="number" name="quantity" id="quantity" min="1" max="100" value="{{ old('quantity', 1) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('quantity')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Domaine (optionnel) -->
                <div>
                    <label for="domain" class="block text-sm font-medium text-gray-700">Domaine (optionnel)</label>
                    <input type="text" name="domain" id="domain" value="{{ old('domain') }}" placeholder="exemple.com" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    <p class="mt-1 text-xs text-gray-500">Si spécifié, la clé ne fonctionnera que pour ce domaine.</p>
                    @error('domain')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Adresse IP (optionnel) -->
                <div>
                    <label for="ip_address" class="block text-sm font-medium text-gray-700">Adresse IP (optionnel)</label>
                    <input type="text" name="ip_address" id="ip_address" value="{{ old('ip_address') }}" placeholder="192.168.1.1" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    <p class="mt-1 text-xs text-gray-500">Si spécifiée, la clé ne fonctionnera que pour cette adresse IP.</p>
                    @error('ip_address')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Date d'expiration (optionnel) -->
                <div>
                    <label for="expires_at" class="block text-sm font-medium text-gray-700">Date d'expiration (optionnel)</label>
                    <input type="date" name="expires_at" id="expires_at" value="{{ old('expires_at') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    <p class="mt-1 text-xs text-gray-500">Si non spécifiée, la clé n'expirera jamais.</p>
                    @error('expires_at')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Générer les clés
                </button>
                <a href="{{ url()->previous() }}" class="ml-3 inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection