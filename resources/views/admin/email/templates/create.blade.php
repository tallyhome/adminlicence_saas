@extends('admin.layouts.app')

@section('title', 'Création d\'un template')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3">Création d'un template</h1>
                <div class="btn-group">
                    <a href="{{ route('admin.email.templates.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.email.templates.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nom du template</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                    id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="description" class="form-label">Description</label>
                                <input type="text" class="form-control @error('description') is-invalid @enderror" 
                                    id="description" name="description" value="{{ old('description') }}">
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Variables dynamiques -->
                        <div class="mb-3">
                            <label class="form-label">Variables disponibles</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="newVariable" placeholder="Nouvelle variable">
                                <button type="button" class="btn btn-primary" onclick="addVariable()">
                                    <i class="fas fa-plus me-2"></i> Ajouter
                                </button>
                            </div>
                            <div id="variablesList" class="mt-2">
                                @if(old('variables'))
                                    @foreach(old('variables') as $variable)
                                        <span class="badge bg-info me-2 mb-2">
                                            {{ $variable }}
                                            <i class="fas fa-times ms-1" onclick="removeVariable(this)"></i>
                                            <input type="hidden" name="variables[]" value="{{ $variable }}">
                                        </span>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- Contenu multilingue -->
                        <div class="mb-3">
                            <label class="form-label">Contenu par langue</label>
                            <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                @foreach($languages as $code => $name)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                                            id="{{ $code }}-tab" 
                                            data-bs-toggle="tab" 
                                            data-bs-target="#{{ $code }}-content" 
                                            type="button" 
                                            role="tab">
                                            {{ $name }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content mt-3" id="languageContent">
                                @foreach($languages as $code => $name)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                                        id="{{ $code }}-content" 
                                        role="tabpanel">
                                        <div class="mb-3">
                                            <label class="form-label">Sujet ({{ $name }})</label>
                                            <input type="text" 
                                                class="form-control @error('subject.' . $code) is-invalid @enderror" 
                                                name="subject[{{ $code }}]" 
                                                value="{{ old('subject.' . $code, '') }}">
                                            @error('subject.' . $code)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Contenu ({{ $name }})</label>
                                            <textarea class="form-control editor @error('content.' . $code) is-invalid @enderror" 
                                                name="content[{{ $code }}]" 
                                                rows="10">{{ old('content.' . $code, '') }}</textarea>
                                            @error('content.' . $code)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="preview" class="form-label mb-0">Aperçu du template</label>
                                <button type="button" id="previewBtn" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye me-2"></i> Prévisualiser
                                </button>
                            </div>
                            <div id="preview" class="border p-3 bg-light">
                                <!-- L'aperçu sera affiché ici -->
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Enregistrer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
// Initialisation des éditeurs
const editors = {};
document.querySelectorAll('.editor').forEach(element => {
    const editor = new Quill(element, {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['link', 'image'],
                ['clean']
            ]
        }
    });
    editors[element.getAttribute('name')] = editor;

    // Synchroniser le contenu avec le textarea
    editor.on('text-change', function() {
        element.value = editor.root.innerHTML;
    });
});

// Gestion des variables
function addVariable() {
    const input = document.getElementById('newVariable');
    const variable = input.value.trim();
    
    if (variable) {
        const variablesList = document.getElementById('variablesList');
        const badge = document.createElement('span');
        badge.className = 'badge bg-info me-2 mb-2';
        badge.innerHTML = `
            ${variable}
            <i class="fas fa-times ms-1" onclick="removeVariable(this)"></i>
            <input type="hidden" name="variables[]" value="${variable}">
        `;
        variablesList.appendChild(badge);
        input.value = '';
    }
}

function removeVariable(element) {
    element.parentElement.remove();
}

// Prévisualisation du template
document.getElementById('previewBtn').addEventListener('click', function() {
    const previewContainer = document.getElementById('preview');
    const activeTab = document.querySelector('.tab-pane.active');
    const contentField = activeTab.querySelector('.editor');
    const contentName = contentField.getAttribute('name');
    const editor = editors[contentName];
    
    if (editor) {
        previewContainer.innerHTML = editor.root.innerHTML;
    }
});

// Soumission du formulaire
document.querySelector('form').addEventListener('submit', function() {
    // Synchroniser tous les éditeurs avant la soumission
    Object.entries(editors).forEach(([name, editor]) => {
        const textarea = document.querySelector(`textarea[name="${name}"]`);
        textarea.value = editor.root.innerHTML;
    });
    
    // Convertir les variables en JSON
    const variables = [];
    document.querySelectorAll('input[name="variables[]"]').forEach(input => {
        variables.push(input.value);
    });
    
    // Ajouter un champ caché pour les variables en JSON
    const variablesInput = document.createElement('input');
    variablesInput.type = 'hidden';
    variablesInput.name = 'variables';
    variablesInput.value = JSON.stringify(variables);
    this.appendChild(variablesInput);
});
</script>
@endpush