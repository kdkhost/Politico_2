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

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use FilesystemIterator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Response;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class BackupController extends Controller
{
    public function index()
    {
        $backups = Backup::orderByDesc('created_at')->paginate(config('sistema.pagination_per_page', 15));
        $stats = [
            'total' => Backup::count(),
            'total_size' => Backup::sum('size'),
            'last_backup' => Backup::latest()->first()?->created_at,
        ];

        return view('admin.backups.index', compact('backups', 'stats'));
    }

    public function list(Request $request)
    {
        try {
            $backups = Backup::orderByDesc('created_at')->paginate(config('sistema.pagination_per_page', 15));

            return response()->json([
                'status' => 'success',
                'success' => true,
                'data' => $backups->items(),
                'draw' => (int) $request->draw,
                'recordsTotal' => $backups->total(),
                'recordsFiltered' => $backups->total(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao listar backups: ' . $e->getMessage()], 500);
        }
    }

    public function createForm()
    {
        return view('admin.backups.create');
    }

    public function create(Request $request)
    {
        try {
            $type = $request->input('type', 'full');
            $extension = class_exists(ZipArchive::class) ? 'zip' : 'json';
            $filename = 'backup_' . now()->format('Ymd_His') . '.' . $extension;
            $path = storage_path("app/backups/{$filename}");
            $dir = dirname($path);

            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $createdByPackage = false;
            $commands = Artisan::all();

            if (($type === 'db' || $type === 'full') && isset($commands['backup:run'])) {
                Artisan::call('backup:run', [
                    '--only-db' => $type === 'db',
                    '--filename' => $filename,
                ]);
                $createdByPackage = file_exists($path);
            }

            if (!$createdByPackage) {
                $this->createFallbackBackup($path, $type, $request->boolean('incluir_midia'));
            }

            $size = file_exists($path) ? filesize($path) : 0;

            $backup = Backup::create([
                'filename' => $filename,
                'path' => "backups/{$filename}",
                'size' => $size,
                'type' => $type,
                'status' => 'completed',
                'notes' => $request->input('notes', ''),
            ]);

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Backup criado com sucesso.',
                'data' => $backup,
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao criar backup: ' . $e->getMessage()], 500);
        }
    }

    public function download(int $id)
    {
        try {
            $backup = Backup::findOrFail($id);
            $path = $this->secureBackupPath($backup);

            if (!file_exists($path)) {
                return response()->json(['status' => 'error', 'success' => false, 'message' => 'Arquivo de backup nao encontrado.'], 404);
            }

            return Response::download($path, $backup->filename);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao baixar backup: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $backup = Backup::findOrFail($id);
            $path = $this->secureBackupPath($backup);

            if (file_exists($path)) {
                @unlink($path);
            }

            $backup->delete();

            return response()->json(['status' => 'success', 'success' => true, 'message' => 'Backup excluido com sucesso.', 'reload' => true]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao excluir backup.'], 500);
        }
    }

    public function saveConfig(Request $request)
    {
        try {
            $validated = $request->validate([
                'frequencia' => 'nullable|string|in:diario,semanal,mensal',
                'horario' => 'nullable|date_format:H:i',
                'retencao' => 'nullable|integer|min:1|max:365',
                'incluir_midia' => 'boolean',
            ]);

            foreach ($validated as $key => $value) {
                setting()->set("backup_{$key}", $value);
            }

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Configuracoes de backup salvas com sucesso.',
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao salvar configuracoes: ' . $e->getMessage()], 500);
        }
    }

    public function restore(Request $request)
    {
        try {
            $validated = $request->validate([
                'backup_id' => 'required|exists:backups,id',
            ]);

            $backup = Backup::findOrFail($validated['backup_id']);
            $path = $this->secureBackupPath($backup);

            if (!file_exists($path)) {
                return response()->json(['status' => 'error', 'success' => false, 'message' => 'Arquivo de backup nao encontrado.'], 404);
            }

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Restauracao preparada. Em producao, valide o arquivo antes de restaurar.',
                'path' => $path,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao restaurar backup: ' . $e->getMessage()], 500);
        }
    }

    protected function createFallbackBackup(string $path, string $type, bool $includeMedia): void
    {
        $manifest = [
            'app' => config('app.name'),
            'url' => config('app.url'),
            'type' => $type,
            'created_at' => now()->toIso8601String(),
            'driver' => class_exists(ZipArchive::class) ? 'ziparchive' : 'json-manifest',
            'note' => 'Backup gerado pelo fallback interno quando o comando backup:run nao esta disponivel.',
        ];

        if (!class_exists(ZipArchive::class)) {
            file_put_contents($path, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return;
        }

        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Nao foi possivel criar o arquivo ZIP de backup.');
        }

        $zip->addFromString('manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        if ($type === 'db' || $type === 'full') {
            $zip->addFromString(
                'database/README.txt',
                'Dump SQL automatico nao disponivel neste ambiente. Use o backup do cPanel ou mysqldump para restauracao completa do banco.'
            );
        }

        if ($type !== 'db') {
            $paths = [
                'app',
                'bootstrap',
                'config',
                'database',
                'resources',
                'routes',
                'public/build',
                'public/assets',
                'public/img',
            ];

            if ($includeMedia) {
                $paths[] = 'storage/app/public';
            }

            foreach ($paths as $relativePath) {
                $absolutePath = base_path($relativePath);
                if (is_dir($absolutePath)) {
                    $this->addDirectoryToZip($zip, $absolutePath, $relativePath);
                } elseif (is_file($absolutePath)) {
                    $zip->addFile($absolutePath, $relativePath);
                }
            }

            foreach (['composer.json', 'composer.lock', 'package.json', 'vite.config.js', '.htaccess'] as $file) {
                $absolutePath = base_path($file);
                if (is_file($absolutePath)) {
                    $zip->addFile($absolutePath, $file);
                }
            }
        }

        $zip->close();
    }

    protected function addDirectoryToZip(ZipArchive $zip, string $absolutePath, string $relativePath): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($absolutePath, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
        );

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $fullPath = $file->getPathname();
            $zipPath = str_replace('\\', '/', $relativePath . '/' . ltrim(substr($fullPath, strlen($absolutePath)), '\\/'));

            if ($this->shouldExcludeBackupPath($zipPath)) {
                continue;
            }

            $zip->addFile($fullPath, $zipPath);
        }
    }

    protected function secureBackupPath(Backup $backup): string
    {
        $backupBase = storage_path('app/backups');

        if (!is_dir($backupBase)) {
            throw new \RuntimeException('Diretorio de backups nao encontrado.');
        }

        $realBase = realpath($backupBase);
        if ($realBase === false) {
            throw new \RuntimeException('Diretorio de backups invalido.');
        }

        $relativePath = ltrim(str_replace('\\', '/', (string) $backup->path), '/');
        if ($relativePath === '' || str_contains($relativePath, '../') || str_contains($relativePath, '..\\')) {
            throw new \RuntimeException('Caminho de backup invalido.');
        }

        $candidate = $realBase . DIRECTORY_SEPARATOR . basename($relativePath);
        $realCandidate = realpath($candidate);
        $resolved = $realCandidate !== false ? $realCandidate : $candidate;

        $normalizedBase = rtrim(str_replace('\\', '/', $realBase), '/');
        $normalizedResolved = rtrim(str_replace('\\', '/', $resolved), '/');

        if (!str_starts_with($normalizedResolved, $normalizedBase . '/')
            && $normalizedResolved !== $normalizedBase) {
            throw new \RuntimeException('Caminho de backup fora da pasta permitida.');
        }

        return $resolved;
    }

    protected function shouldExcludeBackupPath(string $zipPath): bool
    {
        $zipPath = ltrim(str_replace('\\', '/', $zipPath), '/');

        if (
            str_contains($zipPath, '/node_modules/')
            || str_contains($zipPath, '/vendor/')
            || str_starts_with($zipPath, 'public/storage/')
            || str_starts_with($zipPath, 'public/uploads/')
            || str_starts_with($zipPath, 'storage/logs/')
            || str_starts_with($zipPath, 'storage/app/backups/')
            || $zipPath === '.env'
        ) {
            return true;
        }

        return preg_match('/\.zip$/i', $zipPath) === 1;
    }
}
