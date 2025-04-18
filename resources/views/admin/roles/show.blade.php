@extends('admin.layouts.app')

@section('title', 'Détails du rôle')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Détails du rôle: {{ $role->name }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Rôles</a></li>
        <li class="breadcrumb-item active">Détails</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div><i class="fas fa-user-tag me-1"></i> Informations du rôle</div>
                    <div>
                        <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nom</label>
                        <p>{{ $role->name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <p>{{ $role->description ?? 'Aucune description' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Date de création</label>
                        <p>{{ $role->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dernière mise à jour</label>
                        <p>{{ $role->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-key me-1"></i> Permissions
                </div>
                <div class="card-body">
                    @if($role->permissions->count() > 0)
                        <div class="row">
                            @foreach($role->permissions->groupBy(function($item) {
                                return explode('.', $item->name)[0];
                            }) as $group => $items)
                                <div class="col-md-4 mb-3">
                                    <h6 class="border-bottom pb-2">{{ ucfirst($group) }}</h6>
                                    <ul class="list-unstyled">
                                        @foreach($items as $permission)
                                            <li>
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                {{ str_replace($group . '.', '', $permission->name) }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            Ce rôle n'a aucune permission assignée.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-users-cog me-1"></i> Administrateurs avec ce rôle ({{ $admins->total() }})
                </div>
                <div class="card-body">
                    @if($admins->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($admins as $admin)
                                        <tr>
                                            <td>{{ $admin->name }}</td>
                                            <td>{{ $admin->email }}</td>
                                            <td>
                                                <span class="badge {{ $admin->is_super_admin ? 'bg-danger' : 'bg-primary' }}">
                                                    {{ $admin->is_super_admin ? 'Super Admin' : 'Admin' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.users.show', $admin->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $admins->links() }}
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            Aucun administrateur n'a ce rôle.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-users me-1"></i> Utilisateurs avec ce rôle ({{ $users->total() }})
                </div>
                <div class="card-body">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Date d'inscription</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
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
                        <div class="d-flex justify-content-center mt-3">
                            {{ $users->links() }}
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            Aucun utilisateur n'a ce rôle.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
