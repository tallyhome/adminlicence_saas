@extends('install.layout')

@section('content')
    <div class="space-y-6 text-center">
        <div class="flex justify-center">
            <svg class="h-16 w-16 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        
        <h3 class="text-lg font-medium text-gray-900">
            {{ t('install.complete_step') }}
        </h3>
        
        <p class="text-sm text-gray-600">
            {{ t('install.complete_message') }}
        </p>

        <div class="mt-6">
            <a href="{{ route('admin.login') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                {{ t('install.go_to_login') }}
            </a>
        </div>
    </div>
@endsection