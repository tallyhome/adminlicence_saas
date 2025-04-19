@extends('layouts.auth')

@section('title', 'Politique de confidentialité')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">{{ $page->title ?? __('Politique de confidentialité') }}</h4>
                </div>

                <div class="card-body">
                    <h5 class="mb-4">Dernière mise à jour : {{ isset($page) ? $page->updated_at->format('d/m/Y') : now()->format('d/m/Y') }}</h5>

                    @if(isset($page))
                        {!! $page->content !!}
                    @else
                        <div class="alert alert-info">
                            La politique de confidentialité n'a pas encore été configurée. Veuillez contacter l'administrateur.
                        </div>
                    @endif
                </div>

                <div class="card-footer text-center">
                    <a href="{{ url()->previous() == route('privacy') ? route('register') : url()->previous() }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
