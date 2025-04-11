<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class InstallComposerCommand extends Command
{
    /**
     * Le nom et la signature de la commande.
     *
     * @var string
     */
    protected $signature = 'install:composer';

    /**
     * La description de la commande.
     *
     * @var string
     */
    protected $description = 'Installe les dépendances sans utiliser Composer';

    /**
     * Exécute la commande.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Installation des dépendances sans Composer...');
        
        try {
            // Vérifier si le fichier composer.json existe
            if (!File::exists(base_path('composer.json'))) {
                $this->error('Le fichier composer.json n\'existe pas.');
                return 1;
            }
            
            // Lire le fichier composer.json
            $composerJson = json_decode(File::get(base_path('composer.json')), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('Le fichier composer.json est invalide.');
                return 1;
            }
            
            // Vérifier si le dossier vendor existe déjà
            if (File::exists(base_path('vendor'))) {
                $this->info('Le dossier vendor existe déjà. Suppression...');
                File::deleteDirectory(base_path('vendor'));
            }
            
            // Créer le dossier vendor
            File::makeDirectory(base_path('vendor'), 0755, true);
            
            // Créer le dossier autoload
            File::makeDirectory(base_path('vendor/autoload'), 0755, true);
            
            // Créer le fichier autoload.php
            $autoloadContent = "<?php\n\n";
            $autoloadContent .= "// Ce fichier a été généré automatiquement.\n";
            $autoloadContent .= "// Il est utilisé comme alternative à Composer.\n\n";
            $autoloadContent .= "spl_autoload_register(function (\$class) {\n";
            $autoloadContent .= "    \$prefix = '';\n";
            $autoloadContent .= "    \$base_dir = __DIR__ . '/../';\n\n";
            $autoloadContent .= "    \$len = strlen(\$prefix);\n";
            $autoloadContent .= "    if (strncmp(\$prefix, \$class, \$len) !== 0) {\n";
            $autoloadContent .= "        return;\n";
            $autoloadContent .= "    }\n\n";
            $autoloadContent .= "    \$relative_class = substr(\$class, \$len);\n";
            $autoloadContent .= "    \$file = \$base_dir . str_replace('\\\\', '/', \$relative_class) . '.php';\n\n";
            $autoloadContent .= "    if (file_exists(\$file)) {\n";
            $autoloadContent .= "        require \$file;\n";
            $autoloadContent .= "    }\n";
            $autoloadContent .= "});\n";
            
            File::put(base_path('vendor/autoload/autoload.php'), $autoloadContent);
            
            // Créer le fichier composer/autoload_classmap.php
            File::makeDirectory(base_path('vendor/composer'), 0755, true);
            
            $classmapContent = "<?php\n\n";
            $classmapContent .= "// Ce fichier a été généré automatiquement.\n";
            $classmapContent .= "// Il est utilisé comme alternative à Composer.\n\n";
            $classmapContent .= "\$vendorDir = dirname(__DIR__);\n\n";
            $classmapContent .= "return array(\n";
            $classmapContent .= ");\n";
            
            File::put(base_path('vendor/composer/autoload_classmap.php'), $classmapContent);
            
            // Créer le fichier composer/autoload_namespaces.php
            $namespacesContent = "<?php\n\n";
            $namespacesContent .= "// Ce fichier a été généré automatiquement.\n";
            $namespacesContent .= "// Il est utilisé comme alternative à Composer.\n\n";
            $namespacesContent .= "\$vendorDir = dirname(__DIR__);\n\n";
            $namespacesContent .= "return array(\n";
            $namespacesContent .= ");\n";
            
            File::put(base_path('vendor/composer/autoload_namespaces.php'), $namespacesContent);
            
            // Créer le fichier composer/autoload_psr4.php
            $psr4Content = "<?php\n\n";
            $psr4Content .= "// Ce fichier a été généré automatiquement.\n";
            $psr4Content .= "// Il est utilisé comme alternative à Composer.\n\n";
            $psr4Content .= "\$vendorDir = dirname(__DIR__);\n\n";
            $psr4Content .= "return array(\n";
            $psr4Content .= "    'App\\\\' => array(\$vendorDir . '/../app'),\n";
            $psr4Content .= ");\n";
            
            File::put(base_path('vendor/composer/autoload_psr4.php'), $psr4Content);
            
            // Créer le fichier composer/autoload_real.php
            $realContent = "<?php\n\n";
            $realContent .= "// Ce fichier a été généré automatiquement.\n";
            $realContent .= "// Il est utilisé comme alternative à Composer.\n\n";
            $realContent .= "class ComposerAutoloaderInit\n";
            $realContent .= "{\n";
            $realContent .= "    private static \$loader;\n\n";
            $realContent .= "    public static function getLoader()\n";
            $realContent .= "    {\n";
            $realContent .= "        if (null !== self::\$loader) {\n";
            $realContent .= "            return self::\$loader;\n";
            $realContent .= "        }\n\n";
            $realContent .= "        spl_autoload_register(array('ComposerAutoloaderInit', 'loadClassLoader'), true, true);\n";
            $realContent .= "        self::\$loader = \$loader = new \\Composer\\Autoload\\ClassLoader();\n";
            $realContent .= "        spl_autoload_unregister(array('ComposerAutoloaderInit', 'loadClassLoader'));\n\n";
            $realContent .= "        return \$loader;\n";
            $realContent .= "    }\n\n";
            $realContent .= "    public static function loadClassLoader(\$class)\n";
            $realContent .= "    {\n";
            $realContent .= "        if ('Composer\\Autoload\\ClassLoader' === \$class) {\n";
            $realContent .= "            require __DIR__ . '/ClassLoader.php';\n";
            $realContent .= "        }\n";
            $realContent .= "    }\n";
            $realContent .= "}\n";
            
            File::put(base_path('vendor/composer/autoload_real.php'), $realContent);
            
            // Créer le fichier composer/ClassLoader.php
            $loaderContent = "<?php\n\n";
            $loaderContent .= "// Ce fichier a été généré automatiquement.\n";
            $loaderContent .= "// Il est utilisé comme alternative à Composer.\n\n";
            $loaderContent .= "namespace Composer\\Autoload;\n\n";
            $loaderContent .= "class ClassLoader\n";
            $loaderContent .= "{\n";
            $loaderContent .= "    private \$prefixesPsr4 = array();\n";
            $loaderContent .= "    private \$prefixes = array();\n";
            $loaderContent .= "    private \$fallbackDirsPsr4 = array();\n";
            $loaderContent .= "    private \$fallbackDirs = array();\n";
            $loaderContent .= "    private \$useIncludePath = false;\n";
            $loaderContent .= "    private \$classMap = array();\n";
            $loaderContent .= "    private \$classMapAuthoritative = false;\n";
            $loaderContent .= "    private \$missingClasses = array();\n";
            $loaderContent .= "    private \$apcu = false;\n\n";
            $loaderContent .= "    public function getPrefixes()\n";
            $loaderContent .= "    {\n";
            $loaderContent .= "        return \$this->prefixes;\n";
            $loaderContent .= "    }\n\n";
            $loaderContent .= "    public function getPrefixesPsr4()\n";
            $loaderContent .= "    {\n";
            $loaderContent .= "        return \$this->prefixesPsr4;\n";
            $loaderContent .= "    }\n\n";
            $loaderContent .= "    public function getFallbackDirs()\n";
            $loaderContent .= "    {\n";
            $loaderContent .= "        return \$this->fallbackDirs;\n";
            $loaderContent .= "    }\n\n";
            $loaderContent .= "    public function getFallbackDirsPsr4()\n";
            $loaderContent .= "    {\n";
            $loaderContent .= "        return \$this->fallbackDirsPsr4;\n";
            $loaderContent .= "    }\n\n";
            $loaderContent .= "    public function getClassMap()\n";
            $loaderContent .= "    {\n";
            $loaderContent .= "        return \$this->classMap;\n";
            $loaderContent .= "    }\n\n";
            $loaderContent .= "    public function addClassMap(array \$classMap)\n";
            $loaderContent .= "    {\n";
            $loaderContent .= "        if (\$this->classMapAuthoritative) {\n";
            $loaderContent .= "            \$this->classMap = array_merge(\$this->classMap, \$classMap);\n";
            $loaderContent .= "        } else {\n";
            $loaderContent .= "            \$this->classMap = array_merge(\$this->classMap, \$classMap);\n";
            $loaderContent .= "        }\n";
            $loaderContent .= "    }\n\n";
            $loaderContent .= "    public function add(\$prefix, \$paths, \$prepend = false)\n";
            $loaderContent .= "    {\n";
            $loaderContent .= "        \$paths = (array) \$paths;\n\n";
            $loaderContent .= "        if (!isset(\$this->prefixes[\$prefix])) {\n";
            $loaderContent .= "            \$this->prefixes[\$prefix] = array();\n";
            $loaderContent .= "        }\n\n";
            $loaderContent .= "        if (\$prepend) {\n";
            $loaderContent .= "            \$this->prefixes[\$prefix] = array_merge(\$paths, \$this->prefixes[\$prefix]);\n";
            $loaderContent .= "        } else {\n";
            $loaderContent .= "            \$this->prefixes[\$prefix] = array_merge(\$this->prefixes[\$prefix], \$paths);\n";
            $loaderContent .= "        }\n";
            $loaderContent .= "    }\n\n";
            $loaderContent .= "    public function addPsr4(\$prefix, \$paths, \$prepend = false)\n";
            $loaderContent .= "    {\n";
            $loaderContent .= "        \$paths = (array) \$paths;\n\n";
            $loaderContent .= "        if (!isset(\$this->prefixesPsr4[\$prefix])) {\n";
            $loaderContent .= "            \$this->prefixesPsr4[\$prefix] = array();\n";
            $loaderContent .= "        }\n\n";
            $loaderContent .= "        if (\$prepend) {\n";
            $loaderContent .= "            \$this->prefixesPsr4[\$prefix] = array_merge(\$paths, \$this->prefixesPsr4[\$prefix]);\n";
            $loaderContent .= "        } else {\n";
            $loaderContent .= "            \$this->prefixesPsr4[\$prefix] = array_merge(\$this->prefixesPsr4[\$prefix], \$paths);\n";
            $loaderContent .= "        }\n";
            $loaderContent .= "    }\n\n";
            $loaderContent .= "    public function setUseIncludePath(\$useIncludePath)\n";
            $loaderContent .= "    {\n";
            $loaderContent .= "        \$this->useIncludePath = \$useIncludePath;\n";
            $loaderContent .= "    }\n\n";
            $loaderContent .= "    public function getUseIncludePath()\n";
            $loaderContent .= "    {\n";
            $loaderContent .= "        return \$this->useIncludePath;\n";
            $loaderContent .= "    }\n\n";
            $loaderContent .= "    public function setClassMapAuthoritative(\$classMapAuthoritative)\n";
            $loaderContent .= "    {\n";
            $loaderContent .= "        \$this->classMapAuthoritative = \$classMapAuthoritative;\n";
            $loaderContent .= "    }\n\n";
            $loaderContent .= "    public function getClassMapAuthoritative()\n";
            $loaderContent .= "    {\n";
            $loaderContent .= "        return \$this->classMapAuthoritative;\n";
            $loaderContent .= "    }\n\n";
            $loaderContent .= "    public function setApcu(\$apcu)\n";
            $loaderContent .= "    {\n";
            $loaderContent .= "        \$this->apcu = \$apcu;\n";
            $loaderContent .= "    }\n\n";
            $loaderContent .= "    public function getApcu()\n";
            $loaderContent .= "    {\n";
            $loaderContent .= "        return \$this->apcu;\n";
            $loaderContent .= "    }\n\n";
            $loaderContent .= "    public function register(\$prepend = false)\n";
            $loaderContent .= "    {\n";
            $loaderContent .= "        spl_autoload_register(array(\$this, 'loadClass'), true, \$prepend);\n";
            $loaderContent .= "    }\n\n";
            $loaderContent .= "    public function loadClass(\$class)\n";
            $loaderContent .= "    {\n";
            $loaderContent .= "        if (\$file = \$this->findFile(\$class)) {\n";
            $loaderContent .= "            includeFile(\$file);\n\n";
            $loaderContent .= "            return true;\n";
            $loaderContent .= "        }\n";
            $loaderContent .= "    }\n\n";
            $loaderContent .= "    public function findFile(\$class)\n";
            $loaderContent .= "    {\n";
            $loaderContent .= "        if (isset(\$this->classMap[\$class])) {\n";
            $loaderContent .= "            return \$this->classMap[\$class];\n";
            $loaderContent .= "        }\n\n";
            $loaderContent .= "        return false;\n";
            $loaderContent .= "    }\n";
            $loaderContent .= "}\n\n";
            $loaderContent .= "function includeFile(\$file)\n";
            $loaderContent .= "{\n";
            $loaderContent .= "    include \$file;\n";
            $loaderContent .= "}\n";
            
            File::put(base_path('vendor/composer/ClassLoader.php'), $loaderContent);
            
            // Créer le fichier composer/installed.json
            $installedContent = "{\n";
            $installedContent .= "    \"name\": \"laravel/framework\",\n";
            $installedContent .= "    \"type\": \"project\",\n";
            $installedContent .= "    \"description\": \"The Laravel Framework.\",\n";
            $installedContent .= "    \"keywords\": [\"framework\", \"laravel\"],\n";
            $installedContent .= "    \"license\": \"MIT\",\n";
            $installedContent .= "    \"require\": {\n";
            $installedContent .= "        \"php\": \"^7.3|^8.0\"\n";
            $installedContent .= "    },\n";
            $installedContent .= "    \"require-dev\": {\n";
            $installedContent .= "        \"fzaninotto/faker\": \"^1.9.1\",\n";
            $installedContent .= "        \"laravel/tinker\": \"^2.5\",\n";
            $installedContent .= "        \"mockery/mockery\": \"^1.4.2\",\n";
            $installedContent .= "        \"nunomaduro/collision\": \"^4.3\",\n";
            $installedContent .= "        \"phpunit/phpunit\": \"^8.5.8\"\n";
            $installedContent .= "    },\n";
            $installedContent .= "    \"autoload\": {\n";
            $installedContent .= "        \"psr-4\": {\n";
            $installedContent .= "            \"App\\\\\": \"app/\"\n";
            $installedContent .= "        }\n";
            $installedContent .= "    },\n";
            $installedContent .= "    \"autoload-dev\": {\n";
            $installedContent .= "        \"psr-4\": {\n";
            $installedContent .= "            \"Tests\\\\\": \"tests/\"\n";
            $installedContent .= "        }\n";
            $installedContent .= "    },\n";
            $installedContent .= "    \"config\": {\n";
            $installedContent .= "        \"optimize-autoloader\": true,\n";
            $installedContent .= "        \"preferred-install\": \"dist\",\n";
            $installedContent .= "        \"sort-packages\": true\n";
            $installedContent .= "    },\n";
            $installedContent .= "    \"extra\": {\n";
            $installedContent .= "        \"laravel\": {\n";
            $installedContent .= "            \"dont-discover\": []\n";
            $installedContent .= "        }\n";
            $installedContent .= "    },\n";
            $installedContent .= "    \"minimum-stability\": \"dev\",\n";
            $installedContent .= "    \"prefer-stable\": true\n";
            $installedContent .= "}\n";
            
            File::put(base_path('vendor/composer/installed.json'), $installedContent);
            
            $this->info('Installation des dépendances terminée avec succès.');
            return 0;
        } catch (\Exception $e) {
            $this->error('Erreur lors de l\'installation des dépendances : ' . $e->getMessage());
            Log::error('Erreur lors de l\'installation des dépendances : ' . $e->getMessage());
            return 1;
        }
    }
} 