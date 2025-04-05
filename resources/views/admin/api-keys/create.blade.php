@extends('admin.layouts.app')

@section('title', __('Créer une clé API'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Créer une clé API') }}</h1>
        <a href="{{ route('admin.api-keys.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('Retour') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.api-keys.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="project_id">{{ __('Projet') }}</label>
                            <select name="project_id" id="project_id" class="form-control @error('project_id') is-invalid @enderror" required>
                                <option value="">{{ __('Sélectionnez un projet') }}</option>
                                @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('project_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">{{ __('Nom de la clé') }}</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="expires_at">{{ __('Date d\'expiration') }}</label>
                            <input type="datetime-local" name="expires_at" id="expires_at"
                                class="form-control @error('expires_at') is-invalid @enderror" value="{{ old('expires_at') }}">
                            <small class="form-text text-muted">{{ __('Laisser vide pour aucune expiration') }}</small>
                            @error('expires_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <h5>{{ __('Permissions') }}</h5>
                        <div class="row">
                            @foreach(config('api.permissions') as $permission => $description)
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission }}"
                                        id="permission_{{ $permission }}" class="form-check-input"
                                        {{ in_array($permission, old('permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permission_{{ $permission }}">
                                        {{ __($description) }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('Créer la clé API') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 