@extends('admin.layouts.app')

@section('title', 'Configuration Mailgun')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3">Configuration Mailgun</h1>
                <a href="{{ route('admin.mail.providers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.mail.providers.mailgun.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="domain" class="form-label">Domaine</label>
                            <input type="text" class="form-control @error('domain') is-invalid @enderror" 
                                   id="domain" name="domain" value="{{ old('domain', $config['domain']) }}"
                                   placeholder="exemple.com">
                            @error('domain')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="secret" class="form-label">Clé API secrète</label>
                            <input type="password" class="form-control @error('secret') is-invalid @enderror" 
                                   id="secret" name="secret" value="{{ old('secret', $config['secret']) }}"
                                   placeholder="key-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                            @error('secret')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="endpoint" class="form-label">Point de terminaison API</label>
                            <input type="text" class="form-control @error('endpoint') is-invalid @enderror" 
                                   id="endpoint" name="endpoint" value="{{ old('endpoint', $config['endpoint']) }}"
                                   placeholder="api.mailgun.net">
                            @error('endpoint')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="scheme" class="form-label">Protocole</label>
                            <select class="form-select @error('scheme') is-invalid @enderror" 
                                    id="scheme" name="scheme">
                                <option value="https" {{ old('scheme', $config['scheme']) === 'https' ? 'selected' : '' }}>HTTPS</option>
                                <option value="http" {{ old('scheme', $config['scheme']) === 'http' ? 'selected' : '' }}>HTTP</option>
                            </select>
                            @error('scheme')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer
                            </button>
                            <button type="button" class="btn btn-info" onclick="testConnection()">
                                <i class="fas fa-vial me-2"></i>Tester la connexion
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Logs Mailgun</h5>
                    <p class="card-text">
                        Consultez les logs des événements Mailgun pour suivre vos envois d'emails.
                    </p>
                    <a href="{{ route('admin.mail.providers.mailgun.logs') }}" class="btn btn-secondary">
                        <i class="fas fa-list me-2"></i>Voir les logs
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function testConnection() {
    const button = event.target;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Test en cours...';

    fetch('{{ route("admin.mail.providers.mailgun.test") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Succès : ' + data.message);
        } else {
            alert('Erreur : ' + data.message);
        }
    })
    .catch(error => {
        alert('Erreur lors du test : ' + error.message);
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalText;
    });
}
</script>
@endpush