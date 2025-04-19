@extends('admin.layouts.app')

@section('title', 'Modifier la page légale')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<style>
    .note-editor {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .note-editor .note-toolbar {
        background-color: #f8f9fa;
        border-bottom: 1px solid #ced4da;
    }
    .note-statusbar {
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">
        @if($type === 'terms')
            Modifier les Conditions Générales d'Utilisation
        @else
            Modifier la Politique de Confidentialité
        @endif
    </h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.legal.index') }}">Pages légales</a></li>
        <li class="breadcrumb-item active">
            @if($type === 'terms')
                Conditions d'utilisation
            @else
                Politique de confidentialité
            @endif
        </li>
    </ol>

    <div class="card shadow-lg mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Formulaire d'édition</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.legal.update', $type) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="title" class="form-label">Titre</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $page->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Contenu</label>
                    <textarea id="content" name="content" class="form-control @error('content') is-invalid @enderror" rows="20">{{ old('content', $page->content) }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> 
                    @if($type === 'terms')
                        Les conditions générales d'utilisation définissent les règles et obligations pour l'utilisation de votre service. Assurez-vous qu'elles sont complètes et conformes aux lois en vigueur.
                    @else
                        La politique de confidentialité explique comment vous collectez, utilisez et protégez les données des utilisateurs. Elle doit être conforme au RGPD et autres réglementations sur la protection des données.
                    @endif
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.legal.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Retour
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Prévisualisation</h5>
        </div>
        <div class="card-body">
            <div class="border p-4 rounded bg-light">
                <h2 id="preview-title">{{ $page->title }}</h2>
                <hr>
                <div id="preview-content">{!! $page->content !!}</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#content').summernote({
            height: 500,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onChange: function(contents) {
                    $('#preview-content').html(contents);
                }
            }
        });

        // Mise à jour du titre en temps réel
        $('#title').on('input', function() {
            $('#preview-title').text($(this).val());
        });
    });
</script>
@endsection
