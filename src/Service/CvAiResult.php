<?php

namespace App\Service;

final class CvAiResult
{
    public function __construct(
        public readonly string $improvedText,
        public readonly string $adviceText,
    ) {
    }
}

