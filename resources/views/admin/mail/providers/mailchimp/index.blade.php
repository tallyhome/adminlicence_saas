@extends('admin.layouts.app')

@section('title', 'Configuration Mailchimp')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Configuration Mailchimp</h1>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.mail.providers.mailchimp.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="api_key" class="form-label">Clé API</label>
                    <input type="text" class="form-control" id="api_key" name="api_key" value="{{ old('api_key', $config->api_key ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label for="list_id" class="form-label">ID de la liste</label>
                    <input type="text" class="form-control" id="list_id" name="list_id" value="{{ old('list_id', $config->list_id ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label for="from_email" class="form-label">Email d'expédition</label>
                    <input type="email" class="form-control" id="from_email" name="from_email" value="{{ old('from_email', $config->from_email ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label for="from_name" class="form-label">Nom d'expéditeur</label>
                    <input type="text" class="form-control" id="from_name" name="from_name" value="{{ old('from_name', $config->from_name ?? '') }}" required>
                </div>

                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <button type="button" class="btn btn-secondary" onclick="testConnection()">Tester la connexion</button>
                <button type="button" class="btn btn-info" onclick="syncLists()">Synchroniser les listes</button>
                <button type="button" class="btn btn-info" onclick="syncTemplates()">Synchroniser les templates</button>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-envelope me-1"></i>
            Campagnes
        </div>
        <div class="card-body">
            <div id="campaigns-container">
                <!-- Les campagnes seront chargées ici dynamiquement -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function testConnection() {
    fetch('{{ route("admin.mail.providers.mailchimp.test") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert('Erreur : ' + data.error);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert('Erreur lors du test de connexion');
    });
}

function syncLists() {
    fetch('{{ route("admin.mail.providers.mailchimp.sync-lists") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert('Erreur : ' + data.error);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert('Erreur lors de la synchronisation des listes');
    });
}

function syncTemplates() {
    fetch('{{ route("admin.mail.providers.mailchimp.sync-templates") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert('Erreur : ' + data.error);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert('Erreur lors de la synchronisation des templates');
    });
}
</script>
@endpush
@endsection