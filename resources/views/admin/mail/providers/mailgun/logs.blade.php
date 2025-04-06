@extends('admin.layouts.app')

@section('title', 'Logs Mailgun')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3">Logs Mailgun</h1>
                <a href="{{ route('admin.mail.providers.mailgun.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Événement</th>
                                    <th>Destinataire</th>
                                    <th>Sujet</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($events as $event)
                                <tr>
                                    <td>{{ \Carbon\Carbon::createFromTimestamp($event->getTimestamp())->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ $event->getEvent() }}</td>
                                    <td>{{ $event->getRecipient() }}</td>
                                    <td>{{ $event->getMessage()->getHeaders()['subject'] ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            $status = $event->getDeliveryStatus()['message'] ?? null;
                                            $badgeClass = match($event->getEvent()) {
                                                'delivered' => 'bg-success',
                                                'failed' => 'bg-danger',
                                                'bounced' => 'bg-warning',
                                                'complained' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            {{ $status ?? $event->getEvent() }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Aucun événement trouvé</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 