@extends('admin.layouts.app')

@php
use Illuminate\Support\Facades\Auth;
@endphp

@section('title', 'Détails de l\'administrateur')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Détails de l'administrateur</h1>
        <a href="{{ route('admin.users.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Retour à la liste
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Informations de l'administrateur -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informations personnelles</h6>
                    @if(Auth::guard('admin')->user()->is_super_admin || Auth::guard('admin')->id() == $admin->id)
                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editAdminModal">
                        <i class="fas fa-edit fa-sm"></i> Modifier
                    </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Nom</label>
                        <p class="h5 mb-0 font-weight-bold text-gray-800">{{ $admin->name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Email</label>
                        <p class="h5 mb-0 font-weight-bold text-gray-800">{{ $admin->email }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Date d'inscription</label>
                        <p class="h5 mb-0 font-weight-bold text-gray-800">{{ $admin->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Type</label>
                        <p class="h5 mb-0 font-weight-bold text-gray-800">
                            <span class="badge {{ $admin->is_super_admin ? 'bg-danger' : 'bg-primary' }}">
                                {{ $admin->is_super_admin ? 'Super Administrateur' : 'Administrateur' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistiques</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Utilisateurs gérés</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $userCount }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Rôles attribués</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $roles->count() }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Dernière connexion</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'Jamais' }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Rôles et permissions -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Rôles et permissions</h6>
                </div>
                <div class="card-body">
                    @if($roles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Rôle</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $role)
                                    <tr>
                                        <td><span class="badge bg-primary">{{ $role->name }}</span></td>
                                        <td>{{ $role->description ?? 'Aucune description' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500">Aucun rôle attribué</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Utilisateurs gérés -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Utilisateurs gérés ({{ $userCount }} au total)</h6>
                    @if($userCount > 5)
                    <a href="#" class="btn btn-sm btn-primary">
                        <i class="fas fa-list fa-sm"></i> Voir tous
                    </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($managedUsers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Date d'inscription</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($managedUsers as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500">Aucun utilisateur géré</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de modification de l'administrateur -->
<div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAdminModalLabel">Modifier l'administrateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.admins.update', $admin->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $admin->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $admin->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                    @if(Auth::guard('admin')->user()->is_super_admin)
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_super_admin" name="is_super_admin" value="1" {{ $admin->is_super_admin ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_super_admin">Super Administrateur</label>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
