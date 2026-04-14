<?php

namespace App\Service;

use Smalot\PdfParser\Parser;

final class PdfTextExtractor
{
    public function __construct(
        private readonly Parser $parser = new Parser(),
    ) {
    }

    public function extractText(string $pdfAbsolutePath): string
    {
        try {
            $pdf = $this->parser->parseFile($pdfAbsolutePath);
            $text = (string) $pdf->getText();
        } catch (\Throwable) {
            return '';
        }

        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace("/[ \t]+/", ' ', $text) ?? $text;
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;

        return trim($text);
    }
}

