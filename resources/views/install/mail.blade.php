@extends('install.layout')

@section('content')
    <div class="space-y-6">
        <h3 class="text-lg font-medium text-gray-900">
            {{ t('install.mail_step') }}
        </h3>
        
        <p class="text-sm text-gray-600">
            {{ t('install.mail_message') }}
        </p>

        <form method="POST" action="{{ route('install.mail.process') }}">
            @csrf

            <div class="space-y-4">
                <!-- Driver de messagerie -->
                <div>
                    <label for="mail_driver" class="block text-sm font-medium text-gray-700">{{ t('install.mail_driver') }}</label>
                    <select id="mail_driver" name="mail_driver" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" x-data="{ driver: '{{ old('mail_driver', 'smtp') }}' }" x-model="driver">
                        <option value="smtp">SMTP</option>
                        <option value="sendmail">Sendmail</option>
                        <option value="mailgun">Mailgun</option>
                        <option value="ses">Amazon SES</option>
                        <option value="postmark">Postmark</option>
                        <option value="log">Log</option>
                        <option value="array">Array</option>
                    </select>
                </div>

                <!-- Champs conditionnels pour SMTP -->
                <div x-data="{ driver: '{{ old('mail_driver', 'smtp') }}' }" x-show="driver === 'smtp'">
                    <!-- Hôte -->
                    <div class="mt-4">
                        <label for="mail_host" class="block text-sm font-medium text-gray-700">{{ t('install.mail_host') }}</label>
                        <input type="text" name="mail_host" id="mail_host" value="{{ old('mail_host', 'smtp.mailtrap.io') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <!-- Port -->
                    <div class="mt-4">
                        <label for="mail_port" class="block text-sm font-medium text-gray-700">{{ t('install.mail_port') }}</label>
                        <input type="text" name="mail_port" id="mail_port" value="{{ old('mail_port', '2525') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <!-- Nom d'utilisateur -->
                    <div class="mt-4">
                        <label for="mail_username" class="block text-sm font-medium text-gray-700">{{ t('install.mail_username') }}</label>
                        <input type="text" name="mail_username" id="mail_username" value="{{ old('mail_username') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <!-- Mot de passe -->
                    <div class="mt-4">
                        <label for="mail_password" class="block text-sm font-medium text-gray-700">{{ t('install.mail_password') }}</label>
                        <input type="password" name="mail_password" id="mail_password" value="{{ old('mail_password') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <!-- Chiffrement -->
                    <div class="mt-4">
                        <label for="mail_encryption" class="block text-sm font-medium text-gray-700">{{ t('install.mail_encryption') }}</label>
                        <select id="mail_encryption" name="mail_encryption" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="null">{{ t('common.none') }}</option>
                            <option value="tls">TLS</option>
                            <option value="ssl">SSL</option>
                        </select>
                    </div>
                </div>

                <!-- Adresse d'expédition -->
                <div class="mt-4">
                    <label for="mail_from_address" class="block text-sm font-medium text-gray-700">{{ t('install.mail_from_address') }}</label>
                    <input type="email" name="mail_from_address" id="mail_from_address" value="{{ old('mail_from_address', 'admin@example.com') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <!-- Nom d'expédition -->
                <div class="mt-4">
                    <label for="mail_from_name" class="block text-sm font-medium text-gray-700">{{ t('install.mail_from_name') }}</label>
                    <input type="text" name="mail_from_name" id="mail_from_name" value="{{ old('mail_from_name', 'AdminLicence') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
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