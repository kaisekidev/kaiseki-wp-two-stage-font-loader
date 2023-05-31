<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\TwoStageFontLoader;

use Kaiseki\WordPress\TwoStageFontLoader\Exception\InvalidResourceException;

use function in_array;
use function is_string;
use function pathinfo;

use const PATHINFO_BASENAME;
use const PATHINFO_EXTENSION;
use const PHP_URL_PATH;

final class FontFile
{
    public string $baseName;
    public string $extension;
    public string $path;

    public function __construct(
        public readonly string $url
    ) {
        $this->path = $this->getUrlPath();
        $this->baseName = $this->getBaseName();
        $this->extension = $this->getExtension();
    }

    private function getBaseName(): string
    {
        return pathinfo($this->path, PATHINFO_BASENAME);
    }

    private function getExtension(): string
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    private function getUrlPath(): string
    {
        $urlPath = \Safe\parse_url($this->url, PHP_URL_PATH);
        if (is_string($urlPath) && $urlPath !== '') {
            return $urlPath;
        }
        return '';
    }

    public function hasValidExtension(): bool
    {
        if (!in_array($this->extension, ['woff2', 'woff'], true)) {
            throw new InvalidResourceException(
                \Safe\sprintf(
                    'The given font file %s has an invalid type. Use woff2 or woff instead.',
                    $this->baseName
                )
            );
        }
        return true;
    }
}
