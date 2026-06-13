<?php

declare(strict_types=1);

/**
 * @autor marcelo-brad rj
 * @contato Tel: +55 (21) 98132-5441
 * @contato Email: contato@kdkhost.com.br
 * @contato Telegram: @MARCELO_BRAD
 * @contato Instagram: @marcelobradrj
 * @contato WhatsApp: 5521981325441
 *
 * LicenseService - Gerenciamento de licenciamento do sistema
 */

namespace App\Services\License;

use App\Models\LicenseSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LicenseService
{
    private LicenseBoxExternalAPI $api;

    public function __construct()
    {
        $this->api = new LicenseBoxExternalAPI();
    }

    public function checkConnection(): array
    {
        if (!$this->hasApiKey()) {
            return [
                'success' => false,
                'message' => 'Chave da API de licenciamento não configurada.',
            ];
        }

        $result = $this->api->checkConnection();
        Log::info('License connection check', ['result' => $result]);

        return $result;
    }

    public function activate(string $licenseKey, ?string $clientName = null, ?string $clientEmail = null): array
    {
        if (!$this->hasApiKey()) {
            return [
                'success' => false,
                'message' => 'Chave da API de licenciamento não configurada.',
            ];
        }

        $client = $clientName ?? ($_SERVER['SERVER_NAME'] ?? gethostname());
        $cacheKey = $this->licenseCacheKey();
        $response = $this->api->activateLicense($licenseKey, $client);

        if (!empty($response['status'])) {
            LicenseSetting::updateOrCreate(
                ['license_key' => $licenseKey],
                [
                    'license_key' => $licenseKey,
                    'cliente' => $client,
                    'email_cliente' => $clientEmail ?? '',
                    'status' => 'active',
                    'activated_at' => now(),
                    'last_verified_at' => now(),
                    'next_verified_at' => now()->addDays(config('license.verification_period', 1)),
                    'current_version' => config('license.version', 'v1.0.0'),
                    'license_data' => json_encode($response, JSON_UNESCAPED_UNICODE),
                ]
            );

            Cache::put($cacheKey, true, now()->addSeconds(config('license.cache_ttl', 86400)));

            return [
                'success' => true,
                'message' => $this->normalizeMessage($response['message'] ?? 'Verificado! Obrigado por comprar.'),
                'data' => $response,
            ];
        }

        return [
            'success' => false,
            'message' => $this->normalizeMessage($response['message'] ?? 'Licença inválida ou expirada.'),
        ];
    }

    public function verify(bool $force = false): array
    {
        if ((bool) config('license.skip_check', false)) {
            return [
                'valid' => true,
                'cached' => true,
                'message' => 'Validação de licença ignorada por configuração.',
            ];
        }

        $cacheKey = $this->licenseCacheKey();

        if (!$force && Cache::has($cacheKey)) {
            return [
                'valid' => true,
                'cached' => true,
                'message' => 'Verificado! Obrigado por comprar.',
            ];
        }

        if (!$this->api->checkLocalLicenseExist()) {
            return [
                'valid' => false,
                'message' => 'Nenhuma licença encontrada.',
            ];
        }

        if (!$this->hasApiKey()) {
            return $this->verifyOfflineGrace('Chave da API de licenciamento não configurada.');
        }

        $response = $this->api->verifyLicense(true);

        if (!empty($response['status'])) {
            Cache::put($cacheKey, true, now()->addSeconds(config('license.cache_ttl', 86400)));

            LicenseSetting::where('status', 'active')->update([
                'last_verified_at' => now(),
                'next_verified_at' => now()->addDays(config('license.verification_period', 1)),
                'status' => 'active',
            ]);

            return [
                'valid' => true,
                'message' => $this->normalizeMessage($response['message'] ?? 'Verificado! Obrigado por comprar.'),
            ];
        }

        Cache::forget($cacheKey);

        LicenseSetting::where('status', 'active')->update([
            'last_verified_at' => now(),
            'status' => 'invalid',
        ]);

        return $this->verifyOfflineGrace(
            $this->normalizeMessage($response['message'] ?? 'Licença inválida ou expirada.')
        );
    }

    public function deactivate(): array
    {
        if (!$this->hasApiKey()) {
            LicenseSetting::truncate();
            Cache::forget($this->licenseCacheKey());

            return [
                'success' => true,
                'message' => 'Licença local removida sem chamada externa porque a chave da API não está configurada.',
            ];
        }

        $response = $this->api->deactivateLicense();

        LicenseSetting::truncate();
        Cache::forget($this->licenseCacheKey());

        return [
            'success' => !empty($response['status']),
            'message' => !empty($response['status'])
                ? 'Licença desativada com sucesso.'
                : $this->normalizeMessage($response['message'] ?? 'Erro ao desativar licença.'),
        ];
    }

    public function getStatus(): array
    {
        $localData = LicenseSetting::first();
        $verified = Cache::has($this->licenseCacheKey());
        $licenseExists = $this->api->checkLocalLicenseExist();

        return [
            'activated' => $licenseExists && $localData !== null,
            'verified' => $verified,
            'license_key' => $localData?->license_key,
            'cliente' => $localData?->cliente,
            'email_cliente' => $localData?->email_cliente,
            'domain' => config('app.url'),
            'status' => $localData?->status,
            'current_version' => $localData?->current_version ?? config('license.version', 'v1.0.0'),
            'latest_version' => $localData?->latest_version,
            'activated_at' => $localData?->activated_at,
            'last_verified_at' => $localData?->last_verified_at,
            'next_verified_at' => $localData?->next_verified_at,
            'expires_at' => null,
            'update_available' => $localData?->update_available ?? false,
        ];
    }

    private function licenseCacheKey(): string
    {
        return 'license_verified_' . md5((string) config('license.product_code'));
    }

    private function normalizeMessage(string $message): string
    {
        return preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', static function (array $match): string {
            return html_entity_decode('&#x' . $match[1] . ';', ENT_QUOTES, 'UTF-8');
        }, $message) ?? $message;
    }

    private function verifyOfflineGrace(?string $fallbackMessage = null): array
    {
        $setting = LicenseSetting::query()->where('status', 'active')->latest('last_verified_at')->first();

        if (!$setting) {
            return [
                'valid' => false,
                'message' => $fallbackMessage ?? 'Licença não configurada.',
            ];
        }

        $graceDays = max(0, (int) config('license.offline_grace_days', 7));
        $lastVerifiedAt = $setting->last_verified_at ?? $setting->activated_at;

        if ($lastVerifiedAt && $lastVerifiedAt->copy()->addDays($graceDays)->isFuture()) {
            return [
                'valid' => true,
                'offline_grace' => true,
                'message' => 'Licença validada em modo de tolerância offline.',
            ];
        }

        return [
            'valid' => false,
            'message' => $fallbackMessage ?? 'Não foi possível validar a licença e a tolerância offline expirou.',
        ];
    }

    private function hasApiKey(): bool
    {
        return trim((string) config('license.api_key', '')) !== '';
    }

    public function getCurrentVersion(): string
    {
        $setting = LicenseSetting::first();

        return $setting?->current_version ?? config('license.version', 'v1.0.0');
    }

    public function checkForUpdates(): array
    {
        if (!$this->hasApiKey()) {
            return [
                'has_update' => false,
                'message' => 'Chave da API de licenciamento não configurada.',
            ];
        }

        $response = $this->api->checkUpdate();

        if (!empty($response['status'])) {
            $latestVersion = $response['version'] ?? '';
            $hasUpdate = $response['has_update'] ?? false;

            if ($hasUpdate) {
                LicenseSetting::where('status', 'active')->update([
                    'latest_version' => $latestVersion,
                    'update_available' => true,
                ]);
            }

            return [
                'has_update' => (bool) $hasUpdate,
                'latest_version' => $latestVersion,
                'update_id' => $response['update_id'] ?? null,
                'sql_update' => $response['sql_update'] ?? false,
                'changelog' => $response['changelog'] ?? '',
                'message' => $response['message'] ?? '',
            ];
        }

        return [
            'has_update' => false,
            'message' => $response['message'] ?? 'O servidor não está disponível no momento. Tente novamente.',
        ];
    }

    public function downloadUpdate(string $type = 'main'): array
    {
        $updateInfo = $this->checkForUpdates();

        if (!$updateInfo['has_update']) {
            return [
                'success' => false,
                'message' => $updateInfo['message'] ?? 'Nenhuma atualização disponível.',
            ];
        }

        $updateId = $updateInfo['update_id'] ?? '';
        $version = $updateInfo['latest_version'] ?? $this->getCurrentVersion();
        $hasSql = $updateInfo['sql_update'] ?? false;

        if ($updateId === '') {
            return [
                'success' => false,
                'message' => 'ID de atualização não encontrado.',
            ];
        }

        try {
            if ($type === 'main') {
                $dbConfig = config('database.connections.' . config('database.default'));
                $dbForImport = $hasSql ? [
                    'db_host' => $dbConfig['host'] ?? '',
                    'db_user' => $dbConfig['username'] ?? '',
                    'db_pass' => $dbConfig['password'] ?? '',
                    'db_name' => $dbConfig['database'] ?? '',
                ] : false;

                $this->api->showUpdateProgress = true;
                $this->api->downloadUpdate($updateId, $hasSql, $version, false, false, $dbForImport);

                LicenseSetting::where('status', 'active')->update([
                    'current_version' => $version,
                    'latest_version' => $version,
                    'update_available' => false,
                ]);

                return ['success' => true, 'type' => 'main', 'message' => 'Atualização concluída.'];
            }

            return ['success' => true, 'type' => 'sql', 'message' => 'Download SQL concluído.'];
        } catch (\Throwable $e) {
            Log::error('Update download error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'A pasta não tem permissão de gravação ou o caminho do arquivo de atualização não pôde ser resolvido. Entre em contato com o suporte.',
            ];
        }
    }

    public function applyUpdate(string $type = 'main'): array
    {
        return $this->downloadUpdate($type);
    }

    public function importSqlUpdate(string $file): array
    {
        if (!file_exists($file)) {
            return [
                'success' => false,
                'message' => 'Arquivo SQL não encontrado.',
            ];
        }

        try {
            $sql = file_get_contents($file);
            $db = config('database.default');

            if (!empty(trim((string) $sql))) {
                DB::connection($db)->unprepared((string) $sql);
                @unlink($file);

                return [
                    'success' => true,
                    'message' => 'O aplicativo foi atualizado com sucesso e o arquivo SQL foi importado automaticamente.',
                ];
            }

            @unlink($file);

            return [
                'success' => true,
                'message' => 'O aplicativo foi atualizado com sucesso. Não houve atualizações de SQL.',
            ];
        } catch (\Throwable $e) {
            Log::error('SQL import error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'O aplicativo foi atualizado com sucesso, mas a importação automática de SQL falhou. Importe manualmente o arquivo SQL no banco de dados.',
            ];
        }
    }

    public function getLatestVersion(): array
    {
        if (!$this->hasApiKey()) {
            return [
                'status' => false,
                'message' => 'Chave da API de licenciamento não configurada.',
            ];
        }

        return $this->api->getLatestVersion();
    }
}
