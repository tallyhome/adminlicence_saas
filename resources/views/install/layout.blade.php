<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ t('install.title') }} - {{ config('app.name', 'AdminLicence') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                {{ t('install.title') }}
            </h2>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Étapes d'installation -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    @php
                        $steps = [
                            'welcome' => t('install.welcome'),
                            'database' => t('install.database_step'),
                            'language' => t('install.language_step'),
                            'mail' => t('install.mail_step'),
                            'admin' => t('install.admin_step'),
                            'complete' => t('install.complete_step')
                        ];
                        
                        $currentStep = request()->route()->getName();
                        $currentStep = str_replace(['install.', '.process'], '', $currentStep);
                        
                        $stepIndex = array_search($currentStep, array_keys($steps));
                        $progress = ($stepIndex / (count($steps) - 1)) * 100;
                    @endphp
                    
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
                
                <div class="mt-2 text-center text-sm text-gray-600">
                    {{ $steps[$currentStep] ?? '' }}
                </div>
            </div>

            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md text-center text-sm text-gray-600">
            <div class="flex justify-between items-center">
                <div>
                    @if ($currentStep !== 'welcome')
                        @php
                            $previousStep = array_keys($steps)[$stepIndex - 1] ?? 'welcome';
                        @endphp
                        <a href="{{ route('install.' . $previousStep) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ t('common.previous') }}
                        </a>
                    @endif
                </div>
                <div>
                    <span>{{ config('app.name', 'AdminLicence') }} v{{ config('version.number', '1.0.0') }}</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>