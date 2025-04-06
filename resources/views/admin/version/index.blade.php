@extends('admin.layouts.app')

@section('title', __('Informations de version'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Version actuelle') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h2 class="mb-4">{{ $version['full'] }}</h2>
                            <p><strong>{{ __('Dernière mise à jour') }}:</strong> {{ $version['last_update'] }}</p>
                            <p>
                                <span class="badge bg-primary">Major: {{ $version['major'] }}</span>
                                <span class="badge bg-secondary">Minor: {{ $version['minor'] }}</span>
                                <span class="badge bg-info">Patch: {{ $version['patch'] }}</span>
                                @if($version['release'])
                                    <span class="badge bg-warning">{{ $version['release'] }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h5>{{ __('À propos des numéros de version') }}</h5>
                                <ul class="mb-0">
                                    <li><strong>Major</strong> - Changements majeurs/incompatibles</li>
                                    <li><strong>Minor</strong> - Nouvelles fonctionnalités compatibles</li>
                                    <li><strong>Patch</strong> - Corrections de bugs compatibles</li>
                                    <li><strong>Release</strong> - Suffixe de version (alpha, beta, rc, etc.)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Historique des versions') }}</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($history as $item)
                            <div class="timeline-item mb-5">
                                <div class="row">
                                    <div class="col-md-3">
                                        <h5>{{ $item['version'] }}</h5>
                                        <p class="text-muted">{{ $item['date'] }}</p>
                                    </div>
                                    <div class="col-md-9">
                                        <p><strong>{{ $item['description'] }}</strong></p>
                                        @if(isset($item['categories']))
                                            @if(isset($item['categories']['Ajouts']) && count($item['categories']['Ajouts']) > 0)
                                                <h6 class="mt-3 text-success">Ajouts</h6>
                                                <ul>
                                                    @foreach($item['categories']['Ajouts'] as $change)
                                                        <li>{{ $change }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                            
                                            @if(isset($item['categories']['Améliorations']) && count($item['categories']['Améliorations']) > 0)
                                                <h6 class="mt-3 text-primary">Améliorations</h6>
                                                <ul>
                                                    @foreach($item['categories']['Améliorations'] as $change)
                                                        <li>{{ $change }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                            
                                            @if(isset($item['categories']['Corrections de bugs']) && count($item['categories']['Corrections de bugs']) > 0)
                                                <h6 class="mt-3 text-danger">Corrections de bugs</h6>
                                                <ul>
                                                    @foreach($item['categories']['Corrections de bugs'] as $change)
                                                        <li>{{ $change }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection