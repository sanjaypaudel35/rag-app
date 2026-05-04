<?php

namespace Sanjay\Ragbot\Support;

use Exception;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class TextExtractor
{
    /**
     * Extracts text from a file based on its path and mime type.
     */
    public function extract(string $filePath, string $mimeType): string
    {
        $disk = config('ragbot.storage.disk', 'local');

        if (! Storage::disk($disk)->exists($filePath)) {
            throw new Exception("File not found: {$filePath}");
        }

        $fullPath = Storage::disk($disk)->path($filePath);

        return match ($mimeType) {
            'application/pdf' => $this->extractPdf($fullPath),
            'text/plain' => file_get_contents($fullPath) ?: '',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/msword' => 'DOCX extraction not supported yet',
            default => file_get_contents($fullPath) ?: '',
        };
    }

    /**
     * Extracts text from a PDF file.
     */
    protected function extractPdf(string $fullPath): string
    {
        if (class_exists(Parser::class)) {
            try {
                $parser = new Parser;
                $pdf = $parser->parseFile($fullPath);

                return $pdf->getText();
            } catch (Exception $e) {
                // Fallback if parsing fails
                return $this->rawRead($fullPath);
            }
        }

        return $this->rawRead($fullPath);
    }

    /**
     * Raw read fallback for files.
     */
    protected function rawRead(string $fullPath): string
    {
        return file_get_contents($fullPath) ?: '';
    }
}
