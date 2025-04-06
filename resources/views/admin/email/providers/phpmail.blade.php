@extends('admin.layouts.app')

@section('title', 'Configuration PHPMail')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">Configuration PHPMail</h1>
        </div>
    </div>

    <div class="row">
        <!-- Configuration SMTP -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Paramètres SMTP</h5>
                    <form action="{{ route('admin.email.providers.phpmail.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="host" class="form-label">Serveur SMTP</label>
                                <input type="text" class="form-control @error('host') is-invalid @enderror" 
                                    id="host" name="host" value="{{ old('host', $config->host ?? '') }}">
                                @error('host')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="port" class="form-label">Port</label>
                                <input type="number" class="form-control @error('port') is-invalid @enderror" 
                                    id="port" name="port" value="{{ old('port', $config->port ?? '') }}">
                                @error('port')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="username" class="form-label">Nom d'utilisateur</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                    id="username" name="username" value="{{ old('username', $config->username ?? '') }}">
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                    id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="encryption" class="form-label">Chiffrement</label>
                                <select class="form-select @error('encryption') is-invalid @enderror" 
                                    id="encryption" name="encryption">
                                    <option value="tls" {{ old('encryption', $config->encryption ?? '') === 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ old('encryption', $config->encryption ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="none" {{ old('encryption', $config->encryption ?? '') === 'none' ? 'selected' : '' }}>Aucun</option>
                                </select>
                                @error('encryption')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="from_address" class="form-label">Adresse d'expédition</label>
                                <input type="email" class="form-control @error('from_address') is-invalid @enderror" 
                                    id="from_address" name="from_address" value="{{ old('from_address', $config->from_address ?? '') }}">
                                @error('from_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="from_name" class="form-label">Nom d'expédition</label>
                            <input type="text" class="form-control @error('from_name') is-invalid @enderror" 
                                id="from_name" name="from_name" value="{{ old('from_name', $config->from_name ?? '') }}">
                            @error('from_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Enregistrer
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="testConnection()">
                                <i class="fas fa-vial me-2"></i> Tester la connexion
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Statistiques d'envoi</h5>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Emails envoyés aujourd'hui
                            <span class="badge bg-primary rounded-pill">{{ $stats->today_count ?? 0 }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Emails envoyés ce mois
                            <span class="badge bg-primary rounded-pill">{{ $stats->month_count ?? 0 }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Taux de succès
                            <span class="badge bg-success rounded-pill">{{ $stats->success_rate ?? '0%' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logs d'envoi -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Logs d'envoi</h5>
                        <form action="{{ route('admin.email.providers.phpmail.logs.clear') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash me-2"></i> Nettoyer les logs
                            </button>
                        </form>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Destinataire</th>
                                    <th>Sujet</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs ?? [] as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $log->recipient }}</td>
                                    <td>{{ $log->subject }}</td>
                                    <td>
                                        @if($log->status === 'success')
                                            <span class="badge bg-success">Succès</span>
                                        @else
                                            <span class="badge bg-danger">Échec</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Aucun log disponible</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(isset($logs) && $logs->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $logs->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function testConnection() {
    fetch('{{ route("admin.email.providers.phpmail.test") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('La connexion a été établie avec succès !');
        } else {
            alert('Erreur lors du test de connexion : ' + data.message);
        }
    })
    .catch(error => {
        alert('Une erreur est survenue lors du test');
        console.error('Erreur:', error);
    });
}
</script>
@endpush