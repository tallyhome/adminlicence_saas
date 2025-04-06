@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1>{{ __('Documentation des licences') }}</h1>
                <div class="language-selector">
                    <select class="form-select" onchange="window.location.href = '{{ route('admin.set.language') }}?lang=' + this.value">
                        @foreach($availableLanguages as $code => $name)
                            <option value="{{ $code }}" {{ $currentLanguage === $code ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h2>{{ __('Guide d\'intégration des licences') }}</h2>
                    <p>{{ __('Cette documentation vous guidera à travers le processus d\'intégration du système de licence dans votre application.') }}</p>
                    
                    <!-- Section Installation -->
                    <section class="mb-4">
                        <h3>{{ __('Installation') }}</h3>
                        <p>{{ __('Instructions détaillées pour l\'installation et la configuration initiale.') }}</p>
                    </section>

                    <!-- Section Vérification -->
                    <section class="mb-4">
                        <h3>{{ __('Vérification des licences') }}</h3>
                        <p>{{ __('Comment implémenter la vérification des licences dans votre application.') }}</p>
                    </section>

                    <!-- Section API -->
                    <section class="mb-4">
                        <h3>{{ __('API de gestion des licences') }}</h3>
                        <p>{{ __('Documentation complète de l\'API de gestion des licences.') }}</p>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection