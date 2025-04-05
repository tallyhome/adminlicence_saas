<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\MailConfig;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class InstallController extends Controller
{
    /**
     * Le service de traduction
     *
     * @var TranslationService
     */
    protected $translationService;

    /**
     * Constructeur
     *
     * @param TranslationService $translationService
     */
    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    /**
     * Afficher la page d'accueil du wizard d'installation
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Vérifier si l'application est déjà installée
        if ($this->isInstalled()) {
            return redirect('/')->with('error', t('install.already_installed'));
        }

        return view('install.welcome');
    }

    /**
     * Étape 1: Configuration de la base de données
     *
     * @return \Illuminate\View\View
     */
    public function database()
    {
        // Vérifier si l'application est déjà installée
        if ($this->isInstalled()) {
            return redirect('/')->with('error', t('install.already_installed'));
        }

        return view('install.database');
    }

    /**
     * Traiter la configuration de la base de données
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processDatabaseConfig(Request $request)
    {
        // Vérifier si l'application est déjà installée
        if ($this->isInstalled()) {
            return redirect('/')->with('error', t('install.already_installed'));
        }

        // Valider les données
        $validator = Validator::make($request->all(), [
            'db_connection' => 'required|in:mysql,pgsql,sqlite',
            'db_host' => 'required_unless:db_connection,sqlite',
            'db_port' => 'required_unless:db_connection,sqlite|numeric',
            'db_database' => 'required',
            'db_username' => 'required_unless:db_connection,sqlite',
            'db_password' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Tester la connexion à la base de données
        try {
            $connection = $request->input('db_connection');
            $config = [];

            if ($connection === 'sqlite') {
                $database = $request->input('db_database');
                $databasePath = database_path($database);
                
                // Créer le fichier SQLite s'il n'existe pas
                if (!File::exists($databasePath)) {
                    File::put($databasePath, '');
                }
                
                $config = [
                    'driver' => 'sqlite',
                    'database' => $databasePath,
                ];
            } else {
                $config = [
                    'driver' => $connection,
                    'host' => $request->input('db_host'),
                    'port' => $request->input('db_port'),
                    'database' => $request->input('db_database'),
                    'username' => $request->input('db_username'),
                    'password' => $request->input('db_password'),
                ];
            }

            // Tester la connexion
            $pdo = DB::connection()->getPdo();

            // Mettre à jour le fichier .env
            $this->updateEnvironmentFile([
                'DB_CONNECTION' => $connection,
                'DB_HOST' => $request->input('db_host', ''),
                'DB_PORT' => $request->input('db_port', ''),
                'DB_DATABASE' => $request->input('db_database'),
                'DB_USERNAME' => $request->input('db_username', ''),
                'DB_PASSWORD' => $request->input('db_password', ''),
            ]);

            // Exécuter les migrations
            Artisan::call('migrate', ['--force' => true]);

            // Rediriger vers l'étape suivante
            return redirect()->route('install.language');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', t('install.db_connection_failed') . ': ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Étape 2: Configuration de la langue
     *
     * @return \Illuminate\View\View
     */
    public function language()
    {
        // Vérifier si l'application est déjà installée
        if ($this->isInstalled()) {
            return redirect('/')->with('error', t('install.already_installed'));
        }

        $locales = $this->translationService->getAvailableLocales();
        $localeNames = [];

        foreach ($locales as $locale) {
            $localeNames[$locale] = $this->translationService->getLocaleName($locale);
        }

        return view('install.language', compact('localeNames'));
    }

    /**
     * Traiter la configuration de la langue
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processLanguageConfig(Request $request)
    {
        // Vérifier si l'application est déjà installée
        if ($this->isInstalled()) {
            return redirect('/')->with('error', t('install.already_installed'));
        }

        // Valider les données
        $validator = Validator::make($request->all(), [
            'locale' => 'required|in:' . implode(',', $this->translationService->getAvailableLocales()),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Définir la langue par défaut
        $locale = $request->input('locale');
        $this->translationService->setLocale($locale);

        // Mettre à jour le fichier .env
        $this->updateEnvironmentFile([
            'APP_LOCALE' => $locale,
        ]);

        // Rediriger vers l'étape suivante
        return redirect()->route('install.mail');
    }

    /**
     * Étape 3: Configuration des emails
     *
     * @return \Illuminate\View\View
     */
    public function mail()
    {
        // Vérifier si l'application est déjà installée
        if ($this->isInstalled()) {
            return redirect('/')->with('error', t('install.already_installed'));
        }

        return view('install.mail');
    }

    /**
     * Traiter la configuration des emails
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processMailConfig(Request $request)
    {
        // Vérifier si l'application est déjà installée
        if ($this->isInstalled()) {
            return redirect('/')->with('error', t('install.already_installed'));
        }

        // Valider les données
        $validator = Validator::make($request->all(), [
            'mail_driver' => 'required|in:smtp,sendmail,mailgun,ses,postmark,log,array',
            'mail_host' => 'required_if:mail_driver,smtp',
            'mail_port' => 'required_if:mail_driver,smtp|numeric',
            'mail_username' => 'nullable',
            'mail_password' => 'nullable',
            'mail_encryption' => 'nullable|in:tls,ssl,null',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Mettre à jour le fichier .env
        $this->updateEnvironmentFile([
            'MAIL_MAILER' => $request->input('mail_driver'),
            'MAIL_HOST' => $request->input('mail_host', ''),
            'MAIL_PORT' => $request->input('mail_port', ''),
            'MAIL_USERNAME' => $request->input('mail_username', ''),
            'MAIL_PASSWORD' => $request->input('mail_password', ''),
            'MAIL_ENCRYPTION' => $request->input('mail_encryption', 'null'),
            'MAIL_FROM_ADDRESS' => $request->input('mail_from_address'),
            'MAIL_FROM_NAME' => $request->input('mail_from_name'),
        ]);

        // Enregistrer la configuration dans la base de données
        MailConfig::create([
            'driver' => $request->input('mail_driver'),
            'host' => $request->input('mail_host', ''),
            'port' => $request->input('mail_port', ''),
            'username' => $request->input('mail_username', ''),
            'password' => $request->input('mail_password', ''),
            'encryption' => $request->input('mail_encryption', 'null'),
            'from_address' => $request->input('mail_from_address'),
            'from_name' => $request->input('mail_from_name'),
            'is_active' => true,
        ]);

        // Rediriger vers l'étape suivante
        return redirect()->route('install.admin');
    }

    /**
     * Étape 4: Création du compte administrateur
     *
     * @return \Illuminate\View\View
     */
    public function admin()
    {
        // Vérifier si l'application est déjà installée
        if ($this->isInstalled()) {
            return redirect('/')->with('error', t('install.already_installed'));
        }

        return view('install.admin');
    }

    /**
     * Traiter la création du compte administrateur
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processAdminConfig(Request $request)
    {
        // Vérifier si l'application est déjà installée
        if ($this->isInstalled()) {
            return redirect('/')->with('error', t('install.already_installed'));
        }

        // Valider les données
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Créer le compte administrateur
        Admin::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'is_super_admin' => true,
        ]);

        // Marquer l'installation comme terminée
        $this->updateEnvironmentFile([
            'APP_INSTALLED' => 'true',
        ]);

        // Rediriger vers la page de fin d'installation
        return redirect()->route('install.complete');
    }

    /**
     * Étape finale: Installation terminée
     *
     * @return \Illuminate\View\View
     */
    public function complete()
    {
        // Vérifier si l'application est déjà installée
        if (!$this->isInstalled(false)) {
            return redirect()->route('install.index');
        }

        return view('install.complete');
    }

    /**
     * Vérifier si l'application est déjà installée
     *
     * @param bool $checkDatabase Vérifier également la base de données
     * @return bool
     */
    private function isInstalled($checkDatabase = true)
    {
        // Vérifier si APP_INSTALLED est défini à true dans le fichier .env
        $installed = env('APP_INSTALLED') === 'true';

        // Vérifier si la table admins existe et contient au moins un enregistrement
        if ($checkDatabase && $installed) {
            try {
                $installed = $installed && Schema::hasTable('admins') && Admin::count() > 0;
            } catch (\Exception $e) {
                $installed = false;
            }
        }

        return $installed;
    }

    /**
     * Mettre à jour le fichier .env
     *
     * @param array $data
     * @return bool
     */
    private function updateEnvironmentFile(array $data)
    {
        $path = base_path('.env');

        if (File::exists($path)) {
            $content = File::get($path);

            foreach ($data as $key => $value) {
                // Échapper les caractères spéciaux dans la valeur
                $value = str_replace(['\\', '"', '\''], ['\\\\', '\"', '\\\''], $value);
                
                // Remplacer ou ajouter la variable d'environnement
                if (preg_match("/^{$key}=(.*)$/m", $content)) {
                    $content = preg_replace("/^{$key}=(.*)$/m", "{$key}={$value}", $content);
                } else {
                    $content .= "\n{$key}={$value}";
                }
            }

            File::put($path, $content);
            return true;
        }

        return false;
    }
}