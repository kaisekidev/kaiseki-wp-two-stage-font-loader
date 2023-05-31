<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\TwoStageFontLoader;

final class Preloader
{
    public function __construct(private readonly Font $font)
    {
    }

    public function getPreloaderString(): string
    {
        $file = $this->font->getPrioritizedFile();
        return '<link rel="preload" href="' . $file->url . '" as="font" type="font/' .
            $file->extension . '" crossorigin>';
    }
}
