@extends('layouts.admin')

@section('title', 'Factures')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Factures</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Factures</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-file-invoice-dollar me-1"></i>
            Liste des factures
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Numéro</th>
                        <th>Client</th>
                        <th>Abonnement</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->number }}</td>
                            <td>{{ $invoice->tenant->name }}</td>
                            <td>
                                @if($invoice->subscription)
                                    {{ $invoice->subscription->plan->name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ number_format($invoice->total, 2) }} €</td>
                            <td>
                                @if($invoice->status === 'paid')
                                    <span class="badge bg-success">Payée</span>
                                @elseif($invoice->status === 'pending')
                                    <span class="badge bg-warning">En attente</span>
                                @elseif($invoice->status === 'failed')
                                    <span class="badge bg-danger">Échouée</span>
                                @else
                                    <span class="badge bg-secondary">{{ $invoice->status }}</span>
                                @endif
                            </td>
                            <td>{{ $invoice->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.subscriptions.invoice', $invoice) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Aucune facture trouvée</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</div>
@endsection