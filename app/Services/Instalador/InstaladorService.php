<?php

declare(strict_types=1);

/**
 * @autor marcelo-brad rj
 * @contato Tel: +55 (21) 98132-5441
 * @contato Email: contato@kdkhost.com.br
 * @contato Telegram: @MARCELO_BRAD
 * @contato Instagram: @marcelobradrj
 * @contato WhatsApp: 5521981325441
 */

namespace App\Services\Instalador;

use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class InstaladorService
{
    public function checkRequirements(): array
    {
        $requirements = [
            'php_version' => [
                'name' => 'PHP ' . PHP_VERSION,
                'required' => '^8.3',
                'status' => version_compare(PHP_VERSION, '8.3.0', '>='),
                'message' => version_compare(PHP_VERSION, '8.3.0', '>=') ? 'OK' : 'PHP 8.3 ou superior necessário',
            ],
        ];

        $extensions = [
            'pdo' => 'PDO',
            'mbstring' => 'Mbstring',
            'xml' => 'XML',
            'curl' => 'cURL',
            'gd' => 'GD',
            'zip' => 'Zip',
            'bcmath' => 'BCMath',
            'json' => 'JSON',
            'openssl' => 'OpenSSL',
            'tokenizer' => 'Tokenizer',
            'fileinfo' => 'Fileinfo',
            'xmlwriter' => 'XML Writer',
            'dom' => 'DOM',
            'session' => 'Session',
            'ctype' => 'CTYPE',
            'filter' => 'Filter',
        ];

        if ($this->getInstallationEnvironment() === 'offline') {
            $extensions['pdo_sqlite'] = 'PDO SQLite';
        } else {
            $extensions['pdo_mysql'] = 'PDO MySQL';
        }

        foreach ($extensions as $ext => $name) {
            $requirements["extension_{$ext}"] = [
                'name' => "Extensão {$name}",
                'required' => 'Habilitada',
                'status' => extension_loaded($ext),
                'message' => extension_loaded($ext) ? 'OK' : "Extensão {$name} não está habilitada",
            ];
        }

        return $requirements;
    }

    public function checkPermissions(): array
    {
        $paths = [
            storage_path() => 'storage',
            storage_path('app/public') => 'storage/app/public',
            storage_path('framework/cache') => 'storage/framework/cache',
            storage_path('framework/sessions') => 'storage/framework/sessions',
            storage_path('framework/views') => 'storage/framework/views',
            storage_path('logs') => 'storage/logs',
            base_path('bootstrap/cache') => 'bootstrap/cache',
            public_path('uploads') => 'public/uploads',
            public_path('storage') => 'public/storage',
        ];

        $permissions = [];

        foreach ($paths as $path => $label) {
            if (!is_dir($path)) {
                try {
                    mkdir($path, 0755, true);
                } catch (\Throwable) {
                }
            }

            $exists = is_dir($path) || is_file($path);
            $writable = is_writable($path);

            $permissions[$label] = [
                'path' => $path,
                'exists' => $exists,
                'writable' => $writable,
                'status' => $exists && $writable,
                'message' => match (true) {
                    !$exists => 'Caminho não encontrado',
                    !$writable => 'Sem permissão de escrita',
                    default => 'OK',
                },
            ];
        }

        return $permissions;
    }

    public function getDatabaseConfig(): array
    {
        return [
            'driver' => config('database.default'),
            'host' => config("database.connections." . config('database.default') . ".host"),
            'port' => config("database.connections." . config('database.default') . ".port"),
            'database' => config("database.connections." . config('database.default') . ".database"),
            'username' => config("database.connections." . config('database.default') . ".username"),
        ];
    }

    public function getInstallationEnvironment(): string
    {
        if (function_exists('ambiente_instalacao')) {
            return ambiente_instalacao();
        }

        if (function_exists('is_offline') && is_offline()) {
            return 'offline';
        }

        return 'web';
    }

    public function getDefaultDatabaseDriver(): string
    {
        return $this->getInstallationEnvironment() === 'offline' ? 'sqlite' : 'mysql';
    }

    public function normalizeDatabaseConfig(array $config): array
    {
        $driver = $config['db_driver'] ?? $config['driver'] ?? $this->getDefaultDatabaseDriver();
        $driver = $driver ?: 'sqlite';

        $database = $config['db_database'] ?? $config['database'] ?? '';
        if ($driver === 'sqlite' && empty($database)) {
            $database = database_path('database.sqlite');
        }

        return [
            'driver' => $driver,
            'host' => $config['db_host'] ?? $config['host'] ?? '127.0.0.1',
            'port' => $config['db_port'] ?? $config['port'] ?? ($driver === 'pgsql' ? '5432' : '3306'),
            'database' => $database,
            'username' => $config['db_username'] ?? $config['username'] ?? 'root',
            'password' => $config['db_password'] ?? $config['password'] ?? '',
        ];
    }

    public function getEnvironmentDatabaseConfig(): array
    {
        $env = $this->readEnvironmentFile();
        $driver = $env['DB_CONNECTION'] ?? config('database.default', 'sqlite');
        $database = $env['DB_DATABASE'] ?? ($driver === 'sqlite' ? database_path('database.sqlite') : '');

        return $this->normalizeDatabaseConfig([
            'db_driver' => $driver,
            'db_host' => $env['DB_HOST'] ?? config('database.connections.mysql.host', '127.0.0.1'),
            'db_port' => $env['DB_PORT'] ?? config('database.connections.mysql.port', '3306'),
            'db_database' => $database,
            'db_username' => $env['DB_USERNAME'] ?? config('database.connections.mysql.username', 'root'),
            'db_password' => $env['DB_PASSWORD'] ?? '',
        ]);
    }

    public function applyDatabaseConfig(array $config): array
    {
        $config = $this->normalizeDatabaseConfig($config);
        $driver = $config['driver'];

        if ($driver === 'sqlite') {
            $dbDir = dirname($config['database']);
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0755, true);
            }

            if (!file_exists($config['database'])) {
                file_put_contents($config['database'], '');
            }
        }

        Config::set('database.default', $driver);
        Config::set("database.connections.{$driver}", $this->buildDatabaseConnectionConfig($config));
        Config::set('session.driver', 'file');
        Config::set('cache.default', 'file');
        Config::set('queue.default', 'sync');

        DB::purge($driver);
        DB::setDefaultConnection($driver);

        return $config;
    }

    public function testDatabaseConnection(array $config): array
    {
        $config = $this->normalizeDatabaseConfig($config);
        $driver = $config['driver'];

        if ($driver === 'sqlite') {
            return [
                'success' => true,
                'message' => 'Banco SQLite configurado com sucesso.',
            ];
        }

        Config::set('database.connections.installer', $this->buildDatabaseConnectionConfig($config));
        DB::purge('installer');

        try {
            DB::connection('installer')->getPdo();
            DB::disconnect('installer');

            return [
                'success' => true,
                'message' => 'Conexão com o banco de dados estabelecida com sucesso.',
            ];
        } catch (\Throwable $e) {
            DB::disconnect('installer');

            return [
                'success' => false,
                'message' => 'Falha na conexão: ' . $e->getMessage(),
            ];
        }
    }

    public function createEnvironmentFile(array $data): bool
    {
        $envContent = File::exists(base_path('.env.example'))
            ? File::get(base_path('.env.example'))
            : '';

        if (empty($envContent)) {
            $envContent = $this->getDefaultEnvContent();
        }

        $databaseConfig = $this->normalizeDatabaseConfig($data);
        $driver = $databaseConfig['driver'];
        $environment = $this->getInstallationEnvironment();
        $appName = $data['app_name'] ?? 'Político 2';
        $appUrl = $data['app_url'] ?? $this->detectAppUrl($environment);

        // Generate APP_KEY immediately so .env is born complete
        $appKey = 'base64:' . base64_encode(random_bytes(32));

        $replacements = [
            'APP_NAME' => $appName,
            'APP_URL' => $appUrl,
            'APP_ENV' => $environment === 'offline' ? 'local' : 'production',
            'APP_DEBUG' => $environment === 'offline' ? 'true' : 'false',
            'APP_KEY' => $appKey,
            'DB_CONNECTION' => $driver,
            'SESSION_DRIVER' => 'file',
            'CACHE_STORE' => 'file',
            'CACHE_DRIVER' => 'file',
            'QUEUE_CONNECTION' => 'sync',
        ];

        if ($driver === 'sqlite') {
            $replacements['DB_HOST'] = '';
            $replacements['DB_PORT'] = '';
            $replacements['DB_DATABASE'] = $databaseConfig['database'];
            $replacements['DB_USERNAME'] = '';
            $replacements['DB_PASSWORD'] = '';
        } else {
            $replacements['DB_HOST'] = $databaseConfig['host'];
            $replacements['DB_PORT'] = $databaseConfig['port'];
            $replacements['DB_DATABASE'] = $databaseConfig['database'];
            $replacements['DB_USERNAME'] = $databaseConfig['username'];
            $replacements['DB_PASSWORD'] = $databaseConfig['password'];
        }

        foreach ($replacements as $key => $value) {
            $envContent = $this->setEnvironmentValue($envContent, $key, $value);
        }

        File::put(base_path('.env'), $envContent);
        $this->clearCachedConfig();
        $this->applyDatabaseConfig($databaseConfig);

        return true;
    }

    public function runMigrations(): array
    {
        $logFile = storage_path('logs/installer.log');
        $log = function($msg) use ($logFile) {
            $dir = dirname($logFile);
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            file_put_contents($logFile, date('Y-m-d H:i:s') . ' [MIGRATE] ' . $msg . PHP_EOL, FILE_APPEND);
        };

        $connection = config('database.default');
        $log('Iniciando runMigrations via app(migrator) na conexão ' . $connection);

        $originalDispatcher = null;

        try {
            $log('Obtendo migrator do container...');
            $migrator = app('migrator');
            if (method_exists($migrator, 'setConnection')) {
                $migrator->setConnection($connection);
            }
            $log('Migrator obtido');

            // Ensure migration table exists
            $repository = $migrator->getRepository();
            if (!$repository->repositoryExists()) {
                $log('Criando tabela migrations...');
                $repository->createRepository();
                $log('Tabela migrations criada');
            }

            // Disable events to prevent deadlock on Windows
            $log('Desabilitando eventos...');
            $originalDispatcher = app('events');
            app()->instance('events', new \Illuminate\Events\Dispatcher());
            $log('Eventos desabilitados');

            $log('Rodando migracoes...');
            $migrator->run(database_path('migrations'));

            // Restore events
            app()->instance('events', $originalDispatcher);
            $log('Eventos restaurados');

            $log('Migracoes OK');
            return [
                'success' => true,
                'output' => 'Migrations executadas com sucesso.',
            ];
        } catch (\Throwable $e) {
            if ($originalDispatcher !== null) {
                app()->instance('events', $originalDispatcher);
            }

            $log('Migrator ERRO: ' . $e->getMessage());
            $log('Trace: ' . substr($e->getTraceAsString(), 0, 500));
            return [
                'success' => false,
                'message' => 'Erro ao executar migrations: ' . $e->getMessage(),
            ];
        }
    }

    public function createAdminUser(array $data): User
    {
        return User::create([
            'name' => $data['name'] ?? 'Administrador',
            'email' => $data['email'] ?? '',
            'password' => Hash::make($data['password'] ?? ''),
            'is_super_admin' => true,
            'status' => 'active',
        ]);
    }

    public function setInitialConfig(array $data): void
    {
        $settings = [
            'app_name' => $data['app_name'] ?? 'Político 2',
            'app_description' => $data['app_description'] ?? '',
            'timezone' => $data['timezone'] ?? 'America/Sao_Paulo',
            'locale' => $data['locale'] ?? 'pt_BR',
        ];

        foreach ($settings as $key => $value) {
            Config::set("sistema.{$key}", $value);
        }

        $this->clearCachedConfig();
    }

    public function isInstalled(): bool
    {
        if ($this->hasInstallationFile()) {
            return true;
        }

        // Check if there is at least one user in the database
        try {
            if (File::exists(base_path('.env'))) {
                $config = $this->getEnvironmentDatabaseConfig();
                $driver = $config['driver'];
                $hasDb = false;

                if ($driver === 'sqlite') {
                    $dbPath = $config['database'] ?: database_path('database.sqlite');
                    $hasDb = file_exists($dbPath) && filesize($dbPath) > 100;
                } else {
                    $hasDb = !empty($config['database']);
                }

                if ($hasDb) {
                    $this->applyDatabaseConfig($config);

                    if (!Schema::hasTable('users')) {
                        return false;
                    }

                    $userCount = DB::table('users')->count();
                    if ($userCount > 0) {
                        return true;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Database not ready yet
            return false;
        }

        return false;
    }

    public function hasInstallationFile(): bool
    {
        return file_exists(storage_path('app/installed'));
    }

    public function completeInstallation(): void
    {
        $installedPath = storage_path('app/installed');

        $dir = dirname($installedPath);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($installedPath, date('Y-m-d H:i:s'));
    }

    public function protectInstaller(): void
    {
        $htaccess = public_path('.htaccess');
        $content = '';

        if (file_exists($htaccess)) {
            $content = File::get($htaccess);
        }

        $rule = PHP_EOL . PHP_EOL .
            '# Bloquear acesso ao instalador' . PHP_EOL .
            '<IfModule mod_rewrite.c>' . PHP_EOL .
            '    RewriteEngine On' . PHP_EOL .
            '    RewriteRule ^install - [F,L]' . PHP_EOL .
            '</IfModule>';

        if (!str_contains($content, 'install')) {
            File::append($htaccess, $rule);
        }
    }

    protected function getDefaultEnvContent(): string
    {
        return "APP_NAME=Político 2\n" .
            "APP_ENV=production\n" .
            "APP_KEY=\n" .
            "APP_DEBUG=false\n" .
            "APP_URL=http://localhost\n\n" .
            "LOG_CHANNEL=stack\n\n" .
            "DB_CONNECTION=mysql\n" .
            "DB_HOST=127.0.0.1\n" .
            "DB_PORT=3306\n" .
            "DB_DATABASE=\n" .
            "DB_USERNAME=root\n" .
            "DB_PASSWORD=\n\n" .
            "BROADCAST_DRIVER=log\n" .
            "CACHE_DRIVER=file\n" .
            "FILESYSTEM_DISK=local\n" .
            "QUEUE_CONNECTION=sync\n" .
            "SESSION_DRIVER=file\n" .
            "SESSION_LIFETIME=120\n\n" .
            "MAIL_MAILER=smtp\n" .
            "MAIL_HOST=\n" .
            "MAIL_PORT=587\n" .
            "MAIL_USERNAME=\n" .
            "MAIL_PASSWORD=\n" .
            "MAIL_ENCRYPTION=tls\n" .
            "MAIL_FROM_ADDRESS=\n" .
            "MAIL_FROM_NAME=\"\${APP_NAME}\"\n";
    }

    protected function detectAppUrl(string $environment): string
    {
        if ($environment === 'offline') {
            return 'http://localhost';
        }

        if (!app()->runningInConsole() && request()) {
            return request()->getSchemeAndHttpHost();
        }

        return 'https://localhost';
    }

    protected function buildDatabaseConnectionConfig(array $config): array
    {
        $driver = $config['driver'];

        if ($driver === 'sqlite') {
            return [
                'driver' => 'sqlite',
                'database' => $config['database'] ?: database_path('database.sqlite'),
                'prefix' => '',
                'foreign_key_constraints' => true,
                'busy_timeout' => null,
                'journal_mode' => null,
                'synchronous' => null,
                'transaction_mode' => 'DEFERRED',
            ];
        }

        $connection = array_merge(config("database.connections.{$driver}", []), [
            'driver' => $driver,
            'host' => $config['host'],
            'port' => (string) $config['port'],
            'database' => $config['database'],
            'username' => $config['username'],
            'password' => $config['password'],
            'prefix' => '',
        ]);

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $connection += [
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix_indexes' => true,
                'strict' => true,
                'engine' => null,
                'options' => [],
            ];
        }

        if ($driver === 'pgsql') {
            $connection += [
                'charset' => 'utf8',
                'prefix_indexes' => true,
                'search_path' => 'public',
                'sslmode' => 'prefer',
            ];
        }

        if ($driver === 'sqlsrv') {
            $connection += [
                'charset' => 'utf8',
                'prefix_indexes' => true,
            ];
        }

        return $connection;
    }

    protected function setEnvironmentValue(string $content, string $key, mixed $value): string
    {
        $line = $key . '=' . $this->formatEnvironmentValue($value);
        $escapedKey = preg_quote($key, '/');
        $pattern = "/^[ \t]*#?[ \t]*{$escapedKey}=.*$/m";

        if (preg_match($pattern, $content)) {
            return (string) preg_replace($pattern, $line, $content);
        }

        return rtrim($content) . PHP_EOL . $line . PHP_EOL;
    }

    protected function formatEnvironmentValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return '';
        }

        $value = (string) $value;

        if ($value === '') {
            return '';
        }

        if (preg_match("/\s|#|\"|'|=/", $value)) {
            return "'" . str_replace("'", "\\'", $value) . "'";
        }

        return $value;
    }

    protected function readEnvironmentFile(): array
    {
        $path = base_path('.env');
        if (!File::exists($path)) {
            return [];
        }

        $values = [];
        $lines = preg_split('/\r\n|\r|\n/', File::get($path)) ?: [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if ($key === '') {
                continue;
            }

            if (
                strlen($value) >= 2 &&
                (($value[0] === '"' && substr($value, -1) === '"') ||
                 ($value[0] === "'" && substr($value, -1) === "'"))
            ) {
                $quote = $value[0];
                $value = substr($value, 1, -1);
                $value = $quote === '"' ? stripcslashes($value) : str_replace("\\'", "'", $value);
            }

            $values[$key] = $value;
        }

        return $values;
    }

    protected function clearCachedConfig(): void
    {
        $configCache = base_path('bootstrap/cache/config.php');

        if (File::exists($configCache) && !File::delete($configCache)) {
            throw new \RuntimeException('Não foi possível limpar o cache de configuração em bootstrap/cache/config.php.');
        }
    }
}
