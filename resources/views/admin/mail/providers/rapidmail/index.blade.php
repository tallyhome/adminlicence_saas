@extends('layouts.admin')

@extends('admin.layouts.app')

@section('title', $pageTitle)

@section('content')
<div class="container-fluid px-4">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb" class="mt-4">
        <ol class="breadcrumb">
            @foreach($breadcrumbs as $breadcrumb)
                @if($loop->last)
                    <li class="breadcrumb-item active">{{ $breadcrumb['name'] }}</li>
                @else
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a></li>
                @endif
            @endforeach
        </ol>
    </nav>

    <h1 class="mt-4">{{ $pageTitle }}</h1>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.mail.providers.rapidmail.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="api_key" class="form-label">Clé API</label>
                    <input type="text" class="form-control" id="api_key" name="api_key" value="{{ old('api_key', $config->api_key ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Nom d'utilisateur</label>
                    <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $config->username ?? '') }}" required>
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
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-list me-1"></i>
            Listes de destinataires
        </div>
        <div class="card-body">
            <div id="lists-container">
                <!-- Les listes seront chargées ici dynamiquement -->
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-envelope me-1"></i>
            Mailings
        </div>
        <div class="card-body">
            <div id="mailings-container">
                <!-- Les mailings seront chargés ici dynamiquement -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function testConnection() {
    fetch('{{ route("admin.mail.providers.rapidmail.test") }}', {
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

function loadLists() {
    fetch('{{ route("admin.mail.providers.rapidmail.lists") }}')
    .then(response => response.text())
    .then(html => {
        document.getElementById('lists-container').innerHTML = html;
    })
    .catch(error => {
        console.error('Erreur lors du chargement des listes');
    });
}

function loadMailings() {
    fetch('{{ route("admin.mail.providers.rapidmail.mailings") }}')
    .then(response => response.text())
    .then(html => {
        document.getElementById('mailings-container').innerHTML = html;
    })
    .catch(error => {
        console.error('Erreur lors du chargement des mailings');
    });
}

// Charger les listes et mailings au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    loadLists();
    loadMailings();
});
</script>
@endpush
@endsection