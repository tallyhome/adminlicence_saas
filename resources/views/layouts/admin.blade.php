<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AdminLicence') }} - @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('css/custom-pagination.css') }}">
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('admin.dashboard') }}" class="font-bold text-xl text-indigo-600">
                                AdminLicence
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.dashboard') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                @php
                                    $translationFile = resource_path('locales/' . app()->getLocale() . '/translation.json');
                                    $translations = file_exists($translationFile) ? json_decode(file_get_contents($translationFile), true) : [];
                                @endphp
                                {{ $translations['common']['dashboard'] ?? 'Tableau de bord' }}
                            </a>
                            <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.projects.*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                {{ $translations['common']['projects'] ?? 'Projets' }}
                            </a>
                            <a href="{{ route('admin.serial-keys.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.serial-keys.*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                {{ $translations['common']['serial_keys'] ?? 'Clés de série' }}
                            </a>
                            <a href="{{ route('admin.client-example') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.client-example') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                {{ $translations['common']['api_documentation'] ?? 'Documentation API' }}
                            </a>
                            <a href="{{ route('admin.mail.settings') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.mail.*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                {{ $translations['common']['email'] ?? 'Email' }}
                            </a>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <!-- Language Selector -->
                        <div class="mr-4 relative" x-data="{ open: false }">
                            <div>
                                <button @click="open = !open" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out">
                                    <span class="text-gray-700">{{ strtoupper(app()->getLocale()) }}</span>
                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>

                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg">
                                <div class="py-1 rounded-md bg-white shadow-xs">
                                    <a href="?lang=en" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out {{ app()->getLocale() == 'en' ? 'bg-gray-100' : '' }}">
                                        {{ $translations['language']['en'] ?? 'English' }}
                                    </a>
                                    <a href="?lang=fr" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out {{ app()->getLocale() == 'fr' ? 'bg-gray-100' : '' }}">
                                        {{ $translations['language']['fr'] ?? 'Français' }}
                                    </a>
                                    <a href="?lang=es" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out {{ app()->getLocale() == 'es' ? 'bg-gray-100' : '' }}">
                                        {{ $translations['language']['es'] ?? 'Español' }}
                                    </a>
                                    <a href="?lang=de" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out {{ app()->getLocale() == 'de' ? 'bg-gray-100' : '' }}">
                                        {{ $translations['language']['de'] ?? 'Deutsch' }}
                                    </a>
                                    <a href="?lang=it" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out {{ app()->getLocale() == 'it' ? 'bg-gray-100' : '' }}">
                                        {{ $translations['language']['it'] ?? 'Italiano' }}
                                    </a>
                                    <a href="?lang=pt" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out {{ app()->getLocale() == 'pt' ? 'bg-gray-100' : '' }}">
                                        {{ $translations['language']['pt'] ?? 'Português' }}
                                    </a>
                                    <a href="?lang=nl" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out {{ app()->getLocale() == 'nl' ? 'bg-gray-100' : '' }}">
                                        {{ $translations['language']['nl'] ?? 'Nederlands' }}
                                    </a>
                                    <a href="?lang=ru" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out {{ app()->getLocale() == 'ru' ? 'bg-gray-100' : '' }}">
                                        {{ $translations['language']['ru'] ?? 'Русский' }}
                                    </a>
                                    <a href="?lang=zh" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out {{ app()->getLocale() == 'zh' ? 'bg-gray-100' : '' }}">
                                        {{ $translations['language']['zh'] ?? '中文' }}
                                    </a>
                                    <a href="?lang=ja" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out {{ app()->getLocale() == 'ja' ? 'bg-gray-100' : '' }}">
                                        {{ $translations['language']['ja'] ?? '日本語' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Menu -->
                        <div class="ml-3 relative" x-data="{ open: false }">
                            <div>
                                <button @click="open = !open" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out">
                                    <span class="text-gray-700">{{ Auth::user()->name }}</span>
                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>

                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg">
                                <div class="py-1 rounded-md bg-white shadow-xs">
                                    <form method="POST" action="{{ route('admin.logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                            {{ $translations['common']['logout'] ?? 'Déconnexion' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out" x-data="{ open: false }">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-semibold text-gray-800">@yield('header')</h1>
            </div>
        </header>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <main>
            <div class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100 py-4 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <div>
                        &copy; {{ date('Y') }} {{ config('app.name') }}
                    </div>
                    <div>
                        <a href="{{ route('admin.version') }}" class="text-gray-600 hover:text-gray-900">
                            Version {{ config('version.full')() }}
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>