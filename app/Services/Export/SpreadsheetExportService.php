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

namespace App\Services\Export;

use DateTimeInterface;
use Illuminate\Support\Str;

class SpreadsheetExportService
{
    public const CONTENT_TYPE = 'application/vnd.ms-excel; charset=UTF-8';

    /**
     * @param array<int, string> $headers
     * @param iterable<int, array<int, mixed>> $rows
     * @return array{path: string, filename: string, content_type: string}
     */
    public function excel(string $baseFilename, string $sheetName, array $headers, iterable $rows): array
    {
        $base = trim(Str::beforeLast($baseFilename, '.'));
        $base = preg_replace('/[^A-Za-z0-9_.-]/', '_', $base) ?: 'export';
        $filename = $base . '.xls';
        $path = storage_path("app/exports/{$filename}");
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $tmpRowsPath = $path . '.rows.tmp';
        $rowHandle = fopen($tmpRowsPath, 'w+b');

        if ($rowHandle === false) {
            throw new \RuntimeException('Nao foi possivel criar arquivo temporario de exportacao.');
        }

        $widths = array_map(fn (mixed $value): int => $this->displayLength($value), $headers);
        $this->writeRow($rowHandle, $headers, 'Header');

        foreach ($rows as $row) {
            $values = array_values($row);

            foreach ($values as $index => $value) {
                $widths[$index] = max($widths[$index] ?? 0, $this->displayLength($value));
            }

            $this->writeRow($rowHandle, $values);
        }

        fclose($rowHandle);

        $handle = fopen($path, 'w+b');

        if ($handle === false) {
            @unlink($tmpRowsPath);
            throw new \RuntimeException('Nao foi possivel criar arquivo de exportacao.');
        }

        fwrite($handle, $this->workbookHeader($sheetName));
        fwrite($handle, '<Table ss:ExpandedColumnCount="' . count($headers) . '">' . PHP_EOL);

        foreach ($widths as $width) {
            fwrite($handle, '<Column ss:AutoFitWidth="1" ss:Width="' . $this->columnWidth($width) . '"/>' . PHP_EOL);
        }

        $tmpHandle = fopen($tmpRowsPath, 'rb');
        if ($tmpHandle !== false) {
            stream_copy_to_stream($tmpHandle, $handle);
            fclose($tmpHandle);
        }

        fwrite($handle, '</Table>' . PHP_EOL);
        fwrite($handle, '</Worksheet>' . PHP_EOL);
        fwrite($handle, '</Workbook>' . PHP_EOL);
        fclose($handle);

        @unlink($tmpRowsPath);

        return [
            'path' => $path,
            'filename' => $filename,
            'content_type' => self::CONTENT_TYPE,
        ];
    }

    /**
     * @param resource $handle
     * @param array<int, mixed> $values
     */
    private function writeRow($handle, array $values, ?string $style = null): void
    {
        fwrite($handle, '<Row>' . PHP_EOL);

        foreach ($values as $value) {
            $styleAttr = $style ? ' ss:StyleID="' . $style . '"' : '';
            fwrite($handle, '<Cell' . $styleAttr . '><Data ss:Type="String">' . $this->escape($this->stringValue($value)) . '</Data></Cell>' . PHP_EOL);
        }

        fwrite($handle, '</Row>' . PHP_EOL);
    }

    private function workbookHeader(string $sheetName): string
    {
        $safeSheetName = mb_substr($this->stringValue($sheetName), 0, 31);

        return '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
            . '<?mso-application progid="Excel.Sheet"?>' . PHP_EOL
            . '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" '
            . 'xmlns:o="urn:schemas-microsoft-com:office:office" '
            . 'xmlns:x="urn:schemas-microsoft-com:office:excel" '
            . 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" '
            . 'xmlns:html="http://www.w3.org/TR/REC-html40">' . PHP_EOL
            . '<Styles>' . PHP_EOL
            . '<Style ss:ID="Default" ss:Name="Normal"><Alignment ss:Vertical="Center"/><Font ss:FontName="Calibri" ss:Size="11"/></Style>' . PHP_EOL
            . '<Style ss:ID="Header"><Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1"/><Interior ss:Color="#D9EAF7" ss:Pattern="Solid"/></Style>' . PHP_EOL
            . '</Styles>' . PHP_EOL
            . '<Worksheet ss:Name="' . $this->escape($safeSheetName) . '">' . PHP_EOL;
    }

    private function columnWidth(int $displayLength): int
    {
        return max(55, min(420, ($displayLength + 2) * 7));
    }

    private function displayLength(mixed $value): int
    {
        return mb_strlen($this->stringValue($value));
    }

    private function stringValue(mixed $value): string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('d/m/Y H:i');
        }

        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? 'Sim' : 'Não';
        }

        return $this->removeInvalidXmlChars((string) $value);
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }

    private function removeInvalidXmlChars(string $value): string
    {
        return preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}]/u', '', $value) ?? '';
    }
}
