@extends('layouts.app')

@section('title', $page->title)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h3 mb-0">{{ $page->title }}</h1>
                </div>
                <div class="card-body p-4">
                    <div class="legal-content">
                        {!! $page->content !!}
                    </div>
                    
                    <div class="text-muted mt-4">
                        <small>Dernière mise à jour: {{ $page->updated_at->format('d/m/Y') }}</small>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .legal-content h1, .legal-content h2 {
        color: #0d6efd;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .legal-content h3, .legal-content h4 {
        color: #495057;
        margin-top: 1.25rem;
        margin-bottom: 0.75rem;
    }
    
    .legal-content p {
        margin-bottom: 1rem;
        line-height: 1.6;
    }
    
    .legal-content ul, .legal-content ol {
        margin-bottom: 1rem;
        padding-left: 2rem;
    }
    
    .legal-content li {
        margin-bottom: 0.5rem;
    }
</style>
@endsection
