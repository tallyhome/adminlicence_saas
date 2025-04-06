@extends('layouts.admin')

@section('title', 'Gestion des templates d\'email')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestion des templates d'email</h1>
    
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
            <i class="fas fa-envelope-open-text me-1"></i>
            Créer/Modifier un template
        </div>
        <div class="card-body">
            <form action="{{ route('admin.email.templates.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Nom du template</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Contenu du template</label>
                    <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                    <small class="form-text text-muted">
                        Utilisez les variables dynamiques sous la forme {{variable}}. Par exemple: {{nom}}, {{email}}, etc.
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Variables disponibles</label>
                    <div class="variables-container">
                        <div class="variable-group mb-2">
                            <input type="text" class="form-control" name="variables[]" placeholder="Nom de la variable">
                            <button type="button" class="btn btn-sm btn-outline-secondary add-variable">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="preview" class="form-label">Aperçu du template</label>
                    <div id="preview" class="border p-3 bg-light">
                        <!-- L'aperçu sera affiché ici -->
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Enregistrer le template</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-list me-1"></i>
            Templates existants
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Variables</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Les templates seront listés ici -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter une nouvelle variable
    document.querySelector('.add-variable').addEventListener('click', function() {
        const container = document.querySelector('.variables-container');
        const newGroup = document.createElement('div');
        newGroup.className = 'variable-group mb-2';
        newGroup.innerHTML = `
            <div class="d-flex">
                <input type="text" class="form-control" name="variables[]" placeholder="Nom de la variable">
                <button type="button" class="btn btn-sm btn-outline-danger ms-2 remove-variable">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        `;
        container.appendChild(newGroup);

        // Ajouter l'événement de suppression
        newGroup.querySelector('.remove-variable').addEventListener('click', function() {
            newGroup.remove();
        });
    });

    // Prévisualisation du template
    const contentInput = document.getElementById('content');
    const preview = document.getElementById('preview');

    contentInput.addEventListener('input', function() {
        preview.innerHTML = this.value;
    });
});
</script>
@endpush
@endsection