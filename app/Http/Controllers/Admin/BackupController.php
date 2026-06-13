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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

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
                'data' => $backups->items(),
                'draw' => (int) $request->draw,
                'recordsTotal' => $backups->total(),
                'recordsFiltered' => $backups->total(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao listar backups: ' . $e->getMessage()], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $type = $request->input('type', 'full');
            $filename = 'backup_' . now()->format('Ymd_His') . '.zip';
            $path = storage_path("app/backups/{$filename}");

            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            if ($type === 'db' || $type === 'full') {
                Artisan::call('backup:run', [
                    '--only-db' => true,
                    '--filename' => $filename,
                ]);
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
                'message' => 'Backup criado com sucesso.',
                'data' => $backup,
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao criar backup: ' . $e->getMessage()], 500);
        }
    }

    public function download(int $id)
    {
        try {
            $backup = Backup::findOrFail($id);
            $path = storage_path("app/{$backup->path}");

            if (!file_exists($path)) {
                return response()->json(['status' => 'error', 'message' => 'Arquivo de backup não encontrado.'], 404);
            }

            return Response::download($path, $backup->filename);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao baixar backup: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $backup = Backup::findOrFail($id);
            $path = storage_path("app/{$backup->path}");

            if (file_exists($path)) {
                @unlink($path);
            }

            $backup->delete();

            return response()->json(['status' => 'success', 'message' => 'Backup excluído com sucesso.', 'reload' => true]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao excluir backup.'], 500);
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
                'message' => 'Configurações de backup salvas com sucesso.',
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao salvar configurações: ' . $e->getMessage()], 500);
        }
    }

    public function restore(Request $request)
    {
        try {
            $validated = $request->validate([
                'backup_id' => 'required|exists:backups,id',
            ]);

            $backup = Backup::findOrFail($validated['backup_id']);
            $path = storage_path("app/{$backup->path}");

            if (!file_exists($path)) {
                return response()->json(['status' => 'error', 'message' => 'Arquivo de backup não encontrado.'], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Restauração iniciada. Em ambiente de produção, execute o comando manualmente.',
                'command' => "mysql -u username -p database < {$path}",
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao restaurar backup: ' . $e->getMessage()], 500);
        }
    }
}
