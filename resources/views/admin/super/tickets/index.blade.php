@extends('admin.layouts.app')

@section('title', 'Tickets SuperAdmin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Tickets transférés au SuperAdmin</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des tickets</h5>
                <div>
                    <div class="btn-group">
                        <a href="{{ route('admin.super.tickets.index', ['status' => 'all']) }}" class="btn btn-sm {{ $status == 'all' ? 'btn-primary' : 'btn-outline-primary' }}">Tous</a>
                        <a href="{{ route('admin.super.tickets.index', ['status' => 'forwarded_to_super_admin']) }}" class="btn btn-sm {{ $status == 'forwarded_to_super_admin' ? 'btn-primary' : 'btn-outline-primary' }}">Transférés</a>
                        <a href="{{ route('admin.super.tickets.index', ['status' => 'in_progress']) }}" class="btn btn-sm {{ $status == 'in_progress' ? 'btn-primary' : 'btn-outline-primary' }}">En cours</a>
                        <a href="{{ route('admin.super.tickets.index', ['status' => 'resolved_by_super_admin']) }}" class="btn btn-sm {{ $status == 'resolved_by_super_admin' ? 'btn-primary' : 'btn-outline-primary' }}">Résolus</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($tickets->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Sujet</th>
                            <th>Statut</th>
                            <th>Priorité</th>
                            <th>Dernière réponse</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->id }}</td>
                            <td>{{ $ticket->client->name }}</td>
                            <td>{{ $ticket->subject }}</td>
                            <td>
                                @if($ticket->status == 'forwarded_to_super_admin')
                                <span class="badge bg-info">Transféré</span>
                                @elseif($ticket->status == 'in_progress')
                                <span class="badge bg-warning">En cours</span>
                                @elseif($ticket->status == 'resolved_by_super_admin')
                                <span class="badge bg-success">Résolu</span>
                                @elseif($ticket->status == 'closed')
                                <span class="badge bg-secondary">Fermé</span>
                                @else
                                <span class="badge bg-primary">{{ $ticket->status }}</span>
                                @endif
                            </td>
                            <td>
                                @if($ticket->priority == 'high')
                                <span class="badge bg-danger">Haute</span>
                                @elseif($ticket->priority == 'medium')
                                <span class="badge bg-warning">Moyenne</span>
                                @else
                                <span class="badge bg-success">Basse</span>
                                @endif
                            </td>
                            <td>{{ $ticket->last_reply_at ? $ticket->last_reply_at->diffForHumans() : 'Jamais' }}</td>
                            <td>
                                <a href="{{ route('admin.super.tickets.show', $ticket) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $tickets->appends(request()->query())->links() }}
            </div>
            @else
            <div class="text-center py-4">
                <p class="mb-0">Aucun ticket trouvé.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection