@extends('layouts.user')

@php
use Illuminate\Support\Str;
@endphp

@section('title', 'Mes Projets')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mes Projets</h5>
                    <div>
                        <a href="{{ route('user.projects.export.csv') }}" class="btn btn-success me-2">
                            <i class="fas fa-file-export"></i> Exporter CSV
                        </a>
                        <a href="{{ route('user.projects.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouveau Projet
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if($projects->isEmpty())
                        <div class="alert alert-info">
                            Vous n'avez pas encore créé de projets. 
                            <a href="{{ route('user.projects.create') }}">Créer votre premier projet</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Description</th>
                                        <th>Statut</th>
                                        <th>Clés</th>
                                        <th>Date de création</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($projects as $project)
                                        <tr>
                                            <td>
                                                <a href="{{ route('user.projects.show', $project->id) }}">
                                                    {{ $project->name }}
                                                </a>
                                            </td>
                                            <td>{{ Str::limit($project->description, 50) }}</td>
                                            <td>
                                                @if($project->is_active)
                                                    <span class="badge bg-success">Actif</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactif</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $project->totalKeysCount() }} clés
                                                <span class="text-muted">({{ $project->activeKeysCount() }} actives)</span>
                                            </td>
                                            <td>{{ $project->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('user.projects.show', $project->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('user.projects.edit', $project->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')) { 
                                                                document.getElementById('delete-project-{{ $project->id }}').submit(); 
                                                            }">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <form id="delete-project-{{ $project->id }}" 
                                                          action="{{ route('user.projects.destroy', $project->id) }}" 
                                                          method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $projects->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
