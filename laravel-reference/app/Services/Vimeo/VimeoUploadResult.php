<?php

namespace App\Services\Vimeo;

class VimeoUploadResult
{
    public function __construct(
        public readonly string $uri,
        public readonly string $publicUrl
    ) {}
}
