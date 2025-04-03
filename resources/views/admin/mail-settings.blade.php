@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-6">Configuration des Emails</h1>

        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Paramètres Actuels</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-gray-50 rounded">
                    <p class="font-medium">Driver de messagerie :</p>
                    <p class="text-gray-600">{{ config('mail.default') }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded">
                    <p class="font-medium">Adresse d'envoi :</p>
                    <p class="text-gray-600">{{ config('mail.from.address') }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded">
                    <p class="font-medium">Nom d'envoi :</p>
                    <p class="text-gray-600">{{ config('mail.from.name') }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded">
                    <p class="font-medium">Hôte SMTP :</p>
                    <p class="text-gray-600">{{ config('mail.mailers.smtp.host') }}</p>
                </div>
            </div>
        </div>

        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Comment configurer</h2>
            <div class="prose max-w-none">
                @if (session('success'))
                    <div class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('admin.mail.settings.store') }}" method="POST" class="mt-6 space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Configuration SMTP</h2>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="mailer" class="block text-sm font-medium text-gray-700">Type de Mailer</label>
                                    <select name="mailer" id="mailer" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="smtp" {{ $mailConfig->mailer === 'smtp' ? 'selected' : '' }}>SMTP</option>
                                        <option value="sendmail" {{ $mailConfig->mailer === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="host" class="block text-sm font-medium text-gray-700">Serveur SMTP</label>
                                    <input type="text" name="host" id="host" value="{{ $mailConfig->host }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label for="port" class="block text-sm font-medium text-gray-700">Port</label>
                                    <input type="number" name="port" id="port" value="{{ $mailConfig->port }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label for="encryption" class="block text-sm font-medium text-gray-700">Chiffrement</label>
                                    <select name="encryption" id="encryption" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="tls" {{ $mailConfig->encryption === 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ $mailConfig->encryption === 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="" {{ $mailConfig->encryption === null ? 'selected' : '' }}>Aucun</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="username" class="block text-sm font-medium text-gray-700">Nom d'utilisateur</label>
                                    <input type="text" name="username" id="username" value="{{ $mailConfig->username }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                                    <input type="password" name="password" id="password" value="{{ $mailConfig->password }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>

                        <div>
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Paramètres d'envoi</h2>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="from_address" class="block text-sm font-medium text-gray-700">Adresse d'expédition</label>
                                    <input type="email" name="from_address" id="from_address" value="{{ $mailConfig->from_address }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label for="from_name" class="block text-sm font-medium text-gray-700">Nom d'expédition</label>
                                    <input type="text" name="from_name" id="from_name" value="{{ $mailConfig->from_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mt-8">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Template d'email</h3>
                                    
                                    <div>
                                        <label for="template_name" class="block text-sm font-medium text-gray-700">Nom du template</label>
                                        <input type="text" name="template_name" id="template_name" value="{{ $mailConfig->template_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div class="mt-4">
                                        <label for="template_content" class="block text-sm font-medium text-gray-700">Contenu du template</label>
                                        <textarea name="template_content" id="template_content" rows="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $mailConfig->template_content }}</textarea>
                                        <p class="mt-2 text-sm text-gray-500">Utilisez @{{ variable }} pour les variables dynamiques</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Enregistrer la configuration
                        </button>
                    </div>
                </form>

                <div class="mt-8">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Test d'envoi d'email</h2>
                    <form action="{{ route('admin.mail.test') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="test_email" class="block text-sm font-medium text-gray-700">Email de test</label>
                            <input type="email" name="test_email" id="test_email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="exemple@domaine.com">
                        </div>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            Envoyer un email de test
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection