<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AdminLicence') }} - Récupération 2FA</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-6 text-center">
                <h1 class="text-2xl font-bold text-indigo-600">AdminLicence</h1>
                <p class="text-gray-600">Récupération de compte</p>
            </div>

            @if ($errors->any())
                <div class="mb-4">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        @foreach ($errors->all() as $error)
                            <span class="block sm:inline">{{ $error }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mb-4 text-sm text-gray-600">
                <p>Veuillez saisir l'un de vos codes de récupération pour accéder à votre compte.</p>
            </div>

            <form method="POST" action="{{ route('admin.2fa.recovery') }}">
                @csrf

                <!-- Code de récupération -->
                <div class="mb-4">
                    <label for="recovery_code" class="block text-sm font-medium text-gray-700">Code de récupération</label>
                    <input id="recovery_code" type="text" name="recovery_code" required autofocus
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="flex items-center justify-end mt-4">
                    <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Se connecter
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.2fa.verify') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                    Retour à la vérification par code
                </a>
            </div>
        </div>
    </div>
</body>
</html>