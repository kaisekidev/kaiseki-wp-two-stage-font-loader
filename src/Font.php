<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\TwoStageFontLoader;

use Exception;

use function array_unique;

final class Font
{
    /**  @var array<FontFile> */
    public array $files;

    /**
     * @param string        $name   "font-family" name.
     * @param array<string> $urls   Array of font file URLs. Valid types are "woff2" and "woff".
     * @param string        $weight (Optional) "font-weight" property.
     * @param string        $style  (Optional) "font-style" property. Set to true for italic or pass property as string.
     */
    public function __construct(
        public readonly string $name,
        array $urls,
        public readonly string $weight = 'normal',
        public readonly string $style = ''
    ) {
        foreach (array_unique($urls) as $url) {
            $this->files[] = new FontFile($url);
        }
    }

    public function getPrioritizedFile(): FontFile
    {
        foreach (['woff2', 'woff'] as $extension) {
            $file = $this->getFileByExtension($extension);
            if ($file !== null) {
                return $file;
            }
        }
        throw new Exception(\Safe\sprintf('No prioritized file found for family "%s" not found.', $this->name));
    }

    private function getFileByExtension(string $extension): ?FontFile
    {
        $match = null;
        foreach ($this->files as $file) {
            if ($file->extension === $extension) {
                $match = $file;
                break;
            }
        }
        return $match;
    }
}
