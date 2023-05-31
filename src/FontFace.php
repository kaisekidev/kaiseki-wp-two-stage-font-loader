<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\TwoStageFontLoader;

use function implode;
use function in_array;

final class FontFace
{
    public function __construct(private readonly Font $font)
    {
    }

    public function getFontFaceString(): string
    {
        $fontSources = $this->getFontSources();
        if ($fontSources === '') {
            return '';
        }
        return \Safe\sprintf(
            '@font-face {font-family:"%s";src:%s;%s%sfont-display:swap;}',
            $this->font->name,
            $this->getFontSources(),
            $this->getWeight(),
            $this->getStyle()
        );
    }

    private function getWeight(): string
    {
        if (!in_array($this->font->weight, ['400', 'normal'], true)) {
            return 'font-weight:' . $this->font->weight . ';';
        }
        return '';
    }

    private function getStyle(): string
    {
        if ($this->font->style === '') {
            return '';
        }
        return \Safe\sprintf('font-style:%s;', $this->font->style);
    }

    private function getFontSources(): string
    {
        $fontSources = [];
        foreach ($this->font->files as $file) {
            $fontSources[] = $this->getFontSourceUrl($file);
        }
        return implode(',', $fontSources);
    }

    private function getFontSourceUrl(FontFile $file): string
    {
        return 'url("' . $file->url . '") format("' . $file->extension . '")';
    }
}
