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
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="preview" class="form-label mb-0">Aperçu du template</label>
                        <button type="button" id="previewBtn" class="btn btn-info btn-sm">
                            <i class="fas fa-eye me-1"></i> Prévisualiser
                        </button>
                    </div>
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
    const previewBtn = document.getElementById('previewBtn');

    contentInput.addEventListener('input', function() {
        preview.innerHTML = this.value;
    });
    
    // Ouvrir la prévisualisation complète dans une nouvelle fenêtre
    previewBtn.addEventListener('click', function() {
        // Récupérer les valeurs du formulaire
        const content = contentInput.value;
        const name = document.getElementById('name').value;
        
        // Créer une fenêtre temporaire avec le contenu
        const previewWindow = window.open('', '_blank', 'width=800,height=600');
        previewWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Prévisualisation: ${name || 'Template d\'email'}</title>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { padding: 20px; }
                    .email-container { max-width: 800px; margin: 0 auto; border: 1px solid #ddd; border-radius: 5px; overflow: hidden; }
                    .email-header { background-color: #f8f9fa; padding: 15px; border-bottom: 1px solid #ddd; }
                    .email-body { padding: 20px; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Prévisualisation du template</h2>
                        <button class="btn btn-secondary" onclick="window.close()">Fermer</button>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Ceci est une prévisualisation simple. Les variables ne sont pas remplacées.
                    </div>
                    <div class="email-container">
                        <div class="email-header">
                            <div><strong>De:</strong> Votre Application &lt;noreply@example.com&gt;</div>
                            <div><strong>À:</strong> Destinataire &lt;destinataire@example.com&gt;</div>
                            <div><strong>Sujet:</strong> ${name || 'Template d\'email'}</div>
                        </div>
                        <div class="email-body">
                            ${content}
                        </div>
                    </div>
                </div>
            </body>
            </html>
        `);
        previewWindow.document.close();
    });

});
</script>
@endpush
@endsection