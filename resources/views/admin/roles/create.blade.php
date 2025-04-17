@extends('admin.layouts.app')

@section('title', 'Créer un nouveau rôle')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Créer un nouveau rôle</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Rôles</a></li>
        <li class="breadcrumb-item active">Créer</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-tag me-1"></i> Informations du rôle
        </div>
        <div class="card-body">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Nom du rôle <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Le nom du rôle doit être unique et descriptif (ex: "Administrateur", "Éditeur", etc.)</div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Une description claire des responsabilités et des privilèges associés à ce rôle.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Permissions</label>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                @foreach($permissions->groupBy(function($item) {
                                    return explode('.', $item->name)[0];
                                }) as $group => $items)
                                    <div class="col-md-4 mb-3">
                                        <h6 class="border-bottom pb-2">{{ ucfirst($group) }}</h6>
                                        @foreach($items as $permission)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission-{{ $permission->id }}" {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="permission-{{ $permission->id }}">
                                                    {{ str_replace($group . '.', '', $permission->name) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">Créer le rôle</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
