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
        $this->api_key = config('license.api_key', '8D7D3C0AE370A633F0D6');
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
            default:
                if ($data) {
                    $url = sprintf('%s?%s', $url, http_build_query($data));
                }
        }

        $this_server_name = getenv('SERVER_NAME') ?: ($_SERVER['SERVER_NAME'] ?? (getenv('HTTP_HOST') ?: ($_SERVER['HTTP_HOST'] ?? '')));
        $this_http_or_https = (
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        ) ? 'https://' : 'http://';
        $this_url = $this_http_or_https . $this_server_name . ($_SERVER['REQUEST_URI'] ?? '');
        $this_ip = getenv('SERVER_ADDR') ?: (($_SERVER['SERVER_ADDR'] ?? null) ?: ($this->getIpFromThirdParty() ?: gethostbyname(gethostname())));

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'LB-API-KEY: ' . $this->api_key,
            'LB-URL: ' . $this_url,
            'LB-IP: ' . $this_ip,
            'LB-LANG: ' . $this->api_language,
        ]);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

        $result = curl_exec($curl);

        if (!$result && !$this->debug) {
            curl_close($curl);
            return json_encode([
                'status' => false,
                'message' => 'O servidor não está disponível no momento. Tente novamente.',
            ], JSON_UNESCAPED_UNICODE);
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
        if (!empty($license) && !empty($client)) {
            $data_array = [
                'license_file' => null,
                'license_code' => $license,
                'client_name' => $client,
            ];
        } else {
            if (is_file($this->license_file)) {
                $data_array = [
                    'license_file' => file_get_contents($this->license_file),
                    'license_code' => null,
                    'client_name' => null,
                ];
            } else {
                $data_array = [];
            }
        }

        ob_end_flush();
        ob_implicit_flush(true);
        $version = str_replace('.', '_', $version);
        ob_start();

        $source_size = $this->api_url . '/api/get_update_size/main/' . $update_id;
        echo 'Preparando para baixar a atualiza\u00e7\u00e3o principal...<br>';
        if ($this->showUpdateProgress) {
            echo '<script>document.getElementById(\'prog\').value = 1;</script>';
        }
        ob_flush();

        echo 'Tamanho da atualiza\u00e7\u00e3o principal: ' . $this->getRemoteFilesize($source_size) . ' (Por favor, n\u00e3o atualize a p\u00e1gina).<br>';
        if ($this->showUpdateProgress) {
            echo '<script>document.getElementById(\'prog\').value = 5;</script>';
        }
        ob_flush();

        $ch = curl_init();
        $source = $this->api_url . '/api/download_update/main/' . $update_id;
        curl_setopt($ch, CURLOPT_URL, $source);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_array);

        $this_server_name = getenv('SERVER_NAME') ?: ($_SERVER['SERVER_NAME'] ?? (getenv('HTTP_HOST') ?: ($_SERVER['HTTP_HOST'] ?? '')));
        $this_http_or_https = (
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        ) ? 'https://' : 'http://';
        $this_url = $this_http_or_https . $this_server_name . ($_SERVER['REQUEST_URI'] ?? '');
        $this_ip = getenv('SERVER_ADDR') ?: (($_SERVER['SERVER_ADDR'] ?? null) ?: ($this->getIpFromThirdParty() ?: gethostbyname(gethostname())));

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'LB-API-KEY: ' . $this->api_key,
            'LB-URL: ' . $this_url,
            'LB-IP: ' . $this_ip,
            'LB-LANG: ' . $this->api_language,
        ]);

        if ($this->showUpdateProgress) {
            curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, [$this, 'progress']);
            curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

        echo 'Baixando a atualiza\u00e7\u00e3o principal...<br>';
        if ($this->showUpdateProgress) {
            echo '<script>document.getElementById(\'prog\').value = 10;</script>';
        }
        ob_flush();

        $data = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($http_status !== 200) {
            curl_close($ch);
            if ($http_status === 401) {
                exit('<br>Seu per\u00edodo de atualiza\u00e7\u00e3o terminou ou sua licen\u00e7a \u00e9 inv\u00e1lida. Entre em contato com o suporte.');
            }
            exit('<br>O servidor retornou uma resposta inv\u00e1lida. Entre em contato com o suporte.');
        }

        curl_close($ch);

        $destination = $this->root_path . '/update_main_' . $version . '.zip';
        $file = @fopen($destination, 'w+');

        if (!$file) {
            exit('<br>A pasta n\u00e3o tem permiss\u00e3o de grava\u00e7\u00e3o ou o caminho do arquivo de atualiza\u00e7\u00e3o n\u00e3o p\u00f4de ser resolvido. Entre em contato com o suporte.');
        }

        fputs($file, $data);
        fclose($file);

        if ($this->showUpdateProgress) {
            echo '<script>document.getElementById(\'prog\').value = 65;</script>';
        }
        ob_flush();

        $zip = new \ZipArchive();
        $res = $zip->open($destination);

        if ($res === true) {
            $zip->extractTo($this->root_path . '/');
            $zip->close();
            @unlink($destination);
            echo 'Principais arquivos de atualiza\u00e7\u00e3o baixados e extra\u00eddos.<br><br>';
            if ($this->showUpdateProgress) {
                echo '<script>document.getElementById(\'prog\').value = 75;</script>';
            }
            ob_flush();
        } else {
            echo 'A atualiza\u00e7\u00e3o da extra\u00e7\u00e3o do zip falhou.<br><br>';
            ob_flush();
        }

        if ($type === true) {
            $source_size = $this->api_url . '/api/get_update_size/sql/' . $update_id;
            echo 'Preparando para baixar a atualiza\u00e7\u00e3o SQL...<br>';
            ob_flush();
            echo 'Tamanho da atualiza\u00e7\u00e3o do SQL: ' . $this->getRemoteFilesize($source_size) . ' (Por favor, n\u00e3o atualize a p\u00e1gina).<br>';
            if ($this->showUpdateProgress) {
                echo '<script>document.getElementById(\'prog\').value = 85;</script>';
            }
            ob_flush();

            $ch = curl_init();
            $source = $this->api_url . '/api/download_update/sql/' . $update_id;
            curl_setopt($ch, CURLOPT_URL, $source);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_array);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'LB-API-KEY: ' . $this->api_key,
                'LB-URL: ' . $this_url,
                'LB-IP: ' . $this_ip,
                'LB-LANG: ' . $this->api_language,
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

            echo 'Fazendo o download da atualiza\u00e7\u00e3o do SQL...<br>';
            if ($this->showUpdateProgress) {
                echo '<script>document.getElementById(\'prog\').value = 90;</script>';
            }
            ob_flush();

            $data = curl_exec($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($http_status !== 200) {
                curl_close($ch);
                exit('O servidor retornou uma resposta inv\u00e1lida. Entre em contato com o suporte.');
            }

            curl_close($ch);

            $destination = $this->root_path . '/update_sql_' . $version . '.sql';
            $file = @fopen($destination, 'w+');

            if (!$file) {
                exit('A pasta n\u00e3o tem permiss\u00e3o de grava\u00e7\u00e3o ou o caminho do arquivo de atualiza\u00e7\u00e3o n\u00e3o p\u00f4de ser resolvido. Entre em contato com o suporte.');
            }

            fputs($file, $data);
            fclose($file);

            echo 'Arquivos de atualiza\u00e7\u00e3o SQL baixados.<br><br>';
            if ($this->showUpdateProgress) {
                echo '<script>document.getElementById(\'prog\').value = 95;</script>';
            }
            ob_flush();

            if (is_array($db_for_import)) {
                if (!empty($db_for_import['db_host']) && !empty($db_for_import['db_user']) && !empty($db_for_import['db_name'])) {
                    $db_host = strip_tags(trim($db_for_import['db_host']));
                    $db_user = strip_tags(trim($db_for_import['db_user']));
                    $db_pass = strip_tags(trim($db_for_import['db_pass']));
                    $db_name = strip_tags(trim($db_for_import['db_name']));

                    $con = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
                    if (mysqli_connect_errno()) {
                        echo 'O aplicativo foi atualizado com sucesso, mas a importa\u00e7\u00e3o autom\u00e1tica de SQL falhou. Importe manualmente o arquivo SQL no banco de dados.';
                    } else {
                        $templine = '';
                        $lines = file($destination);
                        foreach ($lines as $line) {
                            if (substr($line, 0, 2) === '--' || trim($line) === '') {
                                continue;
                            }
                            $templine .= $line;
                            if (substr(trim($line), -1, 1) === ';') {
                                @mysqli_query($con, $templine);
                                $templine = '';
                            }
                        }
                        @chmod($destination, 0777);
                        if (is_writable($destination)) {
                            @unlink($destination);
                        }
                        echo 'O aplicativo foi atualizado com sucesso e o arquivo SQL foi importado automaticamente.';
                    }
                } else {
                    echo 'O aplicativo foi atualizado com sucesso, mas a importa\u00e7\u00e3o autom\u00e1tica de SQL falhou. Importe manualmente o arquivo SQL no banco de dados.';
                }
            } else {
                echo 'O aplicativo foi atualizado com sucesso. Importe manualmente o arquivo SQL baixado no banco de dados.';
            }

            if ($this->showUpdateProgress) {
                echo '<script>document.getElementById(\'prog\').value = 100;</script>';
            }
            ob_flush();
        } else {
            if ($this->showUpdateProgress) {
                echo '<script>document.getElementById(\'prog\').value = 100;</script>';
            }
            echo 'O aplicativo foi atualizado com sucesso. N\u00e3o houve atualiza\u00e7\u00f5es de SQL.';
            ob_flush();
        }

        ob_end_flush();
    }

    private function progress($resource, int $download_size, int $downloaded, int $upload_size, int $uploaded): void
    {
        static $prev = 0;

        if ($download_size === 0) {
            $progress = 0;
        } else {
            $progress = (int) round($downloaded * 100 / $download_size);
        }

        if ($progress !== $prev && $progress === 25) {
            $prev = $progress;
            echo '<script>document.getElementById(\'prog\').value = 22.5;</script>';
            ob_flush();
        }
        if ($progress !== $prev && $progress === 50) {
            $prev = $progress;
            echo '<script>document.getElementById(\'prog\').value = 35;</script>';
            ob_flush();
        }
        if ($progress !== $prev && $progress === 75) {
            $prev = $progress;
            echo '<script>document.getElementById(\'prog\').value = 47.5;</script>';
            ob_flush();
        }
        if ($progress !== $prev && $progress === 100) {
            $prev = $progress;
            echo '<script>document.getElementById(\'prog\').value = 60;</script>';
            ob_flush();
        }
    }

    private function getIpFromThirdParty(): string
    {
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
