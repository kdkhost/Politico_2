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

namespace App\Services\Upload;

use App\Models\Media;
use App\Services\Midia\MidiaService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadService
{
    private const UNSAFE_EXTENSIONS = ['svg'];

    public function __construct(
        protected MidiaService $midiaService,
    ) {}

    public function upload(UploadedFile $file, string $pasta = 'images', array $options = []): Media
    {
        $this->validateFile($file);

        $nomeOriginal = $file->getClientOriginalName();
        $extensao = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $tamanho = $file->getSize();
        $hash = md5_file($file->getRealPath());

        $existingMedia = Media::where('hash_arquivo', $hash)->first();

        if ($existingMedia) {
            return $existingMedia;
        }

        $pasta = $this->organizeByDate($pasta);
        $nomeSanitizado = $this->sanitizeFilename(pathinfo($nomeOriginal, PATHINFO_FILENAME));
        $nomeArquivo = $nomeSanitizado . '-' . Str::random(8) . '.' . $extensao;
        $caminho = $file->storeAs($pasta, $nomeArquivo, 'public');

        if (!$caminho) {
            throw new \RuntimeException('Falha ao armazenar o arquivo.');
        }

        $data = [
            'user_id' => auth()->id(),
            'nome' => $nomeSanitizado,
            'nome_original' => $nomeOriginal,
            'caminho' => $caminho,
            'url' => Storage::url($caminho),
            'tipo' => $this->resolveTipo((string) $mimeType),
            'mime_type' => $mimeType,
            'extensao' => $extensao,
            'tamanho' => $tamanho,
            'pasta' => $pasta,
            'hash_arquivo' => $hash,
            'alt_text' => $options['alt_text'] ?? null,
            'descricao' => $options['descricao'] ?? null,
            'tags' => $options['tags'] ?? null,
            'status' => 'ativo',
            'downloadable' => $options['downloadable'] ?? false,
        ];

        if (str_starts_with((string) $mimeType, 'image/') && strtolower($extensao) !== 'svg') {
            $dimensions = @getimagesize($file->getRealPath());

            if ($dimensions) {
                $data['dimensoes'] = [
                    'width' => $dimensions[0],
                    'height' => $dimensions[1],
                ];
            }
        }

        $media = Media::create($data);

        if (str_starts_with((string) $mimeType, 'image/') && in_array(strtolower($extensao), ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $this->generateThumbnail($media);
        }

        return $media;
    }

    public function uploadMulti(array $files, string $pasta): array
    {
        $results = [];

        foreach ($files as $file) {
            try {
                $results[] = $this->upload($file, $pasta);
            } catch (\Throwable $e) {
                Log::warning('Falha no upload de arquivo: ' . $e->getMessage());
                $results[] = ['error' => $e->getMessage(), 'file' => $file->getClientOriginalName()];
            }
        }

        return $results;
    }

    public function delete(int $mediaId): bool
    {
        $media = Media::findOrFail($mediaId);

        if (Storage::disk('public')->exists($media->caminho)) {
            Storage::disk('public')->delete($media->caminho);
        }

        $thumbnailPath = 'thumbnails/' . $media->caminho;

        if (Storage::disk('public')->exists($thumbnailPath)) {
            Storage::disk('public')->delete($thumbnailPath);
        }

        return (bool) $media->delete();
    }

    public function replace(int $mediaId, UploadedFile $file): Media
    {
        $this->validateFile($file);

        $media = Media::findOrFail($mediaId);
        $hash = md5_file($file->getRealPath());
        $existingMedia = Media::where('hash_arquivo', $hash)->where('id', '!=', $mediaId)->first();

        if ($existingMedia) {
            return $existingMedia;
        }

        if (Storage::disk('public')->exists($media->caminho)) {
            Storage::disk('public')->delete($media->caminho);
        }

        $extensao = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $tamanho = $file->getSize();
        $nomeArquivo = $media->nome . '-' . Str::random(8) . '.' . $extensao;
        $caminho = $file->storeAs(dirname($media->caminho), $nomeArquivo, 'public');

        $media->update([
            'caminho' => $caminho,
            'url' => Storage::url($caminho),
            'mime_type' => $mimeType,
            'extensao' => $extensao,
            'tamanho' => $tamanho,
            'hash_arquivo' => $hash,
        ]);

        return $media->fresh();
    }

    public function getUploadLimits(): array
    {
        $postMaxSize = $this->parseSize(ini_get('post_max_size'));
        $uploadMaxSize = $this->parseSize(ini_get('upload_max_filesize'));
        $maxFileUploads = (int) ini_get('max_file_uploads');

        return [
            'post_max_size' => $postMaxSize,
            'upload_max_filesize' => $uploadMaxSize,
            'max_file_uploads' => $maxFileUploads,
            'post_max_size_formatted' => $this->formatBytes($postMaxSize),
            'upload_max_filesize_formatted' => $this->formatBytes($uploadMaxSize),
            'config_max_size' => config('sistema.upload_max_size', 10) * 1024 * 1024,
            'allowed_extensions' => config('sistema.allowed_extensions', []),
            'allowed_mimes' => config('sistema.allowed_mimes', []),
        ];
    }

    public function validateFile(UploadedFile $file): bool
    {
        $maxSize = $this->getUploadLimits();
        $configMax = $maxSize['config_max_size'];
        $serverMax = $maxSize['upload_max_filesize'];
        $effectiveMax = min($configMax, $serverMax);

        if ($file->getSize() > $effectiveMax) {
            throw new \RuntimeException('O arquivo excede o tamanho máximo permitido de ' . $this->formatBytes($effectiveMax) . '.');
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = config('sistema.allowed_extensions', []);

        if (in_array($extension, self::UNSAFE_EXTENSIONS, true)) {
            throw new \RuntimeException("Extensão .{$extension} não permitida por segurança.");
        }

        if (!empty($allowedExtensions) && !in_array($extension, $allowedExtensions, true)) {
            throw new \RuntimeException("Extensão .{$extension} não permitida.");
        }

        $mime = $file->getMimeType();
        $allowedMimes = config('sistema.allowed_mimes', []);

        if (!empty($allowedMimes) && !in_array($mime, $allowedMimes, true)) {
            throw new \RuntimeException("Tipo de arquivo {$mime} não permitido.");
        }

        return true;
    }

    public function generateThumbnail(Media $media): ?string
    {
        $extensao = $media->extensao;
        $caminho = Storage::disk('public')->path($media->caminho);

        if (!file_exists($caminho)) {
            return null;
        }

        $thumbnailPath = 'thumbnails/' . $media->caminho;
        $thumbnailFullPath = Storage::disk('public')->path($thumbnailPath);
        $dir = dirname($thumbnailFullPath);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        try {
            $imageInfo = @getimagesize($caminho);

            if (!$imageInfo) {
                return null;
            }

            [$width, $height] = $imageInfo;
            $maxSize = 300;
            $ratio = min($maxSize / $width, $maxSize / $height, 1);
            $newWidth = (int) round($width * $ratio);
            $newHeight = (int) round($height * $ratio);

            $srcImage = match ($extensao) {
                'jpg', 'jpeg' => @imagecreatefromjpeg($caminho),
                'png' => @imagecreatefrompng($caminho),
                'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($caminho) : null,
                'gif' => @imagecreatefromgif($caminho),
                default => null,
            };

            if (!$srcImage) {
                return null;
            }

            $thumbImage = imagecreatetruecolor($newWidth, $newHeight);

            if ($extensao === 'png') {
                imagealphablending($thumbImage, false);
                imagesavealpha($thumbImage, true);
            }

            imagecopyresampled($thumbImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            match ($extensao) {
                'jpg', 'jpeg' => imagejpeg($thumbImage, $thumbnailFullPath, 80),
                'png' => imagepng($thumbImage, $thumbnailFullPath, 8),
                'webp' => function_exists('imagewebp') ? imagewebp($thumbImage, $thumbnailFullPath, 80) : null,
                'gif' => imagegif($thumbImage, $thumbnailFullPath),
                default => null,
            };

            imagedestroy($srcImage);
            imagedestroy($thumbImage);

            return Storage::url($thumbnailPath);
        } catch (\Throwable $e) {
            Log::warning('Falha ao gerar thumbnail: ' . $e->getMessage());

            return null;
        }
    }

    public function sanitizeFilename(string $name): string
    {
        $name = preg_replace('/[^\p{L}\p{N}\s\-_]/u', '', $name);
        $name = preg_replace('/[\s\-]+/', '-', $name);
        $name = trim((string) $name, '-');

        return Str::limit($name, 100, '');
    }

    public function organizeByDate(string $pasta): string
    {
        $pasta = trim($pasta);
        $pasta = str_replace('\\', '/', $pasta);
        $pasta = preg_replace('#/+#', '/', $pasta) ?? $pasta;
        $pasta = trim($pasta, '/.');
        $pasta = preg_replace('/[^A-Za-z0-9_\/-]/', '', $pasta) ?? '';

        if ($pasta === '') {
            $pasta = 'uploads';
        }

        return $pasta . '/' . now()->format('Y/m');
    }

    protected function resolveTipo(string $mimeType): string
    {
        return match (true) {
            str_starts_with($mimeType, 'image/') => 'imagem',
            str_starts_with($mimeType, 'video/') => 'video',
            str_starts_with($mimeType, 'audio/') => 'audio',
            str_contains($mimeType, 'pdf'),
            str_contains($mimeType, 'document'),
            str_contains($mimeType, 'spreadsheet'),
            str_contains($mimeType, 'csv') => 'documento',
            default => 'outro',
        };
    }

    protected function parseSize(?string $size): int
    {
        if ($size === null || $size === '') {
            return 0;
        }

        $size = trim($size);
        $unit = strtolower(substr($size, -1));
        $value = (int) substr($size, 0, -1);

        return match ($unit) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => (int) $size,
        };
    }

    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        if ($bytes === 0) {
            return '0 B';
        }

        $i = (int) floor(log($bytes) / log(1024));

        return round($bytes / (1024 ** $i), 2) . ' ' . $units[$i];
    }
}
