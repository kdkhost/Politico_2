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
 * LicenseBoxExternalAPI — Classe original de licenciamento adaptada para Laravel
 */

namespace App\Services\License;

use Illuminate\Support\Facades\Log;

class LicenseBoxExternalAPI
{
    private string $product_id;
    private string $api_url;
    private string $api_key;
    private string $api_language;
    private string $current_version;
    private string $verify_type;
    private int $verification_period;
    private string $current_path;
    private string $root_path;
    private string $license_file;

    public bool $debug = false;
    public bool $showUpdateProgress = true;

    public function __construct()
    {
        $this->product_id = config('license.product_code', 'C73B74F0');
        $this->api_url = rtrim(config('license.api_url', 'https://ativador.kdkhost.com.br/'), '/');
        $this->api_key = (string) config('license.api_key', '');
        $this->api_language = config('license.language', 'portuguese');
        $this->current_version = config('license.version', 'v1.0.0');
        $this->verify_type = config('license.verification_type', 'non_envato');
        $this->verification_period = (int) config('license.verification_period', 1);
        $this->current_path = realpath(__DIR__);
        $this->root_path = realpath($this->current_path . '/../../..');
        $this->license_file = config('license.license_file_path', storage_path('app/license/.lic'));
    }

    public function checkLocalLicenseExist(): bool
    {
        return is_file($this->license_file);
    }

    public function getCurrentVersion(): string
    {
        return $this->current_version;
    }

    public function callApi(string $method, string $url, $data = null): string
    {
        if (trim($this->api_key) === '') {
            return json_encode([
                'status' => false,
                'message' => 'Chave da API de licenciamento não configurada.',
            ], JSON_UNESCAPED_UNICODE);
        }

        if ($method === 'GET' && $data) {
            $url = sprintf('%s?%s', $url, http_build_query($data));
        }

        $headers = $this->buildApiHeaders();

        if (!function_exists('curl_init')) {
            return $this->callApiWithStream($method, $url, $data, $headers);
        }

        $curl = curl_init();

        switch ($method) {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
            case 'PUT':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

        $result = curl_exec($curl);

        if (!$result && !$this->debug) {
            curl_close($curl);
            return $this->unavailableResponse();
        }

        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($http_status !== 200) {
            curl_close($curl);
            if ($this->debug) {
                $temp_decode = json_decode($result, true);
                return json_encode([
                    'status' => false,
                    'message' => (!empty($temp_decode['error'])) ? $temp_decode['error'] : ($temp_decode['message'] ?? ''),
                ]);
            }
            return json_encode([
                'status' => false,
                'message' => 'O servidor retornou uma resposta inválida. Entre em contato com o suporte.',
            ], JSON_UNESCAPED_UNICODE);
        }

        curl_close($curl);
        return $this->stripUtf8Bom((string) $result);
    }

    private function buildApiHeaders(): array
    {
        $this_server_name = getenv('SERVER_NAME') ?: ($_SERVER['SERVER_NAME'] ?? (getenv('HTTP_HOST') ?: ($_SERVER['HTTP_HOST'] ?? '')));
        $this_http_or_https = (
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        ) ? 'https://' : 'http://';
        $this_url = $this_http_or_https . $this_server_name . ($_SERVER['REQUEST_URI'] ?? '');
        $this_ip = getenv('SERVER_ADDR') ?: (($_SERVER['SERVER_ADDR'] ?? null) ?: ($this->getIpFromThirdParty() ?: gethostbyname(gethostname())));

        return [
            'Content-Type: application/json',
            'LB-API-KEY: ' . $this->api_key,
            'LB-URL: ' . $this_url,
            'LB-IP: ' . $this_ip,
            'LB-LANG: ' . $this->api_language,
        ];
    }

    private function callApiWithStream(string $method, string $url, $data, array $headers): string
    {
        if (!ini_get('allow_url_fopen')) {
            Log::warning('Licenca: cURL indisponivel e allow_url_fopen desabilitado.');

            return $this->unavailableResponse();
        }

        $contextOptions = [
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $headers),
                'ignore_errors' => true,
                'timeout' => 30,
            ],
        ];

        if (in_array($method, ['POST', 'PUT'], true) && $data) {
            $contextOptions['http']['content'] = $data;
        }

        $context = stream_context_create($contextOptions);
        $result = @file_get_contents($url, false, $context);
        $httpStatus = 0;

        foreach (($http_response_header ?? []) as $header) {
            if (preg_match('/^HTTP\/\S+\s+(\d+)/', (string) $header, $match) === 1) {
                $httpStatus = (int) $match[1];
                break;
            }
        }

        if ($result === false || $result === '') {
            return $this->unavailableResponse();
        }

        if ($httpStatus !== 200) {
            return json_encode([
                'status' => false,
                'message' => 'O servidor retornou uma resposta inválida. Entre em contato com o suporte.',
            ], JSON_UNESCAPED_UNICODE);
        }

        return $this->stripUtf8Bom((string) $result);
    }

    private function unavailableResponse(): string
    {
        return json_encode([
            'status' => false,
            'message' => 'O servidor não está disponível no momento. Tente novamente.',
        ], JSON_UNESCAPED_UNICODE);
    }

    public function checkConnection(): array
    {
        $get_data = $this->callApi('POST', $this->api_url . '/api/check_connection_ext');
        return json_decode($get_data, true) ?? [];
    }

    public function getLatestVersion(): array
    {
        $data_array = ['product_id' => $this->product_id];
        $get_data = $this->callApi('POST', $this->api_url . '/api/latest_version', json_encode($data_array));
        return json_decode($get_data, true) ?? [];
    }

    public function activateLicense(string $license, string $client, bool $createLic = true): array
    {
        $data_array = [
            'product_id' => $this->product_id,
            'license_code' => $license,
            'client_name' => $client,
            'verify_type' => $this->verify_type,
        ];

        $get_data = $this->callApi('POST', $this->api_url . '/api/activate_license', json_encode($data_array));
        $response = json_decode($get_data, true) ?? [];

        if (!empty($createLic)) {
            if (!empty($response['status'])) {
                $licfile = trim($response['lic_response'] ?? '');
                $dir = dirname($this->license_file);
                if (!is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }
                file_put_contents($this->license_file, $licfile, LOCK_EX);
            } else {
                @chmod($this->license_file, 0777);
                if (is_writable($this->license_file)) {
                    @unlink($this->license_file);
                }
            }
        }

        return $response;
    }

    public function verifyLicense(bool $timeBasedCheck = false, $license = false, $client = false): array
    {
        if (!empty($license) && !empty($client)) {
            $data_array = [
                'product_id' => $this->product_id,
                'license_file' => null,
                'license_code' => $license,
                'client_name' => $client,
            ];
        } else {
            if (is_file($this->license_file)) {
                $data_array = [
                    'product_id' => $this->product_id,
                    'license_file' => file_get_contents($this->license_file),
                    'license_code' => null,
                    'client_name' => null,
                ];
            } else {
                $data_array = [];
            }
        }

        $res = ['status' => true, 'message' => 'Verificado! Obrigado por comprar.'];

        if ($timeBasedCheck && $this->verification_period > 0) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $type = $this->verification_period;
            $today = date('d-m-Y');

            if (empty($_SESSION['60449065126e724'] ?? null)) {
                $_SESSION['60449065126e724'] = '00-00-0000';
            }

            if ($type === 1) {
                $type_text = '1 day';
            } elseif ($type === 3) {
                $type_text = '3 days';
            } elseif ($type === 7) {
                $type_text = '1 week';
            } elseif ($type === 30) {
                $type_text = '1 month';
            } elseif ($type === 90) {
                $type_text = '3 months';
            } elseif ($type === 365) {
                $type_text = '1 year';
            } else {
                $type_text = $type . ' days';
            }

            if (strtotime($today) >= strtotime($_SESSION['60449065126e724'])) {
                $get_data = $this->callApi('POST', $this->api_url . '/api/verify_license', json_encode($data_array));
                $res = json_decode($get_data, true) ?? $res;

                if (!empty($res['status'])) {
                    $tomo = date('d-m-Y', strtotime($today . ' + ' . $type_text));
                    $_SESSION['60449065126e724'] = $tomo;
                }
            }
        } else {
            $get_data = $this->callApi('POST', $this->api_url . '/api/verify_license', json_encode($data_array));
            $res = json_decode($get_data, true) ?? $res;
        }

        return $res;
    }

    public function deactivateLicense($license = false, $client = false): array
    {
        if (!empty($license) && !empty($client)) {
            $data_array = [
                'product_id' => $this->product_id,
                'license_file' => null,
                'license_code' => $license,
                'client_name' => $client,
            ];
        } else {
            if (is_file($this->license_file)) {
                $data_array = [
                    'product_id' => $this->product_id,
                    'license_file' => file_get_contents($this->license_file),
                    'license_code' => null,
                    'client_name' => null,
                ];
            } else {
                $data_array = [];
            }
        }

        $get_data = $this->callApi('POST', $this->api_url . '/api/deactivate_license', json_encode($data_array));
        $response = json_decode($get_data, true) ?? [];

        if (!empty($response['status'])) {
            @chmod($this->license_file, 0777);
            if (is_writable($this->license_file)) {
                @unlink($this->license_file);
            }
        }

        return $response;
    }

    public function checkUpdate(): array
    {
        $data_array = [
            'product_id' => $this->product_id,
            'current_version' => $this->current_version,
        ];

        $get_data = $this->callApi('POST', $this->api_url . '/api/check_update', json_encode($data_array));
        return json_decode($get_data, true) ?? [];
    }

    public function downloadUpdate(string $update_id, $type, string $version, $license = false, $client = false, $db_for_import = false): void
    {
        throw new \RuntimeException('Atualizador automatico remoto bloqueado por seguranca. Use somente pacote validado, backup e aprovacao manual.');
    }

    private function progress($resource, int $download_size, int $downloaded, int $upload_size, int $uploaded): void
    {
        // Callback mantido apenas para compatibilidade com a assinatura antiga.
    }

    private function getIpFromThirdParty(): string
    {
        if (!function_exists('curl_init')) {
            return trim((string) @file_get_contents('http://ipecho.net/plain'));
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'http://ipecho.net/plain');
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($curl);
        curl_close($curl);
        return trim($response);
    }

    private function getRemoteFilesize(string $url): string
    {
        if (!function_exists('curl_init')) {
            return '';
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_NOBODY, true);

        $this_server_name = getenv('SERVER_NAME') ?: ($_SERVER['SERVER_NAME'] ?? (getenv('HTTP_HOST') ?: ($_SERVER['HTTP_HOST'] ?? '')));
        $this_http_or_https = (
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        ) ? 'https://' : 'http://';
        $this_url = $this_http_or_https . $this_server_name . ($_SERVER['REQUEST_URI'] ?? '');
        $this_ip = getenv('SERVER_ADDR') ?: (($_SERVER['SERVER_ADDR'] ?? null) ?: ($this->getIpFromThirdParty() ?: gethostbyname(gethostname())));

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'LB-API-KEY: ' . $this->api_key,
            'LB-URL: ' . $this_url,
            'LB-IP: ' . $this_ip,
            'LB-LANG: ' . $this->api_language,
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);

        curl_exec($curl);
        $filesize = curl_getinfo($curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        curl_close($curl);

        if ($filesize) {
            if ($filesize < 1024) {
                return round($filesize, 2) . ' B';
            }
            if ($filesize < 1048576) {
                return round($filesize / 1024, 2) . ' KB';
            }
            if ($filesize < 1073741824) {
                return round($filesize / 1048576, 2) . ' MB';
            }
            if ($filesize < 1099511627776) {
                return round($filesize / 1073741824, 2) . ' GB';
            }
        }

        return '0 B';
    }

    private function stripUtf8Bom(string $value): string
    {
        return preg_replace('/^\xEF\xBB\xBF/', '', $value) ?? $value;
    }
}
