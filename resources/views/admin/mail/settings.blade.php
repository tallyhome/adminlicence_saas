@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Configuration des Emails</h1>
    
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

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-envelope me-1"></i>
            Paramètres SMTP
        </div>
        <div class="card-body">
            <form action="{{ route('admin.mail.settings.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="mailer">Type de Mailer</label>
                            <select name="mailer" id="mailer" class="form-control @error('mailer') is-invalid @enderror" required>
                                <option value="smtp" {{ $mailConfig->mailer === 'smtp' ? 'selected' : '' }}>SMTP</option>
                                <option value="sendmail" {{ $mailConfig->mailer === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                            </select>
                            @error('mailer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="host">Serveur SMTP</label>
                            <input type="text" name="host" id="host" class="form-control @error('host') is-invalid @enderror" 
                                value="{{ old('host', $mailConfig->host) }}" required>
                            @error('host')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="port">Port SMTP</label>
                            <input type="number" name="port" id="port" class="form-control @error('port') is-invalid @enderror" 
                                value="{{ old('port', $mailConfig->port) }}" required>
                            @error('port')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="encryption">Chiffrement</label>
                            <select name="encryption" id="encryption" class="form-control @error('encryption') is-invalid @enderror" required>
                                <option value="tls" {{ $mailConfig->encryption === 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ $mailConfig->encryption === 'ssl' ? 'selected' : '' }}>SSL</option>
                            </select>
                            @error('encryption')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="username">Nom d'utilisateur SMTP</label>
                            <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" 
                                value="{{ old('username', $mailConfig->username) }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="password">Mot de passe SMTP</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" 
                                value="{{ old('password', $mailConfig->password) }}" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="from_address">Adresse d'expédition</label>
                            <input type="email" name="from_address" id="from_address" class="form-control @error('from_address') is-invalid @enderror" 
                                value="{{ old('from_address', $mailConfig->from_address) }}" required>
                            @error('from_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="from_name">Nom d'expédition</label>
                            <input type="text" name="from_name" id="from_name" class="form-control @error('from_name') is-invalid @enderror" 
                                value="{{ old('from_name', $mailConfig->from_name) }}" required>
                            @error('from_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="template_name">Nom du modèle</label>
                    <input type="text" name="template_name" id="template_name" class="form-control @error('template_name') is-invalid @enderror" 
                        value="{{ old('template_name', $mailConfig->template_name) }}" required>
                    @error('template_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="template_content">Contenu du modèle</label>
                    <textarea name="template_content" id="template_content" rows="5" 
                        class="form-control @error('template_content') is-invalid @enderror" required
                    >{{ old('template_content', $mailConfig->template_content) }}</textarea>
                    @error('template_content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Enregistrer la configuration</button>
                    <form action="{{ route('admin.mail.test') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-secondary">Envoyer un email de test</button>
                    </form>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection