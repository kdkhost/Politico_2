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

namespace App\Http\Controllers;

use App\Services\Instalador\InstaladorService;
use Illuminate\Http\Request;

class InstallerController extends Controller
{
    public function __construct(
        protected InstaladorService $instaladorService,
    ) {}

    protected function logInstaller(string $message): void
    {
        $logFile = storage_path('logs/installer.log');
        $dir = dirname($logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($logFile, date('Y-m-d H:i:s') . ' ' . $message . PHP_EOL, FILE_APPEND);
    }

    public function index()
    {
        if ($this->instaladorService->isInstalled()) {
            return redirect()->route('admin.login');
        }

        return view('installer.index');
    }

    public function requirements()
    {
        if ($this->instaladorService->isInstalled()) {
            return redirect()->route('admin.login');
        }

        $requirements = $this->instaladorService->checkRequirements();
        $permissions = $this->instaladorService->checkPermissions();

        return view('installer.requirements', compact('requirements', 'permissions'));
    }

    public function checkRequirements(Request $request)
    {
        if ($this->instaladorService->isInstalled()) {
            return response()->json(['status' => 'error', 'message' => 'Sistema já instalado.']);
        }

        $request->validate(['check' => 'required|string']);

        $check = $request->check;
        $requirements = $this->instaladorService->checkRequirements();
        $permissions = $this->instaladorService->checkPermissions();

        if (isset($requirements[$check])) {
            return response()->json([
                'status' => $requirements[$check]['status'] ? 'success' : 'error',
                'data' => $requirements[$check],
            ]);
        }

        if (isset($permissions[$check])) {
            return response()->json([
                'status' => $permissions[$check]['status'] ? 'success' : 'error',
                'data' => $permissions[$check],
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Requisito não encontrado.'], 404);
    }

    public function database()
    {
        if ($this->instaladorService->isInstalled()) {
            return redirect()->route('admin.login');
        }

        $ambiente = $this->instaladorService->getInstallationEnvironment();
        $config = $this->instaladorService->getDatabaseConfig();

        return view('installer.database', compact('config', 'ambiente'));
    }

    public function configureDatabase(Request $request)
    {
        if ($this->instaladorService->isInstalled()) {
            return response()->json(['status' => 'error', 'message' => 'Sistema j\u00e1 instalado.']);
        }

        $ambiente = $this->instaladorService->getInstallationEnvironment();
        $request->merge([
            'db_driver' => $this->instaladorService->getDefaultDatabaseDriver(),
        ]);

        $validated = $request->validate([
            'db_driver' => 'required|string|in:mysql,pgsql,sqlite,sqlsrv',
            'db_host' => 'required_unless:db_driver,sqlite|string|max:255',
            'db_port' => 'required_unless:db_driver,sqlite|integer|min:1|max:65535',
            'db_database' => 'required_unless:db_driver,sqlite|string|max:255',
            'db_username' => 'required_unless:db_driver,sqlite|string|max:255',
            'db_password' => 'nullable|string|max:255',
            'app_name' => 'nullable|string|max:255',
            'app_url' => 'nullable|url|max:255',
        ], [
            'db_driver.required' => 'O tipo de banco de dados \u00e9 obrigat\u00f3rio.',
            'db_driver.in' => 'Tipo de banco de dados inv\u00e1lido.',
            'db_host.required_unless' => 'O host do banco de dados \u00e9 obrigat\u00f3rio.',
            'db_port.required_unless' => 'A porta do banco de dados \u00e9 obrigat\u00f3ria.',
            'db_database.required_unless' => 'O nome do banco de dados \u00e9 obrigat\u00f3rio.',
            'db_username.required_unless' => 'O usu\u00e1rio do banco de dados \u00e9 obrigat\u00f3rio.',
            'app_url.url' => 'Informe uma URL v\u00e1lida.',
        ]);

        if ($ambiente === 'offline' && $validated['db_driver'] === 'sqlite') {
            return $this->configureSqlite($validated);
        }

        $testResult = $this->instaladorService->testDatabaseConnection($validated);

        if (!$testResult['success']) {
            return response()->json([
                'status' => 'error',
                'message' => $testResult['message'],
            ], 422);
        }

        try {
            $this->instaladorService->createEnvironmentFile($validated);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao criar arquivo .env: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Ambiente configurado. Executando migrations...',
            'next_step' => 'migrate',
            'redirect' => route('install.migrate'),
        ]);
    }

    protected function configureSqlite(array $data): \Illuminate\Http\JsonResponse
    {
        $dbPath = database_path('database.sqlite');

        $dbDir = dirname($dbPath);
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }

        if (!file_exists($dbPath)) {
            file_put_contents($dbPath, '');
        }

        $envData = [
            'db_driver' => 'sqlite',
            'db_host' => '',
            'db_port' => '',
            'db_database' => $dbPath,
            'db_username' => '',
            'db_password' => '',
            'app_name' => $data['app_name'] ?? 'Político 2',
            'app_url' => $data['app_url'] ?? 'http://localhost',
        ];

        try {
            $this->instaladorService->createEnvironmentFile($envData);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao criar arquivo .env: ' . $e->getMessage(),
            ], 500);
        }

        // Migrations will run in a separate request to avoid Windows deadlock
        return response()->json([
            'status' => 'success',
            'message' => 'Ambiente configurado. Executando migrations...',
            'next_step' => 'migrate',
            'redirect' => route('install.migrate'),
        ]);
    }

    protected function finalizeDatabaseSetup(): \Illuminate\Http\JsonResponse
    {
        $this->logInstaller('Iniciando finalizeDatabaseSetup');

        $config = $this->instaladorService->getEnvironmentDatabaseConfig();
        $config = $this->instaladorService->applyDatabaseConfig($config);
        $this->logInstaller('Config DB aplicada: driver=' . $config['driver'] . ' database=' . $config['database']);

        $this->logInstaller('Chamando runMigrations...');
        $migrateResult = $this->instaladorService->runMigrations();
        $this->logInstaller('runMigrations retornou: success=' . ($migrateResult['success'] ? 'YES' : 'NO'));

        if (!$migrateResult['success']) {
            $this->logInstaller('ERRO migrate: ' . $migrateResult['message']);
            return response()->json([
                'status' => 'error',
                'message' => $migrateResult['message'],
            ], 500);
        }

        $this->logInstaller('Migrations OK');
        return response()->json([
            'status' => 'success',
            'message' => 'Banco de dados configurado e migrations executadas com sucesso.',
            'data' => [
                'migration_output' => $migrateResult['output'] ?? '',
            ],
            'redirect' => route('install.admin'),
        ]);
    }

    public function showMigrate()
    {
        if ($this->instaladorService->isInstalled()) {
            return redirect()->route('admin.login');
        }

        return view('installer.migrate');
    }

    public function runMigrate()
    {
        if ($this->instaladorService->isInstalled()) {
            return response()->json(['status' => 'error', 'message' => 'Sistema ja instalado.']);
        }

        $config = $this->instaladorService->getEnvironmentDatabaseConfig();
        $this->instaladorService->applyDatabaseConfig($config);

        $migrateResult = $this->instaladorService->runMigrations();

        if (!$migrateResult['success']) {
            return response()->json([
                'status' => 'error',
                'message' => $migrateResult['message'],
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Migrations executadas com sucesso.',
            'redirect' => route('install.admin'),
        ]);
    }

    public function admin()
    {
        if ($this->instaladorService->isInstalled()) {
            return redirect()->route('admin.login');
        }

        return view('installer.admin');
    }

    public function createAdmin(Request $request)
    {
        if ($this->instaladorService->isInstalled()) {
            return response()->json(['status' => 'error', 'message' => 'Sistema já instalado.']);
        }

        $this->instaladorService->applyDatabaseConfig(
            $this->instaladorService->getEnvironmentDatabaseConfig(),
        );

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'app_name' => 'nullable|string|max:255',
            'app_description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'O nome do administrador é obrigatório.',
            'email.required' => 'O e-mail do administrador é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação de senha não coincide.',
        ]);

        try {
            $user = $this->instaladorService->createAdminUser($validated);

            $this->instaladorService->setInitialConfig($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Administrador criado com sucesso.',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'redirect' => route('install.finish'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao criar administrador: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function finish()
    {
        if ($this->instaladorService->hasInstallationFile()) {
            return redirect()->route('admin.login');
        }

        return view('installer.finish');
    }

    public function complete()
    {
        if ($this->instaladorService->hasInstallationFile()) {
            return response()->json(['status' => 'error', 'message' => 'Sistema já instalado.']);
        }

        try {
            $this->instaladorService->completeInstallation();
            $this->instaladorService->protectInstaller();

            return response()->json([
                'status' => 'success',
                'message' => 'Instalação concluída com sucesso!',
                'redirect' => route('admin.login'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao finalizar instalação: ' . $e->getMessage(),
            ], 500);
        }
    }
}
