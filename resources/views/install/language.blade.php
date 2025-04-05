@extends('install.layout')

@section('content')
    <div class="space-y-6">
        <h3 class="text-lg font-medium text-gray-900">
            {{ t('install.language_step') }}
        </h3>
        
        <p class="text-sm text-gray-600">
            {{ t('install.language_message') }}
        </p>

        <form method="POST" action="{{ route('install.language.process') }}">
            @csrf

            <div class="space-y-4">
                <!-- Sélection de la langue -->
                <div>
                    <label for="locale" class="block text-sm font-medium text-gray-700">{{ t('language.select') }}</label>
                    <select id="locale" name="locale" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        @foreach($localeNames as $code => $name)
                            <option value="{{ $code }}" {{ $code === app()->getLocale() ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ t('common.next') }}
                </button>
            </div>
        </form>
    </div>
@endsection