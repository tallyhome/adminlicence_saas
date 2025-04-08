@extends('admin.layouts.app')

@section('title', 'Documentation SaaS multiutilisateur')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1>{{ __('Documentation SaaS multiutilisateur') }}</h1>
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
                    <div class="markdown-content">
                        @if(!empty($content))
                            <div id="markdown-content">
                                {!! Illuminate\Support\Str::markdown($content) !!}
                            </div>
                        @else
                            <div class="alert alert-warning">
                                {{ __('La documentation n\'est pas disponible pour le moment.') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .markdown-content h1 { font-size: 2rem; margin-bottom: 1rem; }
    .markdown-content h2 { font-size: 1.75rem; margin-top: 2rem; margin-bottom: 1rem; }
    .markdown-content h3 { font-size: 1.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; }
    .markdown-content h4 { font-size: 1.25rem; margin-top: 1.25rem; margin-bottom: 0.5rem; }
    .markdown-content p { margin-bottom: 1rem; }
    .markdown-content ul, .markdown-content ol { margin-bottom: 1rem; padding-left: 2rem; }
    .markdown-content table { width: 100%; margin-bottom: 1rem; border-collapse: collapse; }
    .markdown-content table th, .markdown-content table td { padding: 0.5rem; border: 1px solid #dee2e6; }
    .markdown-content pre { background-color: #f8f9fa; padding: 1rem; border-radius: 0.25rem; margin-bottom: 1rem; overflow-x: auto; }
    .markdown-content code { background-color: #f8f9fa; padding: 0.2rem 0.4rem; border-radius: 0.25rem; font-size: 0.875em; }
    .markdown-content pre code { padding: 0; background-color: transparent; }
</style>
@endpush