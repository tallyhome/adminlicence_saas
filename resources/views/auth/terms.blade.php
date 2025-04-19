@extends('layouts.auth')

@section('title', 'Conditions d\'utilisation')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">{{ $page->title ?? __('Conditions d\'utilisation') }}</h4>
                </div>

                <div class="card-body">
                    <h5 class="mb-4">Dernière mise à jour : {{ isset($page) ? $page->updated_at->format('d/m/Y') : now()->format('d/m/Y') }}</h5>

                    @if(isset($page))
                        {!! $page->content !!}
                    @else
                        <div class="alert alert-info">
                            Les conditions d'utilisation n'ont pas encore été configurées. Veuillez contacter l'administrateur.
                        </div>
                    @endif
                </div>

                <div class="card-footer text-center">
                    <a href="{{ url()->previous() == route('terms') ? route('register') : url()->previous() }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
