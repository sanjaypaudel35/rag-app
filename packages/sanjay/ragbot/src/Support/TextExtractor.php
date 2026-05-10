<?php

namespace Sanjay\Ragbot\Support;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser;

class TextExtractor
{
    /**
     * Extracts text from a file based on its path and mime type.
     */
    public function extract(string $filePath, string $mimeType = ''): string
    {
        $disk = config('ragbot.storage.disk', 'local');

        if (! Storage::disk($disk)->exists($filePath)) {
            throw new Exception("File not found: {$filePath}");
        }

        $fullPath = Storage::disk($disk)->path($filePath);

        // Fallback to detection if mimeType is empty
        if (empty($mimeType)) {
            $mimeType = Storage::disk($disk)->mimeType($filePath) ?? '';
            Log::info("Mime type detection fallback for {$filePath}: {$mimeType}");
        }

        Log::info("Extracting text from {$filePath} with mime-type: {$mimeType}");

        return match ($mimeType) {
            'application/pdf' => $this->extractPdf($fullPath),
            'text/plain' => file_get_contents($fullPath) ?: '',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/msword' => $this->extractDocx($fullPath),
            default => $this->handleDefault($fullPath, $mimeType),
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
                $text = $pdf->getText();

                if (empty(trim($text))) {
                    Log::warning("PDF extraction returned empty text for: {$fullPath}. It might be scanned or empty.");
                }

                return $text;
            } catch (Exception $e) {
                Log::error('PDF parsing failed: '.$e->getMessage());

                return '';
            }
        }

        Log::error("smalot/pdfparser is not installed. Cannot extract text from PDF: {$fullPath}");

        return '';
    }

    /**
     * Extracts text from a DOCX/DOC file.
     */
    protected function extractDocx(string $fullPath): string
    {
        if (class_exists(IOFactory::class)) {
            try {
                $phpWord = IOFactory::load($fullPath);
                $text = '';
                foreach ($phpWord->getSections() as $section) {
                    foreach ($section->getElements() as $element) {
                        if (method_exists($element, 'getText')) {
                            $text .= $element->getText()."\n";
                        }
                    }
                }

                return $text;
            } catch (Exception $e) {
                Log::error('DOCX parsing failed: '.$e->getMessage());

                return '';
            }
        }

        Log::error("phpoffice/phpword is not installed. Cannot extract text from DOCX: {$fullPath}");

        return '';
    }

    /**
     * Handle default/fallback extraction.
     */
    protected function handleDefault(string $fullPath, string $mimeType): string
    {
        // Avoid reading binary files as text
        if (str_starts_with($mimeType, 'text/')) {
            return file_get_contents($fullPath) ?: '';
        }

        Log::warning("Unsupported mime-type for text extraction: {$mimeType}. File: {$fullPath}");

        return '';
    }
}
